@extends('layouts.admin')
@section('title', 'Manage Categories')
@section('content')
<div style="display:grid; grid-template-columns: 1fr 350px; gap: 30px;">
    <div class="card">
        <h3 style="margin-top:0;">Category List</h3>
        @if(session('success')) <div style="background:#e8f5e9; color:#2e7d32; padding:10px; border-radius:5px; margin-bottom:15px;">{{ session('success') }}</div> @endif
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="text-align:left; border-bottom:2px solid #f5f5f5; color:#888; font-size:0.85rem; text-transform:uppercase;">
                    <th style="padding:15px;">Name</th>
                    <th style="padding:15px;">Slug</th>
                    <th style="padding:15px;">Description</th>
                    <th style="padding:15px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categories as $category)
                <tr style="border-bottom:1px solid #f5f5f5;">
                    <td style="padding:15px; font-weight:600;">{{ $category->name }}</td>
                    <td style="padding:15px; color:#999;">{{ $category->slug }}</td>
                    <td style="padding:15px; color:#666;">{{ $category->description ?? 'N/A' }}</td>
                    <td style="padding:15px;">
                        <a href="{{ route('admin.categories.destroy', $category->id) }}" onclick="return confirm('Delete this category?')" style="color:#e74c3c;"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="card">
        <h3 style="margin-top:0;">Add New Category</h3>
        <form action="{{ route('admin.categories.store') }}" method="POST">
            @csrf
            <div style="margin-bottom:15px;">
                <label style="display:block; margin-bottom:5px;">Category Name</label>
                <input type="text" name="name" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;" required>
            </div>
            <div style="margin-bottom:20px;">
                <label style="display:block; margin-bottom:5px;">Description (Optional)</label>
                <textarea name="description" rows="4" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;"></textarea>
            </div>
            <button type="submit" style="width:100%; background:var(--admin-sidebar); color:white; border:none; padding:12px; border-radius:5px; cursor:pointer;">Save Category</button>
        </form>
    </div>
</div>
@endsection
