@extends('layouts.admin')
@section('title', 'Manage Blog Comments')
@section('content')
<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 25px;">
        <h3 style="margin:0;">Comments List</h3>
    </div>
    @if(session('success')) <div style="background:#e8f5e9; color:#2e7d32; padding:10px; border-radius:5px; margin-bottom:15px;">{{ session('success') }}</div> @endif
    <table style="width:100%; border-collapse:collapse;">
        <thead>
            <tr style="text-align:left; border-bottom:2px solid #f5f5f5; color:#888; font-size:0.85rem; text-transform:uppercase;">
                <th style="padding:15px;">User</th>
                <th style="padding:15px;">Comment</th>
                <th style="padding:15px;">Article</th>
                <th style="padding:15px;">Date</th>
                <th style="padding:15px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($comments as $comment)
            <tr style="border-bottom:1px solid #f5f5f5;">
                <td style="padding:15px; font-weight:600;">{{ $comment->username }}</td>
                <td style="padding:15px; color:#555; font-size:0.9rem;">{{ $comment->content }}</td>
                <td style="padding:15px;"><a href="{{ route('news.detail', $comment->post_id) }}" target="_blank" style="color:#3498db; text-decoration:none;">{{ Str::limit($comment->post_title, 30) }}</a></td>
                <td style="padding:15px; color:#999; font-size:0.85rem;">{{ date('M d, Y', strtotime($comment->created_at)) }}</td>
                <td style="padding:15px;">
                    <a href="{{ route('admin.comments.destroy', $comment->id) }}" onclick="return confirm('Delete comment?')" style="color:#e74c3c;"><i class="fas fa-trash"></i></a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div style="margin-top:20px;">
        {{ $comments->links() }}
    </div>
</div>
@endsection
