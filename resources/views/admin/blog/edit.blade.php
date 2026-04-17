@extends('layouts.admin')
@section('title', 'Edit Blog Post: ' . $post->title)
@section('content')
<div class="card" style="max-width: 900px; margin: 0 auto;">
    <form action="{{ route('admin.blog.update', $post->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div style="margin-bottom: 20px;">
            <label style="display:block; margin-bottom:5px;">Article Title</label>
            <input type="text" name="title" value="{{ $post->title }}" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;" required>
        </div>

        <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display:block; margin-bottom:5px;">Status</label>
                <select name="status" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;">
                    <option value="published" {{ $post->status == 'published' ? 'selected' : '' }}>Published</option>
                    <option value="draft" {{ $post->status == 'draft' ? 'selected' : '' }}>Draft</option>
                </select>
            </div>
            <div>
                <label style="display:block; margin-bottom:5px;">Post Type</label>
                <select name="post_type" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;">
                    <option value="news" {{ $post->post_type == 'news' ? 'selected' : '' }}>General / Product News</option>
                    <option value="tournament" {{ $post->post_type == 'tournament' ? 'selected' : '' }}>Tournament News</option>
                </select>
            </div>
        </div>

        <div style="display:grid; grid-template-columns: 150px 1fr; gap: 20px; align-items: center; margin-bottom: 20px;">
            <img src="{{ asset('img/'.$post->image) }}" style="width:150px; height:100px; object-fit:cover; border-radius:8px;" onerror="this.src='{{ asset('img/background/baiviet1.png') }}'">
            <div>
                <label style="display:block; margin-bottom:5px;">Update Featured Image (Optional)</label>
                <input type="file" name="image" style="width:100%;">
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display:block; margin-bottom:5px;">Excerpt (Short Summary)</label>
            <textarea name="excerpt" rows="3" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;">{{ $post->excerpt }}</textarea>
        </div>

        <div style="margin-bottom: 25px;">
            <label style="display:block; margin-bottom:5px;">Content</label>
            <textarea name="content" rows="15" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;" required>{{ $post->content }}</textarea>
        </div>

        <div style="display:flex; justify-content:flex-end; gap:10px;">
            <a href="{{ route('admin.blog') }}" style="background:#eee; color:#333; text-decoration:none; padding:12px 25px; border-radius:5px;">Cancel</a>
            <button type="submit" style="background:var(--admin-accent); color:white; border:none; padding:12px 30px; border-radius:5px; cursor:pointer; font-weight:bold;">Update Post</button>
        </div>
    </form>
</div>
@endsection
