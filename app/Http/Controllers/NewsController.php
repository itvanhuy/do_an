<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NewsController extends Controller
{
    public function index()
    {
        $featuredPost = DB::table('posts')
            ->where('status', 'published')
            ->orderBy('created_at', 'desc')
            ->first();

        $excludeId = $featuredPost ? $featuredPost->id : 0;
        
        $posts = DB::table('posts')
            ->where('status', 'published')
            ->where('id', '!=', $excludeId)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('news', compact('featuredPost', 'posts'));
    }

    public function show($id)
    {
        $post = DB::table('posts')
            ->where('id', $id)
            ->where('status', 'published')
            ->first();

        if (!$post) abort(404);

        // Update views count
        DB::table('posts')->where('id', $id)->increment('views');

        $relatedPosts = DB::table('posts')
            ->where('status', 'published')
            ->where('id', '!=', $id)
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();
            
        $comments = DB::table('comments')
            ->join('users', 'comments.user_id', '=', 'users.id')
            ->where('comments.post_id', $id)
            ->select('comments.*', 'users.username')
            ->orderBy('comments.created_at', 'desc')
            ->get();
            
        return view('news-detail', compact('post', 'relatedPosts', 'comments'));
    }

    public function submitComment(Request $request, $id)
    {
        if (!\Illuminate\Support\Facades\Auth::check()) {
            return back()->with('error', 'Vui lòng đăng nhập để bình luận.');
        }

        $request->validate(['content' => 'required|min:3']);

        DB::table('comments')->insert([
            'post_id' => $id,
            'user_id' => \Illuminate\Support\Facades\Auth::id(),
            'content' => $request->content,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return back()->with('success', 'Bình luận của bạn đã được đăng!');
    }
}
