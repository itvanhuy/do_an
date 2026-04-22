<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - TechShop Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --admin-bg: #f5f6fa; --admin-sidebar: #2c3e50; --admin-accent: #3498db; --admin-text: #2f3640; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: var(--admin-bg); color: var(--admin-text); margin: 0; display: flex; min-height: 100vh; }
        .admin-sidebar { width: 250px; background: var(--admin-sidebar); color: white; display: flex; flex-direction: column; }
        .sidebar-header { padding: 30px 20px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar-menu { list-style: none; padding: 20px 0; margin: 0; flex-grow: 1; }
        .sidebar-menu li { padding: 5px 20px; }
        .sidebar-menu a { text-decoration: none; color: rgba(255,255,255,0.7); display: flex; align-items: center; gap: 15px; padding: 12px 15px; border-radius: 8px; transition: 0.3s; }
        .sidebar-menu a:hover, .sidebar-menu a.active { background: rgba(255,255,255,0.05); color: white; }
        .sidebar-menu a.active { border-left: 4px solid var(--admin-accent); }
        .admin-main { flex-grow: 1; padding: 40px; display: flex; flex-direction: column; overflow-y: auto; }
        .admin-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; }
        .card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .status-badge { padding: 5px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; }
        @yield('styles')
    </style>
</head>
<body>
    <aside class="admin-sidebar">
        <div class="sidebar-header">
            <h2 style="margin:0; font-size: 1.5rem;">TechShop Admin</h2>
        </div>
        <ul class="sidebar-menu">
            <li><a href="{{ route('admin.dashboard') }}" class="{{ request()->is('admin/dashboard*') ? 'active' : '' }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="{{ route('admin.products') }}" class="{{ request()->is('admin/products*') ? 'active' : '' }}"><i class="fas fa-box"></i> Products</a></li>
            <li><a href="{{ route('admin.categories') }}" class="{{ request()->is('admin/categories*') ? 'active' : '' }}"><i class="fas fa-tags"></i> Categories</a></li>
            <li><a href="{{ route('admin.brands') }}" class="{{ request()->is('admin/brands*') ? 'active' : '' }}"><i class="fas fa-copyright"></i> Brands</a></li>
            <li><a href="{{ route('admin.blog') }}" class="{{ request()->is('admin/blog*') ? 'active' : '' }}"><i class="fas fa-newspaper"></i> Blog/News</a></li>
            <li><a href="{{ route('admin.matches') }}" class="{{ request()->is('admin/matches*') ? 'active' : '' }}"><i class="fas fa-trophy"></i> Matches/Tourney</a></li>
            <li><a href="{{ route('admin.teams') }}" class="{{ request()->is('admin/teams*') ? 'active' : '' }}"><i class="fas fa-users-cog"></i> Teams</a></li>
            <li><a href="{{ route('admin.rankings') }}" class="{{ request()->is('admin/rankings*') ? 'active' : '' }}"><i class="fas fa-list-ol"></i> Standings</a></li>
            <li><a href="{{ route('admin.comments') }}" class="{{ request()->is('admin/comments*') ? 'active' : '' }}"><i class="fas fa-comments"></i> Comments</a></li>
            <li><a href="{{ route('admin.reviews') }}" class="{{ request()->is('admin/reviews*') ? 'active' : '' }}"><i class="fas fa-star"></i> Reviews</a></li>
            <li><a href="{{ route('admin.orders') }}" class="{{ request()->is('admin/orders*') ? 'active' : '' }}"><i class="fas fa-shopping-cart"></i> Orders</a></li>
            <li><a href="{{ route('admin.contacts') }}" class="{{ request()->is('admin/contacts*') ? 'active' : '' }}"><i class="fas fa-envelope"></i> Contacts</a></li>
            <li><a href="{{ route('admin.coupons') }}" class="{{ request()->is('admin/coupons*') ? 'active' : '' }}"><i class="fas fa-ticket-alt"></i> Coupons</a></li>
            <li><a href="{{ route('admin.slides') }}" class="{{ request()->is('admin/slides*') ? 'active' : '' }}"><i class="fas fa-images"></i> Slides</a></li>
            <li><a href="{{ route('admin.users') }}" class="{{ request()->is('admin/users*') ? 'active' : '' }}"><i class="fas fa-users"></i> Users</a></li>
            <li><a href="{{ route('admin.settings') }}" class="{{ request()->is('admin/settings*') ? 'active' : '' }}"><i class="fas fa-cog"></i> Settings</a></li>
            <li><a href="{{ route('admin.newsletters') }}" class="{{ request()->is('admin/newsletters*') ? 'active' : '' }}"><i class="fas fa-envelope-open-text"></i> Newsletters</a></li>
            <li><a href="{{ url('/') }}" target="_blank"><i class="fas fa-external-link-alt"></i> Public Site</a></li>
        </ul>
        <div style="padding: 20px; border-top: 1px solid rgba(255,255,255,0.1);">
             <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" style="color: rgba(255,255,255,0.7); text-decoration: none; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
        </div>
    </aside>

    <main class="admin-main">
        <header class="admin-header">
            <h1 style="margin:0;">@yield('title')</h1>
            <div style="display: flex; align-items: center; gap: 15px;">
                <span style="font-weight: 600;">{{ Auth::user()->username }}</span>
                <i class="fas fa-user-circle" style="font-size: 2rem; color: var(--admin-sidebar);"></i>
            </div>
        </header>

        @yield('content')
    </main>
    @yield('scripts')
</body>
</html>
