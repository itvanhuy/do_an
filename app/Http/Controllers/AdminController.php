<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    // Middleware handled in routes/web.php

    public function dashboard()
    {
        $totalUsers = DB::table('users')->count();
        $totalProducts = DB::table('products')->count();
        $totalOrders = DB::table('orders')->count();
        
        $today = date('Y-m-d');
        $dailyOrders = DB::table('orders')->whereDate('created_at', $today)->count();
        
        $currentMonth = date('Y-m');
        $monthlyRevenue = DB::table('orders')
            ->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$currentMonth])
            ->whereIn('status', ['delivered', 'shipped'])
            ->sum('total');

        $recentOrders = DB::table('orders')
            ->leftJoin('users', 'orders.user_id', '=', 'users.id')
            ->select('orders.*', 'users.username')
            ->orderBy('orders.created_at', 'desc')
            ->limit(10)
            ->get();

        $topProducts = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select('products.name', 'products.price', DB::raw('SUM(order_items.quantity) as sold'))
            ->groupBy('products.id', 'products.name', 'products.price')
            ->orderBy('sold', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers', 
            'totalProducts', 
            'totalOrders', 
            'dailyOrders',
            'monthlyRevenue', 
            'recentOrders', 
            'topProducts'
        ));
    }

    public function products()
    {
        $products = DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('products.*', 'categories.name as category_name')
            ->orderBy('products.created_at', 'desc')
            ->paginate(15);

        return view('admin.products', compact('products'));
    }

    public function orders()
    {
        $orders = DB::table('orders')
            ->leftJoin('users', 'orders.user_id', '=', 'users.id')
            ->select('orders.*', 'users.username')
            ->orderBy('orders.created_at', 'desc')
            ->paginate(15);

        return view('admin.orders', compact('orders'));
    }

    public function productsCreate()
    {
        $categories = DB::table('categories')->orderBy('name')->get();
        $brands = DB::table('brands')->orderBy('name')->get();
        return view('admin.products.create', compact('categories', 'brands'));
    }

    public function productsStore(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'category_id' => 'required'
        ]);

        $imageName = 'default.jpg';
        if ($request->hasFile('image')) {
            $imageName = time().'.'.$request->image->extension();
            $request->image->move(public_path('img/product'), $imageName);
        }

        DB::table('products')->insert([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'discount' => $request->discount ?? 0,
            'category_id' => $request->category_id,
            'brand' => $request->brand,
            'stock_quantity' => $request->stock_quantity ?? 0,
            'image' => $imageName,
            'created_at' => now(),
        ]);

        return redirect()->route('admin.products')->with('success', 'Product created successfully.');
    }

    public function productsEdit($id)
    {
        $product = DB::table('products')->where('id', $id)->first();
        if (!$product) abort(404);
        
        $categories = DB::table('categories')->orderBy('name')->get();
        $brands = DB::table('brands')->orderBy('name')->get();
        
        return view('admin.products.edit', compact('product', 'categories', 'brands'));
    }

    public function productsUpdate(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'category_id' => 'required'
        ]);

        $updateData = [
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'discount' => $request->discount ?? 0,
            'category_id' => $request->category_id,
            'brand' => $request->brand,
            'stock_quantity' => $request->stock_quantity ?? 0,
        ];

        if ($request->hasFile('image')) {
            $imageName = time().'.'.$request->image->extension();
            $request->image->move(public_path('img/product'), $imageName);
            $updateData['image'] = $imageName;
        }

        DB::table('products')->where('id', $id)->update($updateData);

        return redirect()->route('admin.products')->with('success', 'Product updated successfully.');
    }

    public function productsDestroy($id)
    {
        DB::table('products')->where('id', $id)->delete();
        return redirect()->route('admin.products')->with('success', 'Product deleted successfully.');
    }

    public function users()
    {
        $users = DB::table('users')->orderBy('created_at', 'desc')->paginate(15);
        return view('admin.users', compact('users'));
    }

    public function usersDestroy($id)
    {
        if ($id == Auth::id()) {
            return back()->with('error', 'You cannot delete yourself.');
        }
        DB::table('users')->where('id', $id)->delete();
        return back()->with('success', 'User deleted successfully.');
    }

    // Brands Management
    public function brands()
    {
        $brands = DB::table('brands')->orderBy('name', 'asc')->get();
        return view('admin.brands', compact('brands'));
    }

    public function brandsStore(Request $request)
    {
        $request->validate(['name' => 'required|unique:brands,name']);
        $logoName = '';
        if ($request->hasFile('logo')) {
            $logoName = 'brand_'.time().'.'.$request->logo->extension();
            $request->logo->move(public_path('img/brands'), $logoName);
        }
        DB::table('brands')->insert([
            'name' => $request->name, 
            'logo' => $logoName, 
            'created_at' => now(),
            'updated_at' => now()
        ]);
        return back()->with('success', 'Brand added successfully.');
    }

    public function brandsDestroy($id)
    {
        DB::table('brands')->where('id', $id)->delete();
        return back()->with('success', 'Brand deleted successfully.');
    }

    // Categories Management
    public function categories()
    {
        $categories = DB::table('categories')->orderBy('name', 'asc')->get();
        return view('admin.categories', compact('categories'));
    }

    public function categoriesStore(Request $request)
    {
        $request->validate(['name' => 'required']);
        DB::table('categories')->insert(['name' => $request->name, 'slug' => \Illuminate\Support\Str::slug($request->name), 'description' => $request->description]);
        return back()->with('success', 'Category added successfully.');
    }

    public function categoriesDestroy($id)
    {
        DB::table('categories')->where('id', $id)->delete();
        return back()->with('success', 'Category deleted successfully.');
    }

    // Blog Management
    public function blog()
    {
        $posts = DB::table('posts')->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.blog.index', compact('posts'));
    }

    public function blogCreate()
    {
        return view('admin.blog.create');
    }

    public function blogStore(Request $request)
    {
        $request->validate(['title' => 'required', 'content' => 'required']);
        $imageName = '';
        if ($request->hasFile('image')) {
            $imageName = 'post_'.time().'.'.$request->image->extension();
            $request->image->move(public_path('img'), $imageName);
        }
        DB::table('posts')->insert([
            'title' => $request->title,
            'excerpt' => $request->excerpt,
            'content' => $request->content,
            'image' => $imageName,
            'status' => $request->status ?? 'published',
            'post_type' => $request->post_type ?? 'news',
            'author_id' => Auth::id(),
            'created_at' => now(),
        ]);
        return redirect()->route('admin.blog')->with('success', 'Post created successfully.');
    }

    public function blogEdit($id)
    {
        $post = DB::table('posts')->where('id', $id)->first();
        if (!$post) abort(404);
        return view('admin.blog.edit', compact('post'));
    }

    public function blogUpdate(Request $request, $id)
    {
        $request->validate(['title' => 'required', 'content' => 'required']);
        $updateData = [
            'title' => $request->title,
            'excerpt' => $request->excerpt,
            'content' => $request->content,
            'status' => $request->status,
            'post_type' => $request->post_type,
        ];
        if ($request->hasFile('image')) {
            $imageName = 'post_'.time().'.'.$request->image->extension();
            $request->image->move(public_path('img'), $imageName);
            $updateData['image'] = $imageName;
        }
        DB::table('posts')->where('id', $id)->update($updateData);
        return redirect()->route('admin.blog')->with('success', 'Post updated successfully.');
    }

    public function blogDestroy($id)
    {
        DB::table('posts')->where('id', $id)->delete();
        return redirect()->route('admin.blog')->with('success', 'Post deleted successfully.');
    }

    // Matches Management
    public function matches()
    {
        $matches = DB::table('matches')->orderBy('match_time', 'desc')->paginate(15);
        return view('admin.matches.index', compact('matches'));
    }

    public function matchesCreate()
    {
        return view('admin.matches.create');
    }

    public function matchesStore(Request $request)
    {
        $request->validate(['team1_name' => 'required', 'team2_name' => 'required', 'match_time' => 'required']);
        $team1Logo = '';
        if ($request->hasFile('team1_logo')) {
            $team1Logo = 'team_1_'.time().'.'.$request->team1_logo->extension();
            $request->team1_logo->move(public_path('img/teams'), $team1Logo);
        }
        $team2Logo = '';
        if ($request->hasFile('team2_logo')) {
            $team2Logo = 'team_2_'.time().'.'.$request->team2_logo->extension();
            $request->team2_logo->move(public_path('img/teams'), $team2Logo);
        }
        DB::table('matches')->insert([
            'tournament_name' => $request->tournament_name,
            'game_type' => $request->game_type,
            'team1_name' => $request->team1_name,
            'team1_logo' => $team1Logo,
            'team2_name' => $request->team2_name,
            'team2_logo' => $team2Logo,
            'match_time' => $request->match_time,
            'status' => $request->status,
            'score_team1' => $request->score_team1 ?? 0,
            'score_team2' => $request->score_team2 ?? 0,
            'stream_link' => $request->stream_link,
            'created_at' => now(),
        ]);
        return redirect()->route('admin.matches')->with('success', 'Match added successfully.');
    }

    public function matchesEdit($id)
    {
        $match = DB::table('matches')->where('id', $id)->first();
        if (!$match) abort(404);
        return view('admin.matches.edit', compact('match'));
    }

    public function matchesUpdate(Request $request, $id)
    {
        $request->validate(['team1_name' => 'required', 'team2_name' => 'required', 'match_time' => 'required']);
        $updateData = [
            'tournament_name' => $request->tournament_name,
            'game_type' => $request->game_type,
            'team1_name' => $request->team1_name,
            'team2_name' => $request->team2_name,
            'match_time' => $request->match_time,
            'status' => $request->status,
            'score_team1' => $request->score_team1,
            'score_team2' => $request->score_team2,
            'stream_link' => $request->stream_link,
        ];
        if ($request->hasFile('team1_logo')) {
            $team1Logo = 'team_1_'.time().'.'.$request->team1_logo->extension();
            $request->team1_logo->move(public_path('img/teams'), $team1Logo);
            $updateData['team1_logo'] = $team1Logo;
        }
        if ($request->hasFile('team2_logo')) {
            $team2Logo = 'team_2_'.time().'.'.$request->team2_logo->extension();
            $request->team2_logo->move(public_path('img/teams'), $team2Logo);
            $updateData['team2_logo'] = $team2Logo;
        }
        DB::table('matches')->where('id', $id)->update($updateData);
        return redirect()->route('admin.matches')->with('success', 'Match updated successfully.');
    }

    public function matchesDestroy($id)
    {
        DB::table('matches')->where('id', $id)->delete();
        return redirect()->route('admin.matches')->with('success', 'Match deleted successfully.');
    }

    // Rankings Management
    public function rankings()
    {
        $rankings = DB::table('team_rankings')->orderBy('game_type')->orderBy('rank_position')->get();
        return view('admin.rankings.index', compact('rankings'));
    }

    public function rankingsStore(Request $request)
    {
        $request->validate(['team_name' => 'required', 'game_type' => 'required', 'rank_position' => 'required']);
        $logoName = '';
        if ($request->hasFile('team_logo')) {
            $logoName = 'rank_'.time().'.'.$request->team_logo->extension();
            $request->team_logo->move(public_path('img/teams'), $logoName);
        }
        DB::table('team_rankings')->insert([
            'team_name' => $request->team_name,
            'team_logo' => $logoName,
            'game_type' => $request->game_type,
            'tournament_name' => $request->tournament_name ?? 'General',
            'rank_position' => $request->rank_position,
            'wins' => $request->wins ?? 0,
            'losses' => $request->losses ?? 0,
        ]);
        return back()->with('success', 'Ranking added successfully.');
    }

    public function rankingsDestroy($id)
    {
        DB::table('team_rankings')->where('id', $id)->delete();
        return back()->with('success', 'Ranking deleted successfully.');
    }

    public function updateOrderStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required']);
        DB::table('orders')->where('id', $id)->update(['status' => $request->status]);
        return back()->with('success', 'Order status updated successfully.');
    }

    // Teams Management
    public function teams()
    {
        $teams = DB::table('teams')->orderBy('name', 'asc')->get();
        return view('admin.teams.index', compact('teams'));
    }

    public function teamsStore(Request $request)
    {
        $request->validate(['name' => 'required']);
        $logoName = '';
        if ($request->hasFile('logo')) {
            $logoName = 'team_'.time().'.'.$request->logo->extension();
            $request->logo->move(public_path('img/teams'), $logoName);
        }
        DB::table('teams')->insert([
            'name' => $request->name,
            'logo' => $logoName,
            'game_type' => $request->game_type,
            'tournament_name' => $request->tournament_name,
            'created_at' => now()
        ]);
        return back()->with('success', 'Team added successfully.');
    }

    public function teamsDestroy($id)
    {
        DB::table('teams')->where('id', $id)->delete();
        return back()->with('success', 'Team deleted successfully.');
    }

    // Comments Management
    public function comments()
    {
        $comments = DB::table('comments')
            ->leftJoin('users', 'comments.user_id', '=', 'users.id')
            ->leftJoin('posts', 'comments.post_id', '=', 'posts.id')
            ->select('comments.*', 'users.username', 'posts.title as post_title')
            ->orderBy('comments.created_at', 'desc')
            ->paginate(15);
        return view('admin.comments.index', compact('comments'));
    }

    public function commentsDestroy($id)
    {
        DB::table('comments')->where('id', $id)->delete();
        return back()->with('success', 'Comment deleted successfully.');
    }

    // Reviews Management
    public function reviews()
    {
        $reviews = DB::table('reviews')
            ->leftJoin('users', 'reviews.user_id', '=', 'users.id')
            ->leftJoin('products', 'reviews.product_id', '=', 'products.id')
            ->select('reviews.*', 'users.username', 'products.name as product_name', 'products.image as product_image')
            ->orderBy('reviews.created_at', 'desc')
            ->paginate(15);
        return view('admin.reviews.index', compact('reviews'));
    }

    public function reviewsApprove($id)
    {
        DB::table('reviews')->where('id', $id)->update(['status' => 'approved']);
        return back()->with('success', 'Review approved successfully.');
    }

    public function reviewsDestroy($id)
    {
        DB::table('reviews')->where('id', $id)->delete();
        return back()->with('success', 'Review deleted successfully.');
    }

    // Settings
    public function settings()
    {
        return view('admin.settings');
    }

    public function settingsUpdate(Request $request)
    {
        // For now, just simulate saving settings
        return back()->with('success', 'Settings updated successfully.');
    }

    // Newsletter Subscribers
    public function newsletterSubscribers()
    {
        $subscribers = DB::table('newsletters')->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.newsletters', compact('subscribers'));
    }

    public function newsletterDestroy($id)
    {
        DB::table('newsletters')->where('id', $id)->delete();
        return back()->with('success', 'Subscriber removed successfully.');
    }
}
