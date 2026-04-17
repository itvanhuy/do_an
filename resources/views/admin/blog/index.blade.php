@extends('layouts.admin')
@section('title', 'Manage Blog Posts')
@section('content')
<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 25px;">
        <h3 style="margin:0;">Articles List</h3>
        <a href="{{ route('admin.blog.create') }}" class="btn" style="background:var(--admin-accent); color:white; border:none; padding:10px 20px; border-radius:5px; text-decoration:none;">+ Add New Post</a>
    </div>
    @if(session('success')) <div style="background:#e8f5e9; color:#2e7d32; padding:10px; border-radius:5px; margin-bottom:15px;">{{ session('success') }}</div> @endif
    <table style="width:100%; border-collapse:collapse;">
        <thead>
            <tr style="text-align:left; border-bottom:2px solid #f5f5f5; color:#888; font-size:0.85rem; text-transform:uppercase;">
                <th style="padding:15px;">Image</th>
                <th style="padding:15px;">Title</th>
                <th style="padding:15px;">Type</th>
                <th style="padding:15px;">Status</th>
                <th style="padding:15px;">Views</th>
                <th style="padding:15px;">Date</th>
                <th style="padding:15px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($posts as $post)
            <tr style="border-bottom:1px solid #f5f5f5;">
                <td style="padding:15px;"><img src="{{ asset('img/'.$post->image) }}" style="width:50px; height:30px; object-fit:cover; border-radius:4px;" onerror="this.src='{{ asset('img/background/baiviet2.png') }}'"></td>
                <td style="padding:15px; font-weight:600;">{{ Str::limit($post->title, 40) }}</td>
                <td style="padding:15px;"><span class="status-badge" style="background:{{ $post->post_type == 'tournament' ? '#a970ff' : '#6c757d' }}; color:white; font-size:0.7rem;">{{ strtoupper($post->post_type) }}</span></td>
                <td style="padding:15px;"><span class="status-badge status-{{ $post->status }}">{{ $post->status }}</span></td>
                <td style="padding:15px;">{{ $post->views }}</td>
                <td style="padding:15px; color:#999;">{{ date('M d, Y', strtotime($post->created_at)) }}</td>
                <td style="padding:15px;">
                    <a href="{{ route('admin.blog.edit', $post->id) }}" style="color:#3498db; margin-right:15px;"><i class="fas fa-edit"></i></a>
                    <form action="{{ route('admin.blog.destroy', $post->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this post?')">
                        @csrf @method('DELETE')
                        <button type="submit" style="background:none; border:none; color:#e74c3c; cursor:pointer;"><i class="fas fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div style="margin-top:20px;">
        {{ $posts->links() }}
    </div>
</div>
@endsection
