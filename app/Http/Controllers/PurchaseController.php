<?php

namespace App\Http\Controllers;

use App\Models\Plant;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PurchaseController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'status' => ['nullable', Rule::in(Purchase::STATUSES)],
            'supplier_id' => ['nullable', 'integer', Rule::exists('suppliers', 'id')],
        ]);

        return view('purchases.index', [
            'purchases' => Purchase::query()
                ->with(['supplier', 'items.plant'])
                ->when($filters['status'] ?? null, function ($query, string $status): void {
                    $query->where('status', $status);
                })
                ->when($filters['supplier_id'] ?? null, function ($query, int $supplierId): void {
                    $query->where('supplier_id', $supplierId);
                })
                ->latest('purchase_date')
                ->latest('id')
                ->paginate(10)
                ->withQueryString(),
            'suppliers' => Supplier::orderBy('name')->get(),
            'filters' => $filters,
        ]);
    }

    public function create(): View
    {
        return view('purchases.create', [
            'purchase' => new Purchase(['purchase_date' => now()->toDateString()]),
            'suppliers' => Supplier::orderBy('name')->get(),
            'plants' => Plant::orderBy('plant_name')->get(),
            'purchaseItems' => [],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->rules());
        $items = $this->prepareItems($data['items']);

        $purchase = DB::transaction(function () use ($data, $items): Purchase {
            $purchase = Purchase::create([
                'supplier_id' => $data['supplier_id'],
                'purchase_date' => $data['purchase_date'],
                'total_amount' => $this->totalFor($items),
                'status' => $data['status'],
            ]);

            $this->saveItems($purchase, $items);
            $this->adjustReceivedStock([], Purchase::STATUS_PENDING, $items, $purchase->status);

            return $purchase;
        });

        return redirect()
            ->route('purchases.index')
            ->with('success', "Purchase #{$purchase->id} was recorded.");
    }

    public function edit(Purchase $purchase): View
    {
        return view('purchases.edit', [
            'purchase' => $purchase->load('items.plant'),
            'suppliers' => Supplier::orderBy('name')->get(),
            'plants' => Plant::orderBy('plant_name')->get(),
            'purchaseItems' => $purchase->items,
        ]);
    }

    public function update(Request $request, Purchase $purchase): RedirectResponse
    {
        $data = $request->validate($this->rules());
        $items = $this->prepareItems($data['items']);

        DB::transaction(function () use ($data, $items, $purchase): void {
            $lockedPurchase = Purchase::query()
                ->lockForUpdate()
                ->findOrFail($purchase->id);
            $oldItems = $lockedPurchase->items()
                ->get(['plant_id', 'quantity'])
                ->all();

            $this->adjustReceivedStock($oldItems, $lockedPurchase->status, $items, $data['status']);

            $lockedPurchase->update([
                'supplier_id' => $data['supplier_id'],
                'purchase_date' => $data['purchase_date'],
                'total_amount' => $this->totalFor($items),
                'status' => $data['status'],
            ]);

            $lockedPurchase->items()->delete();
            $this->saveItems($lockedPurchase, $items);
        });

        return redirect()
            ->route('purchases.index')
            ->with('success', "Purchase #{$purchase->id} was updated.");
    }

    public function destroy(Purchase $purchase): RedirectResponse
    {
        DB::transaction(function () use ($purchase): void {
            $lockedPurchase = Purchase::query()
                ->lockForUpdate()
                ->findOrFail($purchase->id);

            $this->adjustReceivedStock(
                $lockedPurchase->items()->get(['plant_id', 'quantity'])->all(),
                $lockedPurchase->status,
                [],
                Purchase::STATUS_CANCELLED,
            );

            $lockedPurchase->delete();
        });

        return redirect()
            ->route('purchases.index')
            ->with('success', "Purchase #{$purchase->id} was deleted.");
    }

    /**
     * @return array<string, array<int, string|\Illuminate\Validation\Rules\In|Rule>>
     */
    private function rules(): array
    {
        return [
            'supplier_id' => ['required', 'integer', Rule::exists('suppliers', 'id')],
            'purchase_date' => ['required', 'date'],
            'status' => ['required', Rule::in(Purchase::STATUSES)],
            'items' => ['required', 'array', 'min:1'],
            'items.*.plant_id' => ['required', 'integer', 'distinct', Rule::exists('plants', 'id')],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:1000000'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
        ];
    }

    /**
     * @param  array<int, array{plant_id: int, quantity: int, unit_price: numeric-string|int|float}>  $items
     * @return array<int, array{plant_id: int, quantity: int, unit_price: string, subtotal: string}>
     */
    private function prepareItems(array $items): array
    {
        return array_map(function (array $item): array {
            $unitPrice = number_format((float) $item['unit_price'], 2, '.', '');
            $subtotal = number_format((int) $item['quantity'] * (float) $unitPrice, 2, '.', '');

            return [
                'plant_id' => (int) $item['plant_id'],
                'quantity' => (int) $item['quantity'],
                'unit_price' => $unitPrice,
                'subtotal' => $subtotal,
            ];
        }, $items);
    }

    /**
     * @param  array<int, array{plant_id: int, quantity: int, unit_price: string, subtotal: string}>  $items
     */
    private function totalFor(array $items): string
    {
        return number_format(array_sum(array_column($items, 'subtotal')), 2, '.', '');
    }

    /**
     * @param  array<int, array{plant_id: int, quantity: int, unit_price: string, subtotal: string}>  $items
     */
    private function saveItems(Purchase $purchase, array $items): void
    {
        foreach ($items as $item) {
            $purchase->items()->create($item);
        }
    }

    /**
     * Keep received purchase quantities in sync when the receipt or its line items change.
     *
     * @param  array<int, PurchaseItem|array{plant_id: int, quantity: int}>  $oldItems
     * @param  array<int, array{plant_id: int, quantity: int, unit_price: string, subtotal: string}>  $newItems
     */
    private function adjustReceivedStock(array $oldItems, string $oldStatus, array $newItems, string $newStatus): void
    {
        $quantityChanges = [];

        if ($oldStatus === Purchase::STATUS_RECEIVED) {
            foreach ($oldItems as $item) {
                $quantityChanges[$item['plant_id'] ?? $item->plant_id] = -($item['quantity'] ?? $item->quantity);
            }
        }

        if ($newStatus === Purchase::STATUS_RECEIVED) {
            foreach ($newItems as $item) {
                $plantId = $item['plant_id'];
                $quantityChanges[$plantId] = ($quantityChanges[$plantId] ?? 0) + $item['quantity'];
            }
        }

        ksort($quantityChanges);

        foreach ($quantityChanges as $plantId => $change) {
            if ($change === 0) {
                continue;
            }

            $plant = Plant::query()
                ->lockForUpdate()
                ->findOrFail($plantId);

            if ($change < 0 && $plant->quantity < abs($change)) {
                throw ValidationException::withMessages([
                    'status' => "The received quantity for {$plant->plant_name} can no longer be reversed because current stock is lower than the purchase quantity.",
                ]);
            }

            $change > 0
                ? $plant->increment('quantity', $change)
                : $plant->decrement('quantity', abs($change));
        }
    }
}
