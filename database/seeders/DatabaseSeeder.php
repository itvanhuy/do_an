<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Admin & Customer accounts
        \Illuminate\Support\Facades\DB::table('users')->insert([
            [
                'username' => 'admin',
                'full_name' => 'TechShop Admin',
                'email' => 'admin@gmail.com',
                'password' => \Illuminate\Support\Facades\Hash::make('123456'),
                'role' => 'admin',
                'phone' => '0896492400',
                'address' => 'TechShop Headquarters, HCM City',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'customer',
                'full_name' => 'John Doe',
                'email' => 'customer@gmail.com',
                'password' => \Illuminate\Support\Facades\Hash::make('123456'),
                'role' => 'user',
                'phone' => '0987654321',
                'address' => '123 Street No. 1, District 1, HCM City',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'huygaming',
                'full_name' => 'Lê Văn Huy',
                'email' => 'huygaming@gmail.com',
                'password' => \Illuminate\Support\Facades\Hash::make('123456'),
                'role' => 'user',
                'phone' => '0123456789',
                'address' => 'Da Nang City',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        // 2. Product Categories
        $categories = [
            ['id' => 1, 'name' => 'Laptop', 'slug' => 'laptop', 'description' => 'High-end laptops and notebooks'],
            ['id' => 2, 'name' => 'Phone', 'slug' => 'phone', 'description' => 'Latest flagship smartphones'],
            ['id' => 3, 'name' => 'Audio', 'slug' => 'audio', 'description' => 'Headphones, earbuds and speakers'],
            ['id' => 4, 'name' => 'Accessories', 'slug' => 'accessories', 'description' => 'Mice, keyboards and more'],
            ['id' => 5, 'name' => 'Monitors', 'slug' => 'monitors', 'description' => 'Gaming and creative displays']
        ];
        \Illuminate\Support\Facades\DB::table('categories')->insert($categories);

        // 3. Brands
        $brands = [
            ['name' => 'Apple', 'logo' => ''],
            ['name' => 'Samsung', 'logo' => ''],
            ['name' => 'Sony', 'logo' => ''],
            ['name' => 'Asus', 'logo' => ''],
            ['name' => 'Logitech', 'logo' => ''],
            ['name' => 'Dell', 'logo' => ''],
            ['name' => 'LG', 'logo' => '']
        ];
        \Illuminate\Support\Facades\DB::table('brands')->insert($brands);

        // 4. Sample Products
        $products = [
            // Laptops
            [
                'id' => 1, 'name' => 'MacBook Pro M3 14-inch', 'category_id' => 1, 'brand' => 'Apple',
                'price' => 39990000, 'discount' => 5, 'stock_quantity' => 15, 'sold' => 12,
                'image' => 'default.jpg', 'description' => 'Powerful Apple M3 chip.',
                'is_featured' => 1, 'is_new' => 1, 'created_at' => now()
            ],
            [
                'id' => 2, 'name' => 'Asus ROG Zephyrus G14', 'category_id' => 1, 'brand' => 'Asus',
                'price' => 42000000, 'discount' => 10, 'stock_quantity' => 5, 'sold' => 8,
                'image' => 'default.jpg', 'description' => 'The ultimate gaming laptop.',
                'is_featured' => 1, 'is_new' => 0, 'created_at' => now()->subDays(5)
            ],
            [
                'id' => 3, 'name' => 'Dell XPS 15 Oled', 'category_id' => 1, 'brand' => 'Dell',
                'price' => 45000000, 'discount' => 0, 'stock_quantity' => 8, 'sold' => 20,
                'image' => 'default.jpg', 'description' => 'Thin and light high-performance laptop.',
                'is_featured' => 1, 'is_new' => 0, 'created_at' => now()->subDays(12)
            ],
            // Phones
            [
                'id' => 4, 'name' => 'iPhone 15 Pro Max 256GB', 'category_id' => 2, 'brand' => 'Apple',
                'price' => 29500000, 'discount' => 0, 'stock_quantity' => 50, 'sold' => 150,
                'image' => 'default.jpg', 'description' => 'Super lightweight Titanium design.',
                'is_featured' => 1, 'is_new' => 1, 'created_at' => now()
            ],
            [
                'id' => 5, 'name' => 'Samsung Galaxy S24 Ultra', 'category_id' => 2, 'brand' => 'Samsung',
                'price' => 31000000, 'discount' => 15, 'stock_quantity' => 20, 'sold' => 45,
                'image' => 'default.jpg', 'description' => 'Samsung Galaxy AI beast.',
                'is_featured' => 0, 'is_new' => 0, 'created_at' => now()->subDays(10)
            ],
            [
                'id' => 6, 'name' => 'iPhone 14 128GB', 'category_id' => 2, 'brand' => 'Apple',
                'price' => 18000000, 'discount' => 5, 'stock_quantity' => 100, 'sold' => 210,
                'image' => 'default.jpg', 'description' => 'The popular choice.',
                'is_featured' => 0, 'is_new' => 0, 'created_at' => now()->subDays(40)
            ],
            // Audio
            [
                'id' => 7, 'name' => 'Sony WH-1000XM5 Headphones', 'category_id' => 3, 'brand' => 'Sony',
                'price' => 7500000, 'discount' => 5, 'stock_quantity' => 30, 'sold' => 10,
                'image' => 'default.jpg', 'description' => 'Industry-leading noise cancellation.',
                'is_featured' => 1, 'is_new' => 0, 'created_at' => now()->subDays(2)
            ],
            [
                'id' => 8, 'name' => 'AirPods Pro 2', 'category_id' => 3, 'brand' => 'Apple',
                'price' => 6000000, 'discount' => 12, 'stock_quantity' => 60, 'sold' => 55,
                'image' => 'default.jpg', 'description' => 'Advanced wireless noise cancelling.',
                'is_featured' => 0, 'is_new' => 1, 'created_at' => now()->subDays(1)
            ],
            [
                'id' => 9, 'name' => 'Marshall Stanmore III Bluetooth Speaker', 'category_id' => 3, 'brand' => 'Logitech',
                'price' => 9500000, 'discount' => 8, 'stock_quantity' => 12, 'sold' => 5,
                'image' => 'default.jpg', 'description' => 'Classic Marshall sound and design.',
                'is_featured' => 1, 'is_new' => 0, 'created_at' => now()->subDays(20)
            ],
            // Accessories
            [
                'id' => 10, 'name' => 'Logitech G Pro X Superlight Mouse', 'category_id' => 4, 'brand' => 'Logitech',
                'price' => 2990000, 'discount' => 0, 'stock_quantity' => 45, 'sold' => 80,
                'image' => 'default.jpg', 'description' => 'The lightest eSport mouse.',
                'is_featured' => 1, 'is_new' => 1, 'created_at' => now()
            ],
            [
                'id' => 11, 'name' => 'Asus ROG Strix Flare II Keyboard', 'category_id' => 4, 'brand' => 'Asus',
                'price' => 4500000, 'discount' => 15, 'stock_quantity' => 25, 'sold' => 12,
                'image' => 'default.jpg', 'description' => 'Premium mechanical gaming keyboard.',
                'is_featured' => 0, 'is_new' => 0, 'created_at' => now()->subDays(15)
            ],
            // Monitors
            [
                'id' => 12, 'name' => 'LG UltraGear 27 inch 165Hz Monitor', 'category_id' => 5, 'brand' => 'LG',
                'price' => 6500000, 'discount' => 10, 'stock_quantity' => 20, 'sold' => 30,
                'image' => 'default.jpg', 'description' => 'High refresh rate gaming monitor.',
                'is_featured' => 1, 'is_new' => 1, 'created_at' => now()
            ],
            [
                'id' => 13, 'name' => 'Dell UltraSharp U2723QE 4K Monitor', 'category_id' => 5, 'brand' => 'Dell',
                'price' => 14000000, 'discount' => 0, 'stock_quantity' => 10, 'sold' => 5,
                'image' => 'default.jpg', 'description' => 'Professional color-accurate display.',
                'is_featured' => 0, 'is_new' => 0, 'created_at' => now()->subDays(30)
            ]
        ];
        \Illuminate\Support\Facades\DB::table('products')->insert($products);

        // 5. Slides Banner
        \Illuminate\Support\Facades\DB::table('slides')->insert([
            [
                'title' => 'Grand Opening Sale',
                'image' => 'slide1.jpg',
                'link' => '/shop',
                'is_active' => 1,
                'sort_order' => 1,
                'created_at' => now()
            ],
            [
                'title' => 'May Laptop Super Sale',
                'image' => 'slide2.jpg',
                'link' => '/category/laptop',
                'is_active' => 1,
                'sort_order' => 2,
                'created_at' => now()
            ]
        ]);

        // 6. Coupons
        \Illuminate\Support\Facades\DB::table('coupons')->insert([
            [
                'code' => 'TECHSHOP2026',
                'discount_type' => 'percent',
                'discount_value' => 10,
                'min_order' => 0,
                'max_uses' => 100,
                'used_count' => 5,
                'is_active' => 1,
                'expires_at' => now()->addDays(30),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'SAVE100K',
                'discount_type' => 'fixed',
                'discount_value' => 100000,
                'min_order' => 500000,
                'max_uses' => 50,
                'used_count' => 2,
                'is_active' => 1,
                'expires_at' => now()->addDays(15),
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        // 7. Blog Posts
        \Illuminate\Support\Facades\DB::table('posts')->insert([
            [
                'title' => 'MacBook Pro M3 Full Review',
                'excerpt' => 'Is the new Apple M3 chip worth the upgrade? Let’s find out.',
                'content' => '<p>Apple recently released the M3 chip series. While starting at a high price, the rendering and AI performance is outstanding...</p>',
                'status' => 'published',
                'image' => 'default.jpg',
                'post_type' => 'news',
                'author_id' => 1,
                'created_at' => now()->subDays(2)
            ],
            [
                'title' => 'Top 5 FPS Gaming Mice in 2026',
                'excerpt' => 'If you’re into CS2 or Valorant, you cannot miss this list.',
                'content' => '<p>Logitech G Pro X Superlight remains the top choice, but new competitors are rising...</p>',
                'status' => 'published',
                'image' => 'default.jpg',
                'post_type' => 'news',
                'author_id' => 1,
                'created_at' => now()->subDays(5)
            ],
            [
                'title' => 'Summer Esports Event 2026 Begins',
                'excerpt' => 'The International League of Legends tournament returns with a new format.',
                'content' => '<p>Get ready for the biggest teams in the LoL scene...</p>',
                'status' => 'published',
                'image' => 'default.jpg',
                'post_type' => 'news',
                'author_id' => 1,
                'created_at' => now()->subDays(1)
            ]
        ]);

        // 8. Sample Orders
        $orders = [
            [
                'id' => 1, 'user_id' => 2, 'total' => 37990500, 
                'shipping_address' => 'John Doe | 0987654321 | 123 Street No. 1, District 1',
                'payment_method' => 'cod', 'status' => 'delivered', 'created_at' => now()->subDays(2)
            ],
            [
                'id' => 2, 'user_id' => 3, 'total' => 29530000, 
                'shipping_address' => 'Lê Văn Huy | 0123456789 | Da Nang City',
                'payment_method' => 'vnpay', 'status' => 'pending', 'created_at' => now()
            ],
            [
                'id' => 3, 'user_id' => 2, 'total' => 2990000, 
                'shipping_address' => 'John Doe | 0987654321 | 123 Street No. 1',
                'payment_method' => 'cod', 'status' => 'processing', 'created_at' => now()->subDays(1)
            ]
        ];
        \Illuminate\Support\Facades\DB::table('orders')->insert($orders);

        // Order Items
        \Illuminate\Support\Facades\DB::table('order_items')->insert([
            ['order_id' => 1, 'product_id' => 1, 'quantity' => 1, 'price' => 37990500],
            ['order_id' => 2, 'product_id' => 4, 'quantity' => 1, 'price' => 29500000],
            ['order_id' => 3, 'product_id' => 10, 'quantity' => 1, 'price' => 2990000]
        ]);

        // 9. Reviews
        \Illuminate\Support\Facades\DB::table('reviews')->insert([
            ['user_id' => 2, 'product_id' => 1, 'rating' => 5, 'comment' => 'Very smooth, super fast rendering. Great packaging!', 'status' => 'approved', 'created_at' => now()->subDays(1)],
            ['user_id' => 3, 'product_id' => 1, 'rating' => 4, 'comment' => 'A bit expensive but worth every penny.', 'status' => 'approved', 'created_at' => now()->subHours(10)],
            ['user_id' => 2, 'product_id' => 10, 'rating' => 5, 'comment' => 'Super light mouse. Perfect for FPS gaming!', 'status' => 'approved', 'created_at' => now()->subHours(2)],
        ]);

        // 10. Tournament & Rankings
        \Illuminate\Support\Facades\DB::table('team_rankings')->insert([
            ['team_name' => 'T1 Esports', 'game_type' => 'League of Legends', 'points' => 120, 'rank_position' => 1, 'wins' => 15, 'losses' => 2],
            ['team_name' => 'Gen.G', 'game_type' => 'League of Legends', 'points' => 105, 'rank_position' => 2, 'wins' => 13, 'losses' => 4],
            ['team_name' => 'GAM Esports', 'game_type' => 'League of Legends', 'points' => 80, 'rank_position' => 3, 'wins' => 10, 'losses' => 5],
            ['team_name' => 'NAVI', 'game_type' => 'CS:GO 2', 'points' => 95, 'rank_position' => 1, 'wins' => 12, 'losses' => 3],
            ['team_name' => 'FaZe Clan', 'game_type' => 'CS:GO 2', 'points' => 90, 'rank_position' => 2, 'wins' => 11, 'losses' => 4],
        ]);

        \Illuminate\Support\Facades\DB::table('matches')->insert([
            [
                'tournament_name' => 'Worlds 2026 Upper Bracket',
                'game_type' => 'League of Legends',
                'team1_name' => 'T1 Esports',
                'team1_logo' => 'default.jpg',
                'team2_name' => 'Gen.G',
                'team2_logo' => 'default.jpg',
                'match_time' => now()->addDays(2),
                'status' => 'upcoming',
                'score_team1' => 0,
                'score_team2' => 0,
                'stream_link' => 'https://twitch.tv/riotgames',
                'created_at' => now()
            ],
            [
                'tournament_name' => 'VCS Summer',
                'game_type' => 'League of Legends',
                'team1_name' => 'GAM Esports',
                'team1_logo' => 'default.jpg',
                'team2_name' => 'Team Whales',
                'team2_logo' => 'default.jpg',
                'match_time' => now()->subDays(1),
                'status' => 'completed',
                'score_team1' => 3,
                'score_team2' => 1,
                'stream_link' => 'https://youtube.com',
                'created_at' => now()
            ]
        ]);
    }
}
