<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SupplierController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
        ]);

        return view('suppliers.index', [
            'suppliers' => Supplier::query()
                ->withCount('purchases')
                ->when($filters['search'] ?? null, function ($query, string $search): void {
                    $query->where('name', 'like', '%'.$search.'%');
                })
                ->orderBy('name')
                ->paginate(10)
                ->withQueryString(),
            'filters' => $filters,
        ]);
    }

    public function create(): View
    {
        return view('suppliers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $supplier = Supplier::create($request->validate($this->rules()));

        return redirect()
            ->route('suppliers.index')
            ->with('success', "{$supplier->name} was added.");
    }

    public function edit(Supplier $supplier): View
    {
        return view('suppliers.edit', [
            'supplier' => $supplier,
        ]);
    }

    public function update(Request $request, Supplier $supplier): RedirectResponse
    {
        $supplier->update($request->validate($this->rules($supplier)));

        return redirect()
            ->route('suppliers.index')
            ->with('success', "{$supplier->name} was updated.");
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        if ($supplier->purchases()->exists()) {
            return redirect()
                ->route('suppliers.index')
                ->with('error', 'This supplier cannot be deleted while purchase records are linked to it.');
        }

        $supplierName = $supplier->name;
        $supplier->delete();

        return redirect()
            ->route('suppliers.index')
            ->with('success', "{$supplierName} was deleted.");
    }

    /**
     * @return array<string, array<int, \Illuminate\Validation\Rules\Unique|string>>
     */
    private function rules(?Supplier $supplier = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('suppliers', 'name')->ignore($supplier)],
            'contact_number' => ['required', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
