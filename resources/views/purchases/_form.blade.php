@php
    $purchaseItems = collect($purchaseItems);
    $itemRows = old('items');
    if ($itemRows === null) {
        $itemRows = $purchaseItems->isNotEmpty()
            ? $purchaseItems->map(fn ($item) => [
                'plant_id' => $item->plant_id,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
            ])->all()
            : [['plant_id' => '', 'quantity' => 1, 'unit_price' => '']];
    }
    $nextItemIndex = $itemRows === [] ? 0 : max(array_map('intval', array_keys($itemRows))) + 1;
@endphp

<div class="row g-3 mb-4">
    <div class="col-12 col-md-5">
        <label class="form-label fw-semibold" for="supplier_id">Supplier <span class="text-danger">*</span></label>
        <select class="form-select @error('supplier_id') is-invalid @enderror" id="supplier_id" name="supplier_id" required>
            <option value="">Choose a supplier</option>
            @foreach ($suppliers as $supplier)
                <option value="{{ $supplier->id }}" @selected((string) old('supplier_id', $purchase->supplier_id ?? '') === (string) $supplier->id)>{{ $supplier->name }}</option>
            @endforeach
        </select>
        @error('supplier_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        @if ($suppliers->isEmpty())
            <div class="form-text">Create a supplier before recording a purchase.</div>
        @endif
    </div>
    <div class="col-12 col-md-3">
        <label class="form-label fw-semibold" for="purchase_date">Purchase Date <span class="text-danger">*</span></label>
        <input class="form-control @error('purchase_date') is-invalid @enderror" id="purchase_date" name="purchase_date" type="date" value="{{ old('purchase_date', $purchase->purchase_date?->toDateString()) }}" required>
        @error('purchase_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12 col-md-4">
        <label class="form-label fw-semibold" for="status">Status <span class="text-danger">*</span></label>
        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
            @foreach (\App\Models\Purchase::STATUSES as $status)
                <option value="{{ $status }}" @selected(old('status', $purchase->status ?: \App\Models\Purchase::STATUS_PENDING) === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
    <div>
        <h2 class="h6 fw-bold mb-1">Purchase items</h2>
        <p class="small text-secondary mb-0">The total and each line subtotal are calculated from quantity and unit price.</p>
    </div>
    <button class="btn btn-sm btn-outline-success" type="button" id="addPurchaseItem"><i class="bi bi-plus-lg me-1"></i>Add plant</button>
</div>
@error('items')<div class="text-danger small mb-2">{{ $message }}</div>@enderror

<div id="purchaseItems" data-next-index="{{ $nextItemIndex }}">
    @foreach ($itemRows as $index => $item)
        <div class="purchase-item-row row g-2 align-items-end mb-3">
            <div class="col-12 col-md-5">
                <label class="form-label small fw-semibold" for="items_{{ $index }}_plant_id">Plant <span class="text-danger">*</span></label>
                <select class="form-select @error('items.'.$index.'.plant_id') is-invalid @enderror" id="items_{{ $index }}_plant_id" name="items[{{ $index }}][plant_id]" required>
                    <option value="">Choose a plant</option>
                    @foreach ($plants as $plant)
                        <option value="{{ $plant->id }}" @selected((string) ($item['plant_id'] ?? '') === (string) $plant->id)>{{ $plant->plant_name }}</option>
                    @endforeach
                </select>
                @error('items.'.$index.'.plant_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small fw-semibold" for="items_{{ $index }}_quantity">Quantity</label>
                <input class="form-control @error('items.'.$index.'.quantity') is-invalid @enderror" id="items_{{ $index }}_quantity" name="items[{{ $index }}][quantity]" type="number" min="1" max="1000000" step="1" value="{{ $item['quantity'] ?? 1 }}" required>
                @error('items.'.$index.'.quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label small fw-semibold" for="items_{{ $index }}_unit_price">Unit Price (₱)</label>
                <input class="form-control @error('items.'.$index.'.unit_price') is-invalid @enderror" id="items_{{ $index }}_unit_price" name="items[{{ $index }}][unit_price]" type="number" min="0" max="99999999.99" step="0.01" value="{{ $item['unit_price'] ?? '' }}" required>
                @error('items.'.$index.'.unit_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12 col-md-2">
                <button class="btn btn-outline-danger w-100 remove-purchase-item" type="button"><i class="bi bi-trash me-1"></i>Remove</button>
            </div>
        </div>
    @endforeach
</div>

<template id="purchaseItemTemplate">
    <div class="purchase-item-row row g-2 align-items-end mb-3">
        <div class="col-12 col-md-5">
            <label class="form-label small fw-semibold" for="items___INDEX___plant_id">Plant <span class="text-danger">*</span></label>
            <select class="form-select" id="items___INDEX___plant_id" name="items[__INDEX__][plant_id]" required>
                <option value="">Choose a plant</option>
                @foreach ($plants as $plant)
                    <option value="{{ $plant->id }}">{{ $plant->plant_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-md-2">
            <label class="form-label small fw-semibold" for="items___INDEX___quantity">Quantity</label>
            <input class="form-control" id="items___INDEX___quantity" name="items[__INDEX__][quantity]" type="number" min="1" max="1000000" step="1" value="1" required>
        </div>
        <div class="col-6 col-md-3">
            <label class="form-label small fw-semibold" for="items___INDEX___unit_price">Unit Price (₱)</label>
            <input class="form-control" id="items___INDEX___unit_price" name="items[__INDEX__][unit_price]" type="number" min="0" max="99999999.99" step="0.01" required>
        </div>
        <div class="col-12 col-md-2">
            <button class="btn btn-outline-danger w-100 remove-purchase-item" type="button"><i class="bi bi-trash me-1"></i>Remove</button>
        </div>
    </div>
</template>

<div class="alert alert-info small mt-3 mb-0">
    <i class="bi bi-info-circle me-1"></i>Received purchases immediately add their quantities to plant stock. Pending and cancelled purchases do not.
</div>

@pushOnce('scripts')
    <script>
        const purchaseItems = document.getElementById('purchaseItems');
        const purchaseTemplate = document.getElementById('purchaseItemTemplate');
        const addPurchaseItemButton = document.getElementById('addPurchaseItem');

        addPurchaseItemButton.addEventListener('click', () => {
            const index = Number(purchaseItems.dataset.nextIndex);
            purchaseItems.dataset.nextIndex = index + 1;
            purchaseItems.insertAdjacentHTML('beforeend', purchaseTemplate.innerHTML.replaceAll('__INDEX__', index));
        });

        purchaseItems.addEventListener('click', (event) => {
            const removeButton = event.target.closest('.remove-purchase-item');

            if (!removeButton) {
                return;
            }

            if (purchaseItems.querySelectorAll('.purchase-item-row').length === 1) {
                window.alert('A purchase must contain at least one plant.');
                return;
            }

            removeButton.closest('.purchase-item-row').remove();
        });
    </script>
@endPushOnce
