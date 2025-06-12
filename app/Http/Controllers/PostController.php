<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class PostController extends Controller
{

    public function index()
    {
        $user = auth()->user();

        $posts = Post::where('user_id', $user->id)->latest()->get();

        $canApprovePendingPosts = $user->hasPermission('approve post');

        return view('posts.index', compact('posts', 'canApprovePendingPosts'));
    }


    public function create()
    {
        return view('posts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'content' => 'required',
        ]);

        $post = Post::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'content' => $request->content,
        ]);

        return response()->json(['success' => true, 'post' => $post]);
    }

    public function update(Request $request, Post $post)
    {
        if ($post->status !== 'pending' || $post->user_id !== auth()->id()) {
            return response()->json(['error' => 'Not allowed to edit'], 403);
        }

        $post->update([
            'title' => $request->title,
            'content' => $request->content,
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy(Post $post)
    {
        if ($post->status !== 'pending' || $post->user_id !== auth()->id()) {
            return response()->json(['error' => 'Not allowed to delete'], 403);
        }

        $post->delete();

        return response()->json(['success' => true]);
    }

    public function approveAll()
    {
        $user = auth()->user();

        if (Gate::denies('can-approve-posts')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        Post::where('status', 'pending')
            ->where('user_id', '!=', $user->id)
            ->update(['status' => 'approved']);

        return response()->json(['success' => 'All pending posts approved.']);
    }

    public function allPosts()
    {
        $user = auth()->user();

        $posts = Post::with('user')->latest()->get();

        return view('posts.all', compact('posts', 'canApprove', 'user'));
    }

    public function public()
    {
        $posts = Post::with('user')
            ->where('status', 'approved')
            ->orderByDesc('updated_at')
            ->get();

        return view('welcome', compact('posts'));
    }

    public function show($id)
    {
        $blog = Post::with('user')->findOrFail($id);

        if ($blog->status !== 'approved') {
            abort(403, 'This post is not approved.');
        }

        return view('posts.show', compact('blog'));
    }

    public function approve(Post $post)
    {
        $user = auth()->user();

        if (Gate::denies('approve-post', $post)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $post->status = 'approved';
        $post->approved_by = auth()->id();
        $post->save();

        return response()->json(['success' => true,  'message' => 'Post approved successfully.']);
    }

    public function decline(Post $post)
    {
        $user = auth()->user();

        if (Gate::denies('approve-post', $post)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($post->status !== 'pending') {
            return response()->json(['error' => 'Post already processed'], 400);
        }

        $post->status = 'declined';
        $post->save();

        return response()->json(['success' => true, 'message' => 'Post declined successfully']);
    }

    public function pending(Request $request)
    {
        $user = auth()->user();

        $pendingPosts = Post::where('status', 'pending')
            ->where('user_id', '!=', $user->id)
            ->with('user')
            ->latest()
            ->get();

        return view('posts.pending', compact('pendingPosts'));
    }
}
