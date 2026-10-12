<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class CategoryController extends Controller
{
    public function index(): View
    {
        return view('categories.index', [
            'categories' => Category::withCount('plants')
                ->orderBy('name')
                ->paginate(10),
        ]);
    }

    public function create(): View
    {
        return view('categories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $category = Category::create($request->validate($this->rules()));

        return redirect()
            ->route('categories.index')
            ->with('success', "{$category->name} was created.");
    }

    public function edit(Category $category): View
    {
        return view('categories.edit', [
            'category' => $category,
        ]);
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $category->update($request->validate($this->rules($category)));

        return redirect()
            ->route('categories.index')
            ->with('success', "{$category->name} was updated.");
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->plants()->whereHas('purchaseItems')->exists()) {
            return redirect()
                ->route('categories.index')
                ->with('error', 'This category cannot be deleted while its plants have purchase history.');
        }

        $categoryName = $category->name;
        $category->delete();

        return redirect()
            ->route('categories.index')
            ->with('success', "{$categoryName} was deleted.");
    }

    /**
     * @return array<string, array<int, Unique|string>>
     */
    private function rules(?Category $category = null): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'name')->ignore($category),
            ],
        ];
    }
}
