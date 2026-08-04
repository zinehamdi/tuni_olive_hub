<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroSlide;
use App\Services\ImageOptimizationService;
use Illuminate\Http\Request;

class HeroSlideController extends Controller
{
    protected $imageService;

    public function __construct(ImageOptimizationService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function index()
    {
        $slides = HeroSlide::orderBy('order', 'asc')->orderBy('created_at', 'desc')->get();
        return view('admin.hero_slides.index', compact('slides'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|file|mimes:jpeg,jpg,png,gif,webp|max:10240',
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $imagePath = $this->imageService->optimizeHeroSlide($request->file('image'));

        HeroSlide::create([
            'image_path' => $imagePath,
            'title' => $request->input('title'),
            'subtitle' => $request->input('subtitle'),
            'order' => $request->input('order', 0),
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.hero-slides.index')->with('success', __('Hero slide uploaded successfully.'));
    }

    public function update(Request $request, HeroSlide $heroSlide)
    {
        $request->validate([
            'image' => 'nullable|file|mimes:jpeg,jpg,png,gif,webp|max:10240',
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'order' => 'nullable|integer',
        ]);

        $data = [
            'title' => $request->input('title'),
            'subtitle' => $request->input('subtitle'),
            'order' => $request->input('order', 0),
            'is_active' => $request->has('is_active'),
        ];

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $this->imageService->deleteImage($heroSlide->image_path);
            $data['image_path'] = $this->imageService->optimizeHeroSlide($request->file('image'));
        }

        $heroSlide->update($data);

        return redirect()->route('admin.hero-slides.index')->with('success', __('Hero slide updated successfully.'));
    }

    public function destroy(HeroSlide $heroSlide)
    {
        $this->imageService->deleteImage($heroSlide->image_path);
        $heroSlide->delete();

        return redirect()->route('admin.hero-slides.index')->with('success', __('Hero slide deleted successfully.'));
    }
}
