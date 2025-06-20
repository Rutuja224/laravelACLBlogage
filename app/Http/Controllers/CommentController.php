<?php

namespace App\Http\Controllers;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request){
        if(!Auth::check()){
            return redirect()->route('login');
        }

        $request->validate([
            'content' => 'required|string',
            'post_id' => 'required|exists:posts,id',
            'parent_id' => 'nullable|exists:comments,id'
        ]);

        Comment::create([
            'user_id' => Auth::id(),
            'post_id' => $request->post_id,
            'parent_id' => $request->parent_id,
            'content' => $request->content
        ]);

        return back();
    }
    
}
