<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Plant;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PlantController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'category_id' => ['nullable', 'integer', Rule::exists('categories', 'id')],
        ]);

        $plants = Plant::query()
            ->with('category')
            ->when($filters['search'] ?? null, function ($query, string $search): void {
                $query->where('name', 'like', '%'.$search.'%');
            })
            ->when($filters['category_id'] ?? null, function ($query, int $categoryId): void {
                $query->where('category_id', $categoryId);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('plants.index', [
            'plants' => $plants,
            'categories' => Category::orderBy('name')->get(),
            'filters' => $filters,
        ]);
    }

    public function create(): View
    {
        return view('plants.create', [
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $plant = Plant::create($request->validate($this->rules()));

        return redirect()
            ->route('plants.index')
            ->with('success', "{$plant->name} was added to the inventory.");
    }

    public function edit(Plant $plant): View
    {
        return view('plants.edit', [
            'plant' => $plant,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Plant $plant): RedirectResponse
    {
        $plant->update($request->validate($this->rules()));

        return redirect()
            ->route('plants.index')
            ->with('success', "{$plant->name} was updated.");
    }

    public function destroy(Plant $plant): RedirectResponse
    {
        if ($plant->purchaseItems()->exists()) {
            return redirect()
                ->route('plants.index')
                ->with('error', 'This plant cannot be deleted while purchase records refer to it.');
        }

        $plantName = $plant->name;
        $plant->delete();

        return redirect()
            ->route('plants.index')
            ->with('success', "{$plantName} was removed from the inventory.");
    }

    /**
     * @return array<string, array<int, string>>
     */
    private function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'integer', Rule::exists('categories', 'id')],
            'stock' => ['required', 'integer', 'min:0'],
            'price' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
        ];
    }
}
