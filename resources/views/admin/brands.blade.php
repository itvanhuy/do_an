@extends('layouts.admin')
@section('title', 'Manage Brands')
@section('content')
<div style="display:grid; grid-template-columns: 1fr 350px; gap: 30px;">
    <div class="card">
        <h3 style="margin-top:0;">Brand List</h3>
        @if(session('success')) <div style="background:#e8f5e9; color:#2e7d32; padding:10px; border-radius:5px; margin-bottom:15px;">{{ session('success') }}</div> @endif
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="text-align:left; border-bottom:2px solid #f5f5f5; color:#888; font-size:0.85rem; text-transform:uppercase;">
                    <th style="padding:15px;">Logo</th>
                    <th style="padding:15px;">Name</th>
                    <th style="padding:15px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($brands as $brand)
                <tr style="border-bottom:1px solid #f5f5f5;">
                    <td style="padding:15px;"><img src="{{ asset('img/brands/'.$brand->logo) }}" style="width:60px; height:40px; object-fit:contain;" onerror="this.style.display='none'"></td>
                    <td style="padding:15px; font-weight:600;">{{ $brand->name }}</td>
                    <td style="padding:15px;">
                        <a href="{{ route('admin.brands.destroy', $brand->id) }}" onclick="return confirm('Delete this brand?')" style="color:#e74c3c;"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="card">
        <h3 style="margin-top:0;">Add New Brand</h3>
        <form action="{{ route('admin.brands.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div style="margin-bottom:15px;">
                <label style="display:block; margin-bottom:5px;">Brand Name</label>
                <input type="text" name="name" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;" required>
            </div>
            <div style="margin-bottom:20px;">
                <label style="display:block; margin-bottom:5px;">Logo</label>
                <input type="file" name="logo" style="width:100%;">
            </div>
            <button type="submit" style="width:100%; background:var(--admin-sidebar); color:white; border:none; padding:12px; border-radius:5px; cursor:pointer;">Save Brand</button>
        </form>
    </div>
</div>
@endsection
