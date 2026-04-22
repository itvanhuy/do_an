@extends('layouts.admin')
@section('title', 'Quản lý Slide')
@section('content')
<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 25px;">
        <h3 style="margin:0;">Quản lý Slide Banner</h3>
    </div>
    @if(session('success')) <div style="background:#e8f5e9; color:#2e7d32; padding:10px; border-radius:5px; margin-bottom:15px;">{{ session('success') }}</div> @endif
    
    {{-- Add Slide Form --}}
    <div style="background:#f8f9fa; padding:25px; border-radius:10px; margin-bottom:30px;">
        <h4 style="margin:0 0 20px;">Thêm slide mới</h4>
        <form action="{{ route('admin.slides.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px;">
                <div>
                    <label style="display:block; margin-bottom:5px; font-weight:600;">Tiêu đề</label>
                    <input type="text" name="title" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;" placeholder="Tiêu đề slide">
                </div>
                <div>
                    <label style="display:block; margin-bottom:5px; font-weight:600;">Phụ đề</label>
                    <input type="text" name="subtitle" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;" placeholder="Phụ đề">
                </div>
                <div>
                    <label style="display:block; margin-bottom:5px; font-weight:600;">Hình ảnh <span style="color:red">*</span></label>
                    <input type="file" name="image" accept="image/*" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;">
                </div>
                <div>
                    <label style="display:block; margin-bottom:5px; font-weight:600;">Liên kết (URL)</label>
                    <input type="text" name="link" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;" placeholder="/shop">
                </div>
                <div>
                    <label style="display:block; margin-bottom:5px; font-weight:600;">Văn bản nút</label>
                    <input type="text" name="button_text" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;" placeholder="Mua ngay">
                </div>
                <div>
                    <label style="display:block; margin-bottom:5px; font-weight:600;">Thứ tự</label>
                    <input type="number" name="sort_order" value="0" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;">
                </div>
            </div>
            <div style="margin-top:15px;">
                <label><input type="checkbox" name="is_active" checked> Hiển thị slide</label>
            </div>
            <button type="submit" style="margin-top:15px; background:#e63946; color:white; padding:10px 25px; border:none; border-radius:5px; font-weight:bold; cursor:pointer;">Thêm Slide</button>
        </form>
    </div>

    {{-- Slides List --}}
    <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(300px, 1fr)); gap:20px;">
        @foreach($slides as $slide)
        <div style="border:1px solid #eee; border-radius:10px; overflow:hidden; background:white; box-shadow:0 2px 8px rgba(0,0,0,0.05);">
            <div style="position:relative;">
                <img src="{{ asset('img/slides/' . $slide->image) }}" style="width:100%; height:160px; object-fit:cover;" onerror="this.src='{{ asset('img/product/default.jpg') }}'">
                @if(!$slide->is_active)
                <div style="position:absolute; top:10px; right:10px; background:rgba(0,0,0,0.6); color:white; padding:3px 8px; border-radius:5px; font-size:0.75rem;">Ẩn</div>
                @endif
            </div>
            <div style="padding:15px;">
                <h4 style="margin:0 0 5px;">{{ $slide->title ?: 'Không có tiêu đề' }}</h4>
                <p style="color:#888; font-size:0.85rem; margin:0 0 10px;">{{ $slide->subtitle }}</p>
                <p style="color:#888; font-size:0.8rem; margin:0 0 12px;">Thứ tự: {{ $slide->sort_order }} | {{ $slide->is_active ? '✅ Hiển thị' : '❌ Ẩn' }}</p>
                <div style="display:flex; gap:8px;">
                    <form action="{{ route('admin.slides.toggle', $slide->id) }}" method="POST" style="flex:1">
                        @csrf
                        <button type="submit" style="width:100%; padding:6px; border:1px solid #3498db; background:white; color:#3498db; border-radius:5px; cursor:pointer; font-size:0.8rem;">
                            {{ $slide->is_active ? 'Ẩn' : 'Hiện' }}
                        </button>
                    </form>
                    <form action="{{ route('admin.slides.destroy', $slide->id) }}" method="POST" style="flex:1" onsubmit="return confirm('Xóa slide này?')">
                        @csrf @method('DELETE')
                        <button type="submit" style="width:100%; padding:6px; background:#e63946; color:white; border:none; border-radius:5px; cursor:pointer; font-size:0.8rem;">Xóa</button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
        @if($slides->isEmpty())
        <div style="grid-column:1/-1; text-align:center; padding:50px; color:#888;">Chưa có slide nào.</div>
        @endif
    </div>
</div>
@endsection
