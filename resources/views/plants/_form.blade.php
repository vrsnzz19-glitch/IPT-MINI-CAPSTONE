<div class="row g-3">
    <div class="col-12 col-md-6">
        <label class="form-label fw-semibold" for="name">Plant Name <span class="text-danger">*</span></label>
        <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" type="text" value="{{ old('name', $plant->name ?? '') }}" maxlength="255" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label fw-semibold" for="category_id">Category <span class="text-danger">*</span></label>
        @php($selectedCategory = old('category_id', $plant->category_id ?? ''))
        <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
            <option value="">Choose a category</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected((string) $selectedCategory === (string) $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
        @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        @if ($categories->isEmpty())
            <div class="form-text">Create a category before adding a plant.</div>
        @endif
    </div>
    <div class="col-12 col-md-3">
        <label class="form-label fw-semibold" for="stock">Stock <span class="text-danger">*</span></label>
        <input class="form-control @error('stock') is-invalid @enderror" id="stock" name="stock" type="number" min="0" step="1" value="{{ old('stock', $plant->stock ?? 0) }}" required>
        @error('stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12 col-md-3">
        <label class="form-label fw-semibold" for="price">Price (₱) <span class="text-danger">*</span></label>
        <input class="form-control @error('price') is-invalid @enderror" id="price" name="price" type="number" min="0" step="0.01" value="{{ old('price', $plant->price ?? '') }}" required>
        @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>
