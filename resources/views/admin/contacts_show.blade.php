@extends('layouts.admin')
@section('title', 'Chi tiết Liên hệ')
@section('content')
<div style="max-width:800px; margin:0 auto;">
    <div style="margin-bottom:20px;">
        <a href="{{ route('admin.contacts') }}" style="color:#3498db; text-decoration:none;">← Quay lại danh sách liên hệ</a>
    </div>

    @if(session('success')) <div style="background:#e8f5e9; color:#2e7d32; padding:12px; border-radius:8px; margin-bottom:20px;">{{ session('success') }}</div> @endif

    <div class="card" style="margin-bottom:20px;">
        <h3 style="margin:0 0 20px;">📧 Nội dung liên hệ</h3>
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-bottom:20px;">
            <div>
                <p style="color:#888; font-size:0.85rem; margin:0 0 4px;">Tên khách hàng</p>
                <p style="font-weight:bold; margin:0;">{{ $contact->name }}</p>
            </div>
            <div>
                <p style="color:#888; font-size:0.85rem; margin:0 0 4px;">Email</p>
                <p style="font-weight:bold; margin:0;"><a href="mailto:{{ $contact->email }}" style="color:#3498db;">{{ $contact->email }}</a></p>
            </div>
            <div>
                <p style="color:#888; font-size:0.85rem; margin:0 0 4px;">Chủ đề</p>
                <p style="font-weight:bold; margin:0;">{{ $contact->subject }}</p>
            </div>
            <div>
                <p style="color:#888; font-size:0.85rem; margin:0 0 4px;">Ngày gửi</p>
                <p style="font-weight:bold; margin:0;">{{ date('d/m/Y H:i', strtotime($contact->created_at)) }}</p>
            </div>
        </div>
        <div style="background:#f8f9fa; padding:20px; border-radius:8px; border-left:4px solid #3498db;">
            <p style="color:#888; font-size:0.85rem; margin:0 0 8px;">Nội dung tin nhắn</p>
            <p style="margin:0; line-height:1.7;">{{ $contact->message }}</p>
        </div>
    </div>

    @if($contact->reply_content)
    <div class="card" style="margin-bottom:20px; border-left:4px solid #28a745;">
        <h3 style="margin:0 0 15px; color:#28a745;">✅ Đã phản hồi – {{ date('d/m/Y H:i', strtotime($contact->replied_at)) }}</h3>
        <div style="background:#e8f5e9; padding:15px; border-radius:8px;">
            <p style="margin:0; line-height:1.7;">{{ $contact->reply_content }}</p>
        </div>
    </div>
    @endif

    <div class="card">
        <h3 style="margin:0 0 20px;">💬 Gửi phản hồi cho khách hàng</h3>
        <form action="{{ route('admin.contacts.reply', $contact->id) }}" method="POST">
            @csrf
            <div style="margin-bottom:15px;">
                <label style="display:block; margin-bottom:6px; font-weight:600;">Nội dung phản hồi <span style="color:red">*</span></label>
                <textarea name="reply_content" rows="6" required style="width:100%; padding:12px; border:1px solid #ddd; border-radius:8px; resize:vertical; font-size:14px;">{{ $contact->reply_content }}</textarea>
                @error('reply_content')<p style="color:red; font-size:0.85rem; margin-top:4px;">{{ $message }}</p>@enderror
            </div>
            <button type="submit" style="background:#28a745; color:white; padding:12px 30px; border:none; border-radius:8px; font-weight:bold; cursor:pointer; font-size:14px;">
                📧 Gửi phản hồi qua Email
            </button>
        </form>
    </div>
</div>
@endsection
