@extends('layouts.admin')
@section('title', 'Edit Category')
@section('content')
<div style="max-width: 600px; margin: 0 auto;">
    <div class="card">
        <h3 style="margin-top:0;">Edit Category</h3>
        <form action="{{ route('admin.categories.update', $category->id) }}" method="POST">
            @csrf
            <div style="margin-bottom:15px;">
                <label style="display:block; margin-bottom:5px;">Category Name</label>
                <input type="text" name="name" value="{{ $category->name }}" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;" required>
            </div>
            <div style="margin-bottom:20px;">
                <label style="display:block; margin-bottom:5px;">Description (Optional)</label>
                <textarea name="description" rows="4" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;">{{ $category->description }}</textarea>
            </div>
            <div style="display: flex; gap: 10px;">
                <button type="submit" style="flex: 1; background:var(--admin-sidebar); color:white; border:none; padding:12px; border-radius:5px; cursor:pointer;">Update Category</button>
                <a href="{{ route('admin.categories') }}" style="flex: 1; text-align: center; background:#ccc; color:#333; border:none; padding:12px; border-radius:5px; cursor:pointer; text-decoration: none;">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
