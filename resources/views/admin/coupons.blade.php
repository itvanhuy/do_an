@extends('layouts.admin')
@section('title', 'Quản lý Mã giảm giá')
@section('content')
<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 25px;">
        <h3 style="margin:0;">Mã giảm giá (Coupon)</h3>
    </div>
    @if(session('success')) <div style="background:#e8f5e9; color:#2e7d32; padding:10px; border-radius:5px; margin-bottom:15px;">{{ session('success') }}</div> @endif
    @if($errors->any()) <div style="background:#fce4ec; color:#c62828; padding:10px; border-radius:5px; margin-bottom:15px;">{{ $errors->first() }}</div> @endif

    {{-- Add Coupon Form --}}
    <div style="background:#f8f9fa; padding:25px; border-radius:10px; margin-bottom:30px;">
        <h4 style="margin:0 0 20px;">Thêm mã giảm giá mới</h4>
        <form action="{{ route('admin.coupons.store') }}" method="POST">
            @csrf
            <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:15px;">
                <div>
                    <label style="display:block; margin-bottom:5px; font-weight:600;">Mã code <span style="color:red">*</span></label>
                    <input type="text" name="code" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; text-transform:uppercase;" placeholder="SALE10">
                </div>
                <div>
                    <label style="display:block; margin-bottom:5px; font-weight:600;">Loại giảm giá <span style="color:red">*</span></label>
                    <select name="discount_type" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;">
                        <option value="percent">Phần trăm (%)</option>
                        <option value="fixed">Fixed Amount ($)</option>
                    </select>
                </div>
                <div>
                    <label style="display:block; margin-bottom:5px; font-weight:600;">Giá trị giảm <span style="color:red">*</span></label>
                    <input type="number" name="discount_value" required min="0" step="0.01" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;" placeholder="10">
                </div>
                <div>
                    <label style="display:block; margin-bottom:5px; font-weight:600;">Minimum Order ($)</label>
                    <input type="number" name="min_order" min="0" value="0" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;">
                </div>
                <div>
                    <label style="display:block; margin-bottom:5px; font-weight:600;">Số lần sử dụng tối đa</label>
                    <input type="number" name="max_uses" min="1" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;" placeholder="Không giới hạn">
                </div>
                <div>
                    <label style="display:block; margin-bottom:5px; font-weight:600;">Ngày hết hạn</label>
                    <input type="datetime-local" name="expires_at" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;">
                </div>
            </div>
            <div style="margin-top:15px;">
                <label><input type="checkbox" name="is_active" checked> Kích hoạt mã này</label>
            </div>
            <button type="submit" style="margin-top:15px; background:#e63946; color:white; padding:10px 25px; border:none; border-radius:5px; font-weight:bold; cursor:pointer;">Thêm Mã Giảm Giá</button>
        </form>
    </div>

    {{-- Coupons Table --}}
    <table style="width:100%; border-collapse:collapse;">
        <thead>
            <tr style="text-align:left; border-bottom:2px solid #f5f5f5; color:#888; font-size:0.85rem; text-transform:uppercase;">
                <th style="padding:12px;">Mã Code</th>
                <th style="padding:12px;">Loại</th>
                <th style="padding:12px;">Giá trị</th>
                <th style="padding:12px;">Đơn tối thiểu</th>
                <th style="padding:12px;">Sử dụng</th>
                <th style="padding:12px;">Hết hạn</th>
                <th style="padding:12px;">Trạng thái</th>
                <th style="padding:12px;">Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach($coupons as $coupon)
            <tr style="border-bottom:1px solid #f5f5f5;">
                <td style="padding:12px;"><code style="background:#f1f3f4; padding:4px 8px; border-radius:4px; font-weight:bold; font-size:0.95rem;">{{ $coupon->code }}</code></td>
                <td style="padding:12px;">{{ $coupon->discount_type === 'percent' ? 'Phần trăm' : 'Cố định' }}</td>
                <td style="padding:12px; font-weight:bold; color:#e63946;">
                    {{ $coupon->discount_type === 'percent' ? $coupon->discount_value . '%' : '$' . number_format($coupon->discount_value / 25000, 2) }}
                </td>
                <td style="padding:12px;">${{ number_format($coupon->min_order / 25000, 2) }}</td>
                <td style="padding:12px;">{{ $coupon->used_count }}{{ $coupon->max_uses ? '/' . $coupon->max_uses : '' }}</td>
                <td style="padding:12px; color:#888; font-size:0.85rem;">{{ $coupon->expires_at ? date('d/m/Y', strtotime($coupon->expires_at)) : '∞' }}</td>
                <td style="padding:12px;">
                    @if($coupon->is_active)
                        <span style="background:#d4edda; color:#155724; padding:4px 10px; border-radius:20px; font-size:0.8rem;">Hoạt động</span>
                    @else
                        <span style="background:#f8d7da; color:#721c24; padding:4px 10px; border-radius:20px; font-size:0.8rem;">Vô hiệu</span>
                    @endif
                </td>
                <td style="padding:12px;">
                    <form action="{{ route('admin.coupons.destroy', $coupon->id) }}" method="POST" onsubmit="return confirm('Xóa mã này?')">
                        @csrf @method('DELETE')
                        <button type="submit" style="background:#e63946; color:white; padding:5px 12px; border-radius:5px; border:none; cursor:pointer; font-size:0.8rem;">Xóa</button>
                    </form>
                </td>
            </tr>
            @endforeach
            @if($coupons->isEmpty())
            <tr><td colspan="8" style="padding:30px; text-align:center; color:#888;">Chưa có mã giảm giá nào.</td></tr>
            @endif
        </tbody>
    </table>
    <div style="margin-top:20px;">{{ $coupons->links() }}</div>
</div>
@endsection
