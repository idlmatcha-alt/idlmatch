<?php

namespace App\Http\Controllers;

use App\Http\Requests\SuccessStoryRequest;
use App\Models\SuccessStory;
use App\Services\ImageService;

class SuccessStoryController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified'])->only(['create', 'store']);
    }

    public function index()
    {
        $seed = trim((string) request('seed', now()->format('YmdH')));
        if ($seed === '') {
            $seed = now()->format('YmdH');
        }

        $stories = SuccessStory::query()
            ->where('status', 'approved')
            ->inRandomOrder($seed)
            ->paginate(12);
        $stories->appends(['seed' => $seed]);

        return view('success-stories.index', compact('stories'));
    }

    public function create()
    {
        return view('success-stories.create');
    }

    public function show(SuccessStory $successStory)
    {
        $isAdmin = auth()->check()
            && strtolower(trim((string) auth()->user()?->role)) === 'admin';

        if ($successStory->status !== 'approved' && !$isAdmin) {
            abort(404);
        }

        $relatedStories = SuccessStory::query()
            ->select(['id', 'couple_names', 'cover_image', 'created_at'])
            ->where('status', 'approved')
            ->whereKeyNot($successStory->id)
            ->inRandomOrder(now()->format('YmdH'))
            ->take(3)
            ->get();

        return view('success-stories.show', compact('successStory', 'relatedStories'));
    }

    public function store(SuccessStoryRequest $request, ImageService $imageService)
    {
        $story = SuccessStory::create([
            'submitted_by' => $request->user()?->id,
            'couple_names' => $request->input('couple_names'),
            'story' => $request->input('story'),
            'status' => 'pending',
        ]);

        if ($request->hasFile('cover_image')) {
            $story->cover_image = $imageService->optimize($request->file('cover_image'), 'success-stories', 1200);
            $story->save();
        }

        return redirect()->route('success-stories.index')->with('status', 'Story submitted for review.');
    }
}
