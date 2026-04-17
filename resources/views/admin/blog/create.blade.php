@extends('layouts.admin')
@section('title', 'Add New Blog Post')
@section('content')
<div class="card" style="max-width: 900px; margin: 0 auto;">
    <form action="{{ route('admin.blog.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div style="margin-bottom: 20px;">
            <label style="display:block; margin-bottom:5px;">Article Title</label>
            <input type="text" name="title" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;" required>
        </div>

        <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display:block; margin-bottom:5px;">Status</label>
                <select name="status" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;">
                    <option value="published">Published</option>
                    <option value="draft">Draft</option>
                </select>
            </div>
            <div>
                <label style="display:block; margin-bottom:5px;">Post Type</label>
                <select name="post_type" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;">
                    <option value="news">General / Product News</option>
                    <option value="tournament">Tournament News</option>
                </select>
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display:block; margin-bottom:5px;">Featured Image</label>
            <input type="file" name="image" style="width:100%;">
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display:block; margin-bottom:5px;">Excerpt (Short Summary)</label>
            <textarea name="excerpt" rows="3" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;"></textarea>
        </div>

        <div style="margin-bottom: 25px;">
            <label style="display:block; margin-bottom:5px;">Content</label>
            <textarea name="content" rows="15" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;" required></textarea>
        </div>

        <div style="display:flex; justify-content:flex-end; gap:10px;">
            <a href="{{ route('admin.blog') }}" style="background:#eee; color:#333; text-decoration:none; padding:12px 25px; border-radius:5px;">Cancel</a>
            <button type="submit" style="background:var(--admin-accent); color:white; border:none; padding:12px 30px; border-radius:5px; cursor:pointer; font-weight:bold;">Create Post</button>
        </div>
    </form>
</div>
@endsection
