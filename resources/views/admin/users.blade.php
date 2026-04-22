@extends('layouts.admin')
@section('title', 'Manage Users')
@section('content')
<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 25px;">
        <h3 style="margin:0;">User List</h3>
    </div>
    @if(session('success')) <div style="background:#e8f5e9; color:#2e7d32; padding:10px; border-radius:5px; margin-bottom:15px;">{{ session('success') }}</div> @endif
    @if(session('error')) <div style="background:#ffebee; color:#c62828; padding:10px; border-radius:5px; margin-bottom:15px;">{{ session('error') }}</div> @endif
    <table style="width:100%; border-collapse:collapse;">
        <thead>
            <tr style="text-align:left; border-bottom:2px solid #f5f5f5; color:#888; font-size:0.85rem; text-transform:uppercase;">
                <th style="padding:15px;">#</th>
                <th style="padding:15px;">Username</th>
                <th style="padding:15px;">Email</th>
                <th style="padding:15px;">Role</th>
                <th style="padding:15px;">Registered</th>
                <th style="padding:15px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr style="border-bottom:1px solid #f5f5f5;">
                <td style="padding:15px; color:#888;">{{ $user->id }}</td>
                <td style="padding:15px; font-weight:600;">{{ $user->username }}</td>
                <td style="padding:15px;">{{ $user->email }}</td>
                <td style="padding:15px;">
                    <form action="{{ route('admin.users.update_role', $user->id) }}" method="POST" onchange="this.submit()">
                        @csrf
                        <select name="role" style="padding:5px 10px; border-radius:5px; border:1px solid #ddd; background: {{ $user->role === 'admin' ? '#ffebee' : '#e8f5e9' }}; color: {{ $user->role === 'admin' ? '#c62828' : '#2e7d32' }};">
                            <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>User</option>
                            <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                    </form>
                </td>
                <td style="padding:15px; color:#888;">{{ date('M d, Y', strtotime($user->created_at)) }}</td>
                <td style="padding:15px;">
                    <a href="{{ route('admin.users.destroy', $user->id) }}" onclick="return confirm('Delete user?')" style="color:#e74c3c; text-decoration:none;"><i class="fas fa-trash"></i> Delete</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div style="margin-top:20px;">
        {{ $users->links() }}
    </div>
</div>
@endsection
