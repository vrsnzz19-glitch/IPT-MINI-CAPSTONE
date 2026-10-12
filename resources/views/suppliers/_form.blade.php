<div class="row g-3">
    <div class="col-12">
        <label class="form-label fw-semibold" for="name">Supplier Name <span class="text-danger">*</span></label>
        <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" type="text" value="{{ old('name', $supplier->name ?? '') }}" maxlength="255" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label fw-semibold" for="contact_number">Contact Number <span class="text-danger">*</span></label>
        <input class="form-control @error('contact_number') is-invalid @enderror" id="contact_number" name="contact_number" type="text" value="{{ old('contact_number', $supplier->contact_number ?? '') }}" maxlength="50" required>
        @error('contact_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12">
        <label class="form-label fw-semibold" for="address">Address</label>
        <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3" maxlength="2000">{{ old('address', $supplier->address ?? '') }}</textarea>
        @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>
