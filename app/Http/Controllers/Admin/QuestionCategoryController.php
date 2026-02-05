<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class QuestionCategoryController extends Controller
{
    public function index()
    {
        $mode = 'list';

        $categories = Category::with('parent')
            ->orderBy('level')
            ->orderBy('sort_order')
            ->get();

        return view('admin.question_categories.index', compact('mode','categories'));
    }

    public function create()
    {
        $mode = 'create';

        $categories = Category::active()
            ->orderBy('level')
            ->orderBy('sort_order')
            ->get();

        return view('admin.question_categories.index', compact('mode','categories'));
    }

    public function edit(Category $category)
    {
        $mode = 'edit';

        $categories = Category::where('id','!=',$category->id)
            ->orderBy('level')
            ->orderBy('sort_order')
            ->get();

        return view(
            'admin.question_categories.index',
            compact('mode','category','categories')
        );
    }

    public function store(Request $request)
    {
        $this->saveCategory(new Category(), $request);

        return redirect()
            ->route('admin.categories.index')
            ->with('success','Category created');
    }

    public function update(Request $request, Category $category)
    {
        $this->saveCategory($category, $request);

        return redirect()
            ->route('admin.categories.index')
            ->with('success','Category updated');
    }

    private function saveCategory(Category $category, Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        $level = 0;
        if ($request->parent_id) {
            $parent = Category::find($request->parent_id);
            $level = $parent->level + 1;
        }

        $category->fill([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'parent_id' => $request->parent_id,
            'level' => $level,
            'seo_title' => $request->seo_title,
            'seo_description' => $request->seo_description,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->boolean('is_active'),
        ])->save();
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return back()->with('success','Category deleted');
    }
}
