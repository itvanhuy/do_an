@extends('layouts.admin')
@section('title', 'Quản lý Liên hệ')
@section('content')
<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 25px;">
        <h3 style="margin:0;">Danh sách Liên hệ @if($unreadCount > 0)<span style="background:#e63946; color:white; padding:3px 10px; border-radius:20px; font-size:0.8rem; margin-left:10px;">{{ $unreadCount }} chưa đọc</span>@endif</h3>
    </div>
    @if(session('success')) <div style="background:#e8f5e9; color:#2e7d32; padding:10px; border-radius:5px; margin-bottom:15px;">{{ session('success') }}</div> @endif
    @if(session('error')) <div style="background:#fce4ec; color:#c62828; padding:10px; border-radius:5px; margin-bottom:15px;">{{ session('error') }}</div> @endif
    
    <table style="width:100%; border-collapse:collapse;">
        <thead>
            <tr style="text-align:left; border-bottom:2px solid #f5f5f5; color:#888; font-size:0.85rem; text-transform:uppercase;">
                <th style="padding:12px;">Tên</th>
                <th style="padding:12px;">Email</th>
                <th style="padding:12px;">Chủ đề</th>
                <th style="padding:12px;">Trạng thái</th>
                <th style="padding:12px;">Ngày gửi</th>
                <th style="padding:12px;">Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach($contacts as $contact)
            <tr style="border-bottom:1px solid #f5f5f5; {{ $contact->status === 'unread' ? 'font-weight:bold; background:#fffde7;' : '' }}">
                <td style="padding:12px;">{{ $contact->name }}</td>
                <td style="padding:12px;">{{ $contact->email }}</td>
                <td style="padding:12px;">{{ Str::limit($contact->subject, 40) }}</td>
                <td style="padding:12px;">
                    @if($contact->status === 'unread')
                        <span style="background:#fff3cd; color:#856404; padding:4px 10px; border-radius:20px; font-size:0.8rem;">Chưa đọc</span>
                    @elseif($contact->status === 'read')
                        <span style="background:#cce5ff; color:#004085; padding:4px 10px; border-radius:20px; font-size:0.8rem;">Đã đọc</span>
                    @else
                        <span style="background:#d4edda; color:#155724; padding:4px 10px; border-radius:20px; font-size:0.8rem;">Đã phản hồi</span>
                    @endif
                </td>
                <td style="padding:12px; color:#888;">{{ date('d/m/Y H:i', strtotime($contact->created_at)) }}</td>
                <td style="padding:12px;">
                    <a href="{{ route('admin.contacts.show', $contact->id) }}" style="background:#3498db; color:white; padding:5px 12px; border-radius:5px; text-decoration:none; font-size:0.8rem; margin-right:5px;">Xem & Phản hồi</a>
                    <form action="{{ route('admin.contacts.destroy', $contact->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Xóa liên hệ này?')">
                        @csrf @method('DELETE')
                        <button type="submit" style="background:#e63946; color:white; padding:5px 12px; border-radius:5px; border:none; cursor:pointer; font-size:0.8rem;">Xóa</button>
                    </form>
                </td>
            </tr>
            @endforeach
            @if($contacts->isEmpty())
            <tr><td colspan="6" style="padding:30px; text-align:center; color:#888;">Chưa có liên hệ nào.</td></tr>
            @endif
        </tbody>
    </table>
    <div style="margin-top:20px;">{{ $contacts->links() }}</div>
</div>
@endsection
