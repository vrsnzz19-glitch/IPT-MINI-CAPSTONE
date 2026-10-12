<div class="row g-3">
    <div class="col-12 col-md-6">
        <label class="form-label fw-semibold" for="plant_name">Plant Name <span class="text-danger">*</span></label>
        <input class="form-control @error('plant_name') is-invalid @enderror" id="plant_name" name="plant_name" type="text" value="{{ old('plant_name', $plant->plant_name ?? '') }}" maxlength="255" required>
        @error('plant_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label fw-semibold" for="scientific_name">Scientific Name</label>
        <input class="form-control @error('scientific_name') is-invalid @enderror" id="scientific_name" name="scientific_name" type="text" value="{{ old('scientific_name', $plant->scientific_name ?? '') }}" maxlength="255">
        @error('scientific_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
        <label class="form-label fw-semibold" for="quantity">Quantity <span class="text-danger">*</span></label>
        <input class="form-control @error('quantity') is-invalid @enderror" id="quantity" name="quantity" type="number" min="0" step="1" value="{{ old('quantity', $plant->quantity ?? 0) }}" required>
        @error('quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12 col-md-3">
        <label class="form-label fw-semibold" for="price">Price (₱) <span class="text-danger">*</span></label>
        <input class="form-control @error('price') is-invalid @enderror" id="price" name="price" type="number" min="0" step="0.01" value="{{ old('price', $plant->price ?? '') }}" required>
        @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12">
        <label class="form-label fw-semibold" for="description">Description</label>
        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4" maxlength="5000" placeholder="Add helpful details about this plant.">{{ old('description', $plant->description ?? '') }}</textarea>
        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>
