@extends('layouts.admin')
@section('title', 'Global Settings')
@section('content')
<div class="card">
    @if(session('success')) <div style="background:#e8f5e9; color:#2e7d32; padding:10px; border-radius:5px; margin-bottom:15px;">{{ session('success') }}</div> @endif
    <form action="{{ route('admin.settings.update') }}" method="POST">
        @csrf
        <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 30px;">
            <div>
                <h3>General Settings</h3>
                <div style="margin-bottom:15px;">
                    <label style="display:block; margin-bottom:5px;">Site Name</label>
                    <input type="text" name="site_name" value="GAMING SHOP" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;">
                </div>
                <div style="margin-bottom:15px;">
                    <label style="display:block; margin-bottom:5px;">Contact Email</label>
                    <input type="email" name="contact_email" value="contact@gamingshop.com" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;">
                </div>
                <div style="margin-bottom:15px;">
                    <label style="display:block; margin-bottom:5px;">Phone Number</label>
                    <input type="text" name="phone" value="+84 123 456 789" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;">
                </div>
            </div>
            <div>
                <h3>Store Config</h3>
                <div style="margin-bottom:15px;">
                    <label style="display:block; margin-bottom:5px;">Currency Symbol</label>
                    <input type="text" name="currency" value="₫" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;">
                </div>
                <div style="margin-bottom:15px;">
                    <label style="display:block; margin-bottom:5px;">Tax Rate (%)</label>
                    <input type="number" name="tax" value="10" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;">
                </div>
                <div style="margin-bottom:15px;">
                    <label style="display:block; margin-bottom:5px;">Low Stock Alert</label>
                    <input type="number" name="low_stock" value="5" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;">
                </div>
            </div>
        </div>
        <div style="margin-top:20px; border-top:1px solid #eee; padding-top:20px; text-align:right;">
            <button type="submit" style="background:var(--admin-accent); color:white; border:none; padding:12px 30px; border-radius:5px; font-weight:bold; cursor:pointer;">Save All Settings</button>
        </div>
    </form>
</div>
@endsection
