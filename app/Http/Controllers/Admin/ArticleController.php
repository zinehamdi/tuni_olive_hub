<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Services\ImageOptimizationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
    protected $imageService;

    public function __construct(ImageOptimizationService $imageService)
    {
        $this->imageService = $imageService;
    }
    public function index()
    {
        $articles = Article::latest()->paginate(10);
        return view('admin.articles.index', compact('articles'));
    }

    public function create()
    {
        return view('admin.articles.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|array',
            'title.ar' => 'required|string',
            'title.en' => 'required|string',
            'title.fr' => 'required|string',
            'category' => 'required|array',
            'category.ar' => 'required|string',
            'category.en' => 'required|string',
            'category.fr' => 'required|string',
            'content' => 'required|array',
            'content.ar' => 'required|string',
            'content.en' => 'required|string',
            'content.fr' => 'required|string',
            'image' => 'nullable|file|mimes:jpeg,jpg,png,gif,webp|max:20480',
            'is_active' => 'boolean'
        ]);

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $data['image'] = 'storage/' . $this->imageService->optimizeArticleImage($request->file('image'));
        }

        $data['is_active'] = $request->has('is_active');

        Article::create($data);

        // Clear home page articles cache
        \Illuminate\Support\Facades\Cache::forget('home_articles');

        return redirect()->route('admin.articles.index')->with('success', __('Article created successfully.'));
    }

    public function edit(Article $article)
    {
        return view('admin.articles.edit', compact('article'));
    }

    public function update(Request $request, Article $article)
    {
        $data = $request->validate([
            'title' => 'required|array',
            'title.ar' => 'required|string',
            'title.en' => 'required|string',
            'title.fr' => 'required|string',
            'category' => 'required|array',
            'category.ar' => 'required|string',
            'category.en' => 'required|string',
            'category.fr' => 'required|string',
            'content' => 'required|array',
            'content.ar' => 'required|string',
            'content.en' => 'required|string',
            'content.fr' => 'required|string',
            'image' => 'nullable|file|mimes:jpeg,jpg,png,gif,webp|max:20480',
            'is_active' => 'boolean'
        ]);

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            // Delete old uploaded image if it was stored in storage/
            if ($article->image && \Str::startsWith($article->image, 'storage/')) {
                Storage::disk('public')->delete(str_replace('storage/', '', $article->image));
            }
            $data['image'] = 'storage/' . $this->imageService->optimizeArticleImage($request->file('image'));
        }

        $data['is_active'] = $request->has('is_active');

        $article->update($data);

        // Clear home page articles cache
        \Illuminate\Support\Facades\Cache::forget('home_articles');

        return redirect()->route('admin.articles.index')->with('success', __('Article updated successfully.'));
    }

    public function destroy(Article $article)
    {
        if ($article->image) {
            $path = str_replace('storage/', '', $article->image);
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }
        $article->delete();

        // Clear home page articles cache
        \Illuminate\Support\Facades\Cache::forget('home_articles');

        return redirect()->route('admin.articles.index')->with('success', __('Article deleted successfully.'));
    }
}
