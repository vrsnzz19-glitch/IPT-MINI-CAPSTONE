<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Plant;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PlantNurseryTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_shows_live_inventory_totals_and_recent_plants(): void
    {
        $category = Category::factory()->create();
        Plant::factory()->for($category)->create(['name' => 'Out of Stock Fern', 'stock' => 0]);
        Plant::factory()->for($category)->create(['name' => 'Low Stock Fern', 'stock' => 5]);
        Plant::factory()->for($category)->create(['name' => 'Healthy Fern', 'stock' => 8]);

        $response = $this->get(route('dashboard'));

        $response->assertOk()
            ->assertViewHas('totalPlants', 3)
            ->assertViewHas('totalStock', 13)
            ->assertViewHas('lowStockPlants', 2)
            ->assertViewHas('totalCategories', 1)
            ->assertViewHas('totalSuppliers', 0)
            ->assertViewHas('totalPurchases', 0)
            ->assertSee('Healthy Fern');
    }

    public function test_plant_can_be_created_updated_and_deleted(): void
    {
        $category = Category::factory()->create();
        $plantData = [
            'name' => 'Jade Plant',
            'category_id' => $category->id,
            'stock' => 7,
            'price' => '125.50',
        ];

        $this->post(route('plants.store'), $plantData)
            ->assertRedirect(route('plants.index'))
            ->assertSessionHas('success');

        $plant = Plant::query()->where('name', 'Jade Plant')->firstOrFail();
        $this->assertDatabaseHas('plants', [
            'id' => $plant->id,
            'category_id' => $category->id,
            'stock' => 7,
            'price' => '125.50',
        ]);

        $this->put(route('plants.update', $plant), [
            ...$plantData,
            'name' => 'Updated Jade Plant',
            'stock' => 12,
        ])->assertRedirect(route('plants.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('plants', [
            'id' => $plant->id,
            'name' => 'Updated Jade Plant',
            'stock' => 12,
        ]);

        $this->delete(route('plants.destroy', $plant))
            ->assertRedirect(route('plants.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('plants', ['id' => $plant->id]);
    }

    public function test_plant_form_rejects_missing_values_and_unknown_categories(): void
    {
        $this->from(route('plants.create'))
            ->post(route('plants.store'), [])
            ->assertRedirect(route('plants.create'))
            ->assertSessionHasErrors(['name', 'category_id', 'stock', 'price']);

        $this->from(route('plants.create'))
            ->post(route('plants.store'), [
                'name' => 'Invalid Plant',
                'category_id' => 999999,
                'stock' => -1,
                'price' => -10,
            ])
            ->assertRedirect(route('plants.create'))
            ->assertSessionHasErrors(['category_id', 'stock', 'price']);
    }

    public function test_plant_with_purchase_history_cannot_be_deleted(): void
    {
        $plant = Plant::factory()->create();
        PurchaseItem::factory()->for($plant)->create();

        $this->delete(route('plants.destroy', $plant))
            ->assertRedirect(route('plants.index'))
            ->assertSessionHas('error');

        $this->assertModelExists($plant);
    }

    public function test_plant_search_category_filter_and_pagination_work(): void
    {
        $flowers = Category::factory()->create(['name' => 'Flowers']);
        $herbs = Category::factory()->create(['name' => 'Herbs']);
        Plant::factory()->for($flowers)->create(['name' => 'Red Rose']);
        Plant::factory()->for($flowers)->create(['name' => 'White Rose']);
        Plant::factory()->for($herbs)->create(['name' => 'Rosemary']);

        $this->get(route('plants.index', ['search' => 'Rose', 'category_id' => $flowers->id]))
            ->assertOk()
            ->assertSee('Red Rose')
            ->assertSee('White Rose')
            ->assertDontSee('Rosemary');

        Plant::factory()->count(11)->for($herbs)->create();

        $this->get(route('plants.index'))
            ->assertOk()
            ->assertSee('Next', false);
    }

    public function test_category_can_be_created_updated_and_deleted_when_empty(): void
    {
        $this->post(route('categories.store'), [
            'name' => 'Indoor Plants',
        ])->assertRedirect(route('categories.index'))
            ->assertSessionHas('success');

        $category = Category::query()->where('name', 'Indoor Plants')->firstOrFail();
        $this->assertDatabaseHas('categories', ['id' => $category->id]);

        $this->put(route('categories.update', $category), [
            'name' => 'Indoor Greenery',
        ])->assertRedirect(route('categories.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Indoor Greenery',
        ]);

        $this->delete(route('categories.destroy', $category))
            ->assertRedirect(route('categories.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_deleting_category_cascades_to_plants_without_purchase_history(): void
    {
        $category = Category::factory()->create();
        $plant = Plant::factory()->for($category)->create();

        $this->delete(route('categories.destroy', $category))
            ->assertRedirect(route('categories.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
        $this->assertDatabaseMissing('plants', ['id' => $plant->id]);
    }

    public function test_category_with_purchase_history_cannot_be_deleted(): void
    {
        $category = Category::factory()->create();
        $plant = Plant::factory()->for($category)->create();
        PurchaseItem::factory()->for($plant)->create();

        $this->delete(route('categories.destroy', $category))
            ->assertRedirect(route('categories.index'))
            ->assertSessionHas('error');

        $this->assertModelExists($category);
        $this->assertModelExists($plant);
    }

    public function test_supplier_can_be_created_updated_and_deleted_without_purchase_history(): void
    {
        $supplierData = [
            'name' => 'Green Corner Nursery',
            'contact' => '0917-100-2000',
        ];

        $this->post(route('suppliers.store'), $supplierData)
            ->assertRedirect(route('suppliers.index'))
            ->assertSessionHas('success');

        $supplier = Supplier::query()->where('name', 'Green Corner Nursery')->firstOrFail();

        $this->put(route('suppliers.update', $supplier), [
            ...$supplierData,
            'name' => 'Green Corner Plant House',
        ])->assertRedirect(route('suppliers.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('suppliers', [
            'id' => $supplier->id,
            'name' => 'Green Corner Plant House',
        ]);

        $this->delete(route('suppliers.destroy', $supplier))
            ->assertRedirect(route('suppliers.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('suppliers', ['id' => $supplier->id]);
    }

    public function test_supplier_with_purchase_history_cannot_be_deleted(): void
    {
        $supplier = Supplier::factory()->create();
        Purchase::factory()->for($supplier)->create();

        $this->delete(route('suppliers.destroy', $supplier))
            ->assertRedirect(route('suppliers.index'))
            ->assertSessionHas('error');

        $this->assertModelExists($supplier);
    }

    public function test_pending_purchase_can_be_received_and_edits_adjust_inventory_and_totals(): void
    {
        $supplier = Supplier::factory()->create();
        $firstPlant = Plant::factory()->create(['stock' => 5]);
        $secondPlant = Plant::factory()->create(['stock' => 1]);
        $purchaseData = [
            'supplier_id' => $supplier->id,
            'purchase_date' => '2026-10-05',
            'status' => Purchase::STATUS_PENDING,
            'total_amount' => '0.01',
            'items' => [
                ['plant_id' => $firstPlant->id, 'quantity' => 2, 'unit_price' => '10.00'],
                ['plant_id' => $secondPlant->id, 'quantity' => 3, 'unit_price' => '2.25'],
            ],
        ];

        $this->post(route('purchases.store'), $purchaseData)
            ->assertRedirect(route('purchases.index'))
            ->assertSessionHas('success');

        $purchase = Purchase::query()->with('items')->firstOrFail();
        $this->assertDatabaseHas('purchases', [
            'id' => $purchase->id,
            'total_amount' => '26.75',
            'status' => Purchase::STATUS_PENDING,
        ]);
        $this->assertDatabaseHas('plants', ['id' => $firstPlant->id, 'stock' => 5]);
        $this->assertDatabaseHas('plants', ['id' => $secondPlant->id, 'stock' => 1]);

        $this->put(route('purchases.update', $purchase), [
            ...$purchaseData,
            'status' => Purchase::STATUS_RECEIVED,
        ])->assertRedirect(route('purchases.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('plants', ['id' => $firstPlant->id, 'stock' => 7]);
        $this->assertDatabaseHas('plants', ['id' => $secondPlant->id, 'stock' => 4]);

        $this->put(route('purchases.update', $purchase), [
            ...$purchaseData,
            'status' => Purchase::STATUS_RECEIVED,
            'items' => [
                ['plant_id' => $firstPlant->id, 'quantity' => 1, 'unit_price' => '12.50'],
            ],
        ])->assertRedirect(route('purchases.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('plants', ['id' => $firstPlant->id, 'stock' => 6]);
        $this->assertDatabaseHas('plants', ['id' => $secondPlant->id, 'stock' => 1]);
        $this->assertDatabaseHas('purchases', ['id' => $purchase->id, 'total_amount' => '12.50']);
        $this->assertDatabaseCount('purchase_items', 1);

        $this->put(route('purchases.update', $purchase), [
            ...$purchaseData,
            'status' => Purchase::STATUS_CANCELLED,
        ])->assertRedirect(route('purchases.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('plants', ['id' => $firstPlant->id, 'stock' => 5]);
        $this->assertDatabaseHas('purchases', ['id' => $purchase->id, 'status' => Purchase::STATUS_CANCELLED]);
    }

    public function test_deleting_a_received_purchase_removes_its_stock_and_items(): void
    {
        $plant = Plant::factory()->create(['stock' => 8]);
        $purchase = Purchase::factory()->create(['status' => Purchase::STATUS_RECEIVED]);
        PurchaseItem::factory()->for($purchase)->for($plant)->create([
            'quantity' => 3,
            'unit_price' => '5.00',
            'subtotal' => '15.00',
        ]);

        $this->delete(route('purchases.destroy', $purchase))
            ->assertRedirect(route('purchases.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('plants', ['id' => $plant->id, 'stock' => 5]);
        $this->assertDatabaseMissing('purchases', ['id' => $purchase->id]);
        $this->assertDatabaseCount('purchase_items', 0);
    }

    public function test_received_purchase_cannot_be_reversed_below_zero_stock(): void
    {
        $plant = Plant::factory()->create(['stock' => 2]);
        $purchase = Purchase::factory()->create(['status' => Purchase::STATUS_RECEIVED]);
        PurchaseItem::factory()->for($purchase)->for($plant)->create([
            'quantity' => 3,
            'unit_price' => '5.00',
            'subtotal' => '15.00',
        ]);

        $this->from(route('purchases.edit', $purchase))
            ->put(route('purchases.update', $purchase), [
                'supplier_id' => $purchase->supplier_id,
                'purchase_date' => $purchase->purchase_date->toDateString(),
                'status' => Purchase::STATUS_CANCELLED,
                'items' => [
                    ['plant_id' => $plant->id, 'quantity' => 3, 'unit_price' => '5.00'],
                ],
            ])
            ->assertRedirect(route('purchases.edit', $purchase))
            ->assertSessionHasErrors('status');

        $this->assertDatabaseHas('plants', ['id' => $plant->id, 'stock' => 2]);
        $this->assertDatabaseHas('purchases', ['id' => $purchase->id, 'status' => Purchase::STATUS_RECEIVED]);
    }

    public function test_purchase_filters_and_validation_work(): void
    {
        $supplier = Supplier::factory()->create(['name' => 'Sunrise Nursery']);
        $anotherSupplier = Supplier::factory()->create(['name' => 'Valley Nursery']);
        $receivedPurchase = Purchase::factory()->for($supplier)->create(['status' => Purchase::STATUS_RECEIVED]);
        $pendingPurchase = Purchase::factory()->for($anotherSupplier)->create(['status' => Purchase::STATUS_PENDING]);

        $this->get(route('purchases.index', [
            'supplier_id' => $supplier->id,
            'status' => Purchase::STATUS_RECEIVED,
        ]))
            ->assertOk()
            ->assertViewHas('purchases', fn ($purchases): bool => $purchases->count() === 1
                && $purchases->first()->is($receivedPurchase));

        $this->from(route('purchases.create'))
            ->post(route('purchases.store'), [])
            ->assertRedirect(route('purchases.create'))
            ->assertSessionHasErrors(['supplier_id', 'purchase_date', 'status', 'items']);

        $plant = Plant::factory()->create();
        $duplicateItems = [
            ['plant_id' => $plant->id, 'quantity' => 1, 'unit_price' => '1.00'],
            ['plant_id' => $plant->id, 'quantity' => 2, 'unit_price' => '1.00'],
        ];

        $this->from(route('purchases.create'))
            ->post(route('purchases.store'), [
                'supplier_id' => $supplier->id,
                'purchase_date' => '2026-10-05',
                'status' => Purchase::STATUS_PENDING,
                'items' => $duplicateItems,
            ])
            ->assertRedirect(route('purchases.create'))
            ->assertSessionHasErrors(['items.0.plant_id', 'items.1.plant_id']);

        $this->assertDatabaseCount('purchases', 2);
    }

    public function test_supplier_and_purchase_screens_render_for_new_and_existing_records(): void
    {
        $supplier = Supplier::factory()->create();
        $plant = Plant::factory()->create();
        $purchase = Purchase::factory()->for($supplier)->create();
        PurchaseItem::factory()->for($purchase)->for($plant)->create();

        $this->get(route('suppliers.index'))->assertOk()->assertSee($supplier->name);
        $this->get(route('suppliers.create'))->assertOk()->assertSee('Add a supplier');
        $this->get(route('suppliers.edit', $supplier))->assertOk()->assertSee($supplier->name);
        $this->get(route('purchases.index'))->assertOk()->assertSee("#{$purchase->id}");
        $this->get(route('purchases.create'))->assertOk()->assertSee('Record a purchase');
        $this->get(route('purchases.edit', $purchase))->assertOk()->assertSee("Edit purchase #{$purchase->id}");
    }

    public function test_demo_seeder_can_be_run_repeatedly_without_duplicate_records(): void
    {
        Artisan::call('db:seed', [
            '--class' => DatabaseSeeder::class,
            '--no-interaction' => true,
        ]);
        $this->assertDatabaseCount('purchases', 2);

        Artisan::call('db:seed', [
            '--class' => DatabaseSeeder::class,
            '--no-interaction' => true,
        ]);

        $this->assertDatabaseCount('categories', 5);
        $this->assertDatabaseCount('plants', 6);
        $this->assertDatabaseCount('suppliers', 2);
        $this->assertDatabaseCount('purchases', 2);
        $this->assertDatabaseCount('purchase_items', 4);
        $this->assertDatabaseHas('plants', ['name' => 'Peace Lily', 'stock' => 28]);
        $this->assertDatabaseHas('plants', ['name' => 'Tomato', 'stock' => 20]);
    }

    public function test_five_domain_migrations_create_the_exact_columns_and_order(): void
    {
        $expectedMigrations = [
            '2026_10_12_150001_create_categories_table',
            '2026_10_12_150002_create_suppliers_table',
            '2026_10_12_150003_create_plants_table',
            '2026_10_12_150004_create_purchases_table',
            '2026_10_12_150005_create_purchase_items_table',
        ];

        $this->assertSame(
            $expectedMigrations,
            DB::table('migrations')
                ->whereIn('migration', $expectedMigrations)
                ->orderBy('batch')
                ->orderBy('migration')
                ->pluck('migration')
                ->all(),
        );
        $this->assertSame(['id', 'name', 'created_at', 'updated_at'], Schema::getColumnListing('categories'));
        $this->assertSame(['id', 'name', 'contact', 'created_at', 'updated_at'], Schema::getColumnListing('suppliers'));
        $this->assertSame(['id', 'name', 'category_id', 'price', 'stock', 'created_at', 'updated_at'], Schema::getColumnListing('plants'));
        $this->assertSame(['id', 'supplier_id', 'purchase_date', 'total_amount', 'status', 'created_at', 'updated_at'], Schema::getColumnListing('purchases'));
        $this->assertSame(['id', 'purchase_id', 'plant_id', 'quantity', 'unit_price', 'subtotal', 'created_at', 'updated_at'], Schema::getColumnListing('purchase_items'));

        $supplier = Supplier::factory()->create();
        $purchase = Purchase::create([
            'supplier_id' => $supplier->id,
            'purchase_date' => '2026-10-05',
            'total_amount' => '10.00',
        ]);

        $purchase->refresh();
        $this->assertSame(Purchase::STATUS_PENDING, $purchase->status);
    }

    public function test_supplier_deletion_cascades_to_purchases_and_purchase_items(): void
    {
        $supplier = Supplier::factory()->create();
        $plant = Plant::factory()->create();
        $purchase = Purchase::factory()->for($supplier)->create();
        $purchaseItem = PurchaseItem::factory()->for($purchase)->for($plant)->create();

        $supplier->delete();

        $this->assertDatabaseMissing('purchases', ['id' => $purchase->id]);
        $this->assertDatabaseMissing('purchase_items', ['id' => $purchaseItem->id]);
        $this->assertModelExists($plant);
    }
}
