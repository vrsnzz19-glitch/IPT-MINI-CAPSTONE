<?php

namespace Database\Seeders;

use App\Models\Plant;
use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class BusinessRecordsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = [
            'Green Valley Plant Supply' => [
                'contact_number' => '0917-555-0182',
                'address' => '18 Garden Road, Quezon City',
            ],
            'Sunrise Horticulture' => [
                'contact_number' => '0928-555-0164',
                'address' => '42 Orchard Avenue, Laguna',
            ],
        ];

        $supplierRecords = [];

        foreach ($suppliers as $name => $details) {
            $supplierRecords[$name] = Supplier::firstOrCreate(
                ['name' => $name],
                $details,
            );
        }

        [$receivedPurchase, $receivedPurchaseCreated] = $this->findOrCreatePurchase(
            $supplierRecords['Green Valley Plant Supply'],
            '2026-10-01',
            Purchase::STATUS_RECEIVED,
            '3550.00',
        );

        if ($receivedPurchaseCreated) {
            $items = [
                ['plant' => 'Peace Lily', 'quantity' => 10, 'unit_price' => '245.00'],
                ['plant' => 'Tomato', 'quantity' => 20, 'unit_price' => '55.00'],
            ];

            foreach ($items as $item) {
                $plant = Plant::where('plant_name', $item['plant'])->firstOrFail();
                $receivedPurchase->items()->create([
                    'plant_id' => $plant->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => number_format($item['quantity'] * (float) $item['unit_price'], 2, '.', ''),
                ]);
                $plant->increment('quantity', $item['quantity']);
            }
        }

        [$pendingPurchase, $pendingPurchaseCreated] = $this->findOrCreatePurchase(
            $supplierRecords['Sunrise Horticulture'],
            '2026-10-10',
            Purchase::STATUS_PENDING,
            '1420.00',
        );

        if ($pendingPurchaseCreated) {
            foreach ([
                ['plant' => 'Calamansi', 'quantity' => 2, 'unit_price' => '390.00'],
                ['plant' => 'Aloe Vera', 'quantity' => 4, 'unit_price' => '160.00'],
            ] as $item) {
                $plant = Plant::where('plant_name', $item['plant'])->firstOrFail();
                $pendingPurchase->items()->create([
                    'plant_id' => $plant->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => number_format($item['quantity'] * (float) $item['unit_price'], 2, '.', ''),
                ]);
            }
        }
    }

    /**
     * @return array{Purchase, bool}
     */
    private function findOrCreatePurchase(Supplier $supplier, string $date, string $status, string $total): array
    {
        $purchase = Purchase::query()
            ->where('supplier_id', $supplier->id)
            ->whereDate('purchase_date', $date)
            ->first();

        if ($purchase !== null) {
            return [$purchase, false];
        }

        return [
            Purchase::create([
                'supplier_id' => $supplier->id,
                'purchase_date' => $date,
                'status' => $status,
                'total_amount' => $total,
            ]),
            true,
        ];
    }
}
