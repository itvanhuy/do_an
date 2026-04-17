@extends('layouts.admin')
@section('title', 'Manage Product Reviews')
@section('content')
<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 25px;">
        <h3 style="margin:0;">Product Reviews</h3>
    </div>
    @if(session('success')) <div style="background:#e8f5e9; color:#2e7d32; padding:10px; border-radius:5px; margin-bottom:15px;">{{ session('success') }}</div> @endif
    <table style="width:100%; border-collapse:collapse;">
        <thead>
            <tr style="text-align:left; border-bottom:2px solid #f5f5f5; color:#888; font-size:0.85rem; text-transform:uppercase;">
                <th style="padding:15px;">Product</th>
                <th style="padding:15px;">Rating</th>
                <th style="padding:15px;">Review</th>
                <th style="padding:15px;">Status</th>
                <th style="padding:15px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reviews as $review)
            <tr style="border-bottom:1px solid #f5f5f5;">
                <td style="padding:15px;">
                    <div style="display:flex; align-items:center; gap:8px;">
                        <img src="{{ asset('img/product/'.$review->product_image) }}" style="width:30px; height:30px; object-fit:contain; background:#f9f9f9; border-radius:4px;" onerror="this.src='{{ asset('img/product/default.jpg') }}'">
                        <span style="font-weight:600; font-size:0.9rem;">{{ Str::limit($review->product_name, 25) }}</span>
                    </div>
                </td>
                <td style="padding:15px; color:#f1c40f;">
                    {!! str_repeat('★', $review->rating) !!}{!! str_repeat('☆', 5 - $review->rating) !!}
                </td>
                <td style="padding:15px; color:#666; font-size:0.85rem; max-width:300px;">{{ $review->comment }}</td>
                <td style="padding:15px;"><span class="status-badge status-{{ $review->status }}">{{ $review->status }}</span></td>
                <td style="padding:15px;">
                    @if($review->status == 'pending')
                    <a href="{{ route('admin.reviews.approve', $review->id) }}" style="color:#2ecc71; margin-right:15px;" title="Approve"><i class="fas fa-check-circle"></i></a>
                    @endif
                    <a href="{{ route('admin.reviews.destroy', $review->id) }}" onclick="return confirm('Delete review?')" style="color:#e74c3c;"><i class="fas fa-trash"></i></a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div style="margin-top:20px;">
        {{ $reviews->links() }}
    </div>
</div>
@endsection
