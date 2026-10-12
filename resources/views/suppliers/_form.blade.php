<div class="row g-3">
    <div class="col-12">
        <label class="form-label fw-semibold" for="name">Supplier Name <span class="text-danger">*</span></label>
        <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" type="text" value="{{ old('name', $supplier->name ?? '') }}" maxlength="255" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label fw-semibold" for="contact">Contact Number</label>
        <input class="form-control @error('contact') is-invalid @enderror" id="contact" name="contact" type="text" value="{{ old('contact', $supplier->contact ?? '') }}" maxlength="50">
        @error('contact')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>
