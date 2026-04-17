@extends('layouts.admin')
@section('title', 'Newsletter Subscribers')
@section('content')
<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 25px;">
        <h3 style="margin:0;">Subscribers List</h3>
    </div>
    @if(session('success')) <div style="background:#e8f5e9; color:#2e7d32; padding:10px; border-radius:5px; margin-bottom:15px;">{{ session('success') }}</div> @endif
    <table style="width:100%; border-collapse:collapse;">
        <thead>
            <tr style="text-align:left; border-bottom:2px solid #f5f5f5; color:#888; font-size:0.85rem; text-transform:uppercase;">
                <th style="padding:15px;">Email</th>
                <th style="padding:15px;">Subscribed Date</th>
                <th style="padding:15px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($subscribers as $s)
            <tr style="border-bottom:1px solid #f5f5f5;">
                <td style="padding:15px; font-weight:600;">{{ $s->email }}</td>
                <td style="padding:15px; color:#999; font-size:0.85rem;">{{ date('M d, Y H:i', strtotime($s->created_at)) }}</td>
                <td style="padding:15px;">
                    <a href="{{ route('admin.newsletters.destroy', $s->id) }}" onclick="return confirm('Remove subscriber?')" style="color:#e74c3c;"><i class="fas fa-trash"></i></a>
                </td>
            </tr>
            @empty
                <tr><td colspan="3" style="text-align:center; padding:30px; color:#999;">No subscribers yet.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div style="margin-top:20px;">
        {{ $subscribers->links() }}
    </div>
</div>
@endsection
