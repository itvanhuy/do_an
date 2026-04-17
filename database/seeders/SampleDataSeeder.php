<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // ─── 1. EXTRA USERS (keep existing 4, add more for reviews/orders) ───
        $newUsers = [];
        $userEmails = [
            ['username' => 'john_gamer',   'email' => 'john@example.com',   'full_name' => 'John Nguyen',   'phone' => '0901234567', 'address' => '123 Le Loi, District 1, HCMC'],
            ['username' => 'sarah_tech',   'email' => 'sarah@example.com',  'full_name' => 'Sarah Tran',    'phone' => '0912345678', 'address' => '456 Nguyen Hue, District 1, HCMC'],
            ['username' => 'mike_pro',     'email' => 'mike@example.com',   'full_name' => 'Mike Pham',     'phone' => '0923456789', 'address' => '789 Hai Ba Trung, District 3, HCMC'],
            ['username' => 'lisa_stream',  'email' => 'lisa@example.com',   'full_name' => 'Lisa Le',       'phone' => '0934567890', 'address' => '101 Vo Van Tan, District 3, HCMC'],
            ['username' => 'david_cs',     'email' => 'david@example.com',  'full_name' => 'David Hoang',   'phone' => '0945678901', 'address' => '202 Pham Ngu Lao, District 1, HCMC'],
            ['username' => 'emma_lol',     'email' => 'emma@example.com',   'full_name' => 'Emma Vo',       'phone' => '0956789012', 'address' => '303 Cach Mang Thang 8, District 10, HCMC'],
        ];

        foreach ($userEmails as $u) {
            $exists = DB::table('users')->where('email', $u['email'])->exists();
            if (!$exists) {
                $newUsers[] = array_merge($u, [
                    'password'   => Hash::make('password123'),
                    'role'       => 'user',
                    'is_active'  => 1,
                    'created_at' => $now->copy()->subDays(rand(5, 30)),
                    'updated_at' => $now,
                ]);
            }
        }
        if (!empty($newUsers)) {
            DB::table('users')->insert($newUsers);
        }

        // Get all user IDs for references
        $allUserIds  = DB::table('users')->pluck('id')->toArray();
        $adminId     = DB::table('users')->where('role', 'admin')->value('id') ?? $allUserIds[0];
        $customerIds = DB::table('users')->where('role', 'user')->pluck('id')->toArray();

        // Get existing category/brand IDs
        $categoryMap = DB::table('categories')->pluck('id', 'slug')->toArray();
        $brandNames  = DB::table('brands')->pluck('name')->toArray();

        // ─── 2. PRODUCTS (30 realistic gaming/tech products) ───────────────
        if (DB::table('products')->count() === 0) {
            $products = [
                // Laptops (category: laptops)
                ['name' => 'ASUS ROG Strix G16 Gaming Laptop',       'description' => 'Intel Core i9-13980HX, NVIDIA RTX 4070, 16GB DDR5, 1TB SSD, 16" QHD 240Hz display. Built for serious gamers who demand top-tier performance and stunning visuals.', 'price' => 45990000, 'category_id' => $categoryMap['laptops'], 'brand' => 'Asus',   'stock_quantity' => 15, 'discount' => 10.00, 'is_active' => 1],
                ['name' => 'Acer Predator Helios 16',                'description' => 'Intel Core i7-13700HX, NVIDIA RTX 4060, 32GB DDR5, 512GB SSD, 16" WQXGA 165Hz. A powerhouse laptop for gaming and creative workflows.', 'price' => 38990000, 'category_id' => $categoryMap['laptops'], 'brand' => 'Acer',   'stock_quantity' => 20, 'discount' => 5.00,  'is_active' => 1],
                ['name' => 'Dell Alienware m16 R2',                  'description' => 'Intel Core Ultra 9 185H, RTX 4080, 32GB DDR5, 1TB SSD, 16" QHD+ 240Hz. Premium gaming laptop with Alienware Cryo-Tech cooling technology.', 'price' => 62990000, 'category_id' => $categoryMap['laptops'], 'brand' => 'Dell',   'stock_quantity' => 8,  'discount' => 0.00,  'is_active' => 1],
                ['name' => 'Razer Blade 16 (2024)',                  'description' => 'Intel Core i9-14900HX, RTX 4090, 32GB DDR5, 2TB SSD, 16" UHD+ 120Hz OLED. The ultimate gaming laptop with 4K OLED brilliance.', 'price' => 89990000, 'category_id' => $categoryMap['laptops'], 'brand' => 'Razer',  'stock_quantity' => 5,  'discount' => 0.00,  'is_active' => 1],
                ['name' => 'HP Omen Transcend 16',                   'description' => 'Intel Core i7-14700HX, RTX 4070, 16GB DDR5, 1TB SSD, 16" 2.5K 240Hz mini-LED display. Slim yet powerful gaming machine.', 'price' => 42990000, 'category_id' => $categoryMap['laptops'], 'brand' => 'HP',     'stock_quantity' => 12, 'discount' => 8.00,  'is_active' => 1],
                ['name' => 'Lenovo Legion Pro 7i Gen 9',             'description' => 'Intel Core i9-14900HX, RTX 4080, 32GB DDR5, 1TB SSD, 16" WQXGA 240Hz IPS. Legion Coldfront Hyper for maximum thermal performance.', 'price' => 55990000, 'category_id' => $categoryMap['laptops'], 'brand' => 'Lenovo', 'stock_quantity' => 10, 'discount' => 12.00, 'is_active' => 1],

                // Mice (category: mice)
                ['name' => 'Razer DeathAdder V3 Pro',                'description' => 'Ultra-lightweight 63g wireless ergonomic mouse, Focus Pro 30K optical sensor, 90-hour battery, HyperSpeed wireless. The gold standard for FPS gaming.', 'price' => 3290000,  'category_id' => $categoryMap['mice'],    'brand' => 'Razer',  'stock_quantity' => 50, 'discount' => 0.00,  'is_active' => 1],
                ['name' => 'Logitech G Pro X Superlight 2',          'description' => '60g ultra-lightweight wireless gaming mouse, HERO 2 sensor 44K DPI, 95-hour battery, LIGHTSPEED wireless. Pro-level precision.', 'price' => 3490000,  'category_id' => $categoryMap['mice'],    'brand' => 'Razer',  'stock_quantity' => 40, 'discount' => 15.00, 'is_active' => 1],
                ['name' => 'ASUS ROG Harpe Ace Aim Lab Edition',     'description' => '54g ultra-lightweight wireless mouse, ROG AimPoint Pro 42K DPI sensor, 90-hour battery. Co-developed with Aim Lab for perfect precision.', 'price' => 2990000,  'category_id' => $categoryMap['mice'],    'brand' => 'Asus',   'stock_quantity' => 35, 'discount' => 5.00,  'is_active' => 1],
                ['name' => 'Razer Viper V3 HyperSpeed',              'description' => '82g wireless gaming mouse, Focus Pro 35K sensor, 280-hour battery life, mechanical switches. Built for esports dominance.', 'price' => 2490000,  'category_id' => $categoryMap['mice'],    'brand' => 'Razer',  'stock_quantity' => 30, 'discount' => 0.00,  'is_active' => 1],
                ['name' => 'Dell Alienware Pro Wireless Mouse',      'description' => '60g ultra-light ambidextrous wireless mouse, 26K DPI sensor, 120-hour battery. AlienFX per-key RGB lighting.', 'price' => 2890000,  'category_id' => $categoryMap['mice'],    'brand' => 'Dell',   'stock_quantity' => 25, 'discount' => 10.00, 'is_active' => 1],

                // Keyboards (category: keyboards)
                ['name' => 'Razer Huntsman V3 Pro TKL',              'description' => 'Analog optical switch keyboard, adjustable actuation 0.1-4.0mm, magnetic wrist rest, PBT keycaps. Next-gen competitive advantage.', 'price' => 5490000,  'category_id' => $categoryMap['keyboards'], 'brand' => 'Razer',  'stock_quantity' => 20, 'discount' => 0.00,  'is_active' => 1],
                ['name' => 'ASUS ROG Azoth Extreme',                 'description' => 'Wireless 75% mechanical keyboard, ROG NX mechanical switches, OLED display, gasket mount, aluminum CNC body with hot-swappable switches.', 'price' => 7490000,  'category_id' => $categoryMap['keyboards'], 'brand' => 'Asus',   'stock_quantity' => 15, 'discount' => 5.00,  'is_active' => 1],
                ['name' => 'Razer BlackWidow V4 Pro 75%',            'description' => 'Hot-swappable mechanical keyboard with Razer Orange switches, command dial, Chroma RGB, magnetic palm rest.', 'price' => 4990000,  'category_id' => $categoryMap['keyboards'], 'brand' => 'Razer',  'stock_quantity' => 25, 'discount' => 10.00, 'is_active' => 1],
                ['name' => 'HP HyperX Alloy Origins 65',             'description' => 'Compact 65% mechanical keyboard, HyperX Red linear switches, aircraft-grade aluminum body, USB-C detachable cable.', 'price' => 2190000,  'category_id' => $categoryMap['keyboards'], 'brand' => 'HP',     'stock_quantity' => 40, 'discount' => 0.00,  'is_active' => 1],
                ['name' => 'Lenovo Legion K500 RGB',                 'description' => 'Full-size mechanical keyboard, Kailh Red switches, per-key RGB, detachable palm rest. Solid build for gaming and typing.', 'price' => 1890000,  'category_id' => $categoryMap['keyboards'], 'brand' => 'Lenovo', 'stock_quantity' => 35, 'discount' => 15.00, 'is_active' => 1],

                // Monitors (category: monitors)
                ['name' => 'ASUS ROG Swift PG27AQN',                 'description' => '27" 1440p 360Hz Esports IPS monitor, 1ms GTG, G-Sync, HDR600. The fastest 1440p gaming monitor ever made.', 'price' => 23990000, 'category_id' => $categoryMap['monitors'],  'brand' => 'Asus',   'stock_quantity' => 10, 'discount' => 5.00,  'is_active' => 1],
                ['name' => 'Dell Alienware AW3225QF',                'description' => '32" 4K QD-OLED curved gaming monitor, 240Hz, 0.03ms, HDR True Black 400, Dolby Vision, G-Sync. Stunning visual experience.', 'price' => 29990000, 'category_id' => $categoryMap['monitors'],  'brand' => 'Dell',   'stock_quantity' => 7,  'discount' => 0.00,  'is_active' => 1],
                ['name' => 'Acer Predator X27U F3',                  'description' => '27" WQHD OLED gaming monitor, 480Hz, 0.03ms, AMD FreeSync Premium Pro, HDR True Black 400. Record-breaking refresh rate.', 'price' => 25990000, 'category_id' => $categoryMap['monitors'],  'brand' => 'Acer',   'stock_quantity' => 12, 'discount' => 8.00,  'is_active' => 1],
                ['name' => 'HP Omen 27qs QD-OLED',                   'description' => '27" 1440p QD-OLED gaming monitor, 240Hz, 0.03ms, DisplayHDR True Black 400, AMD FreeSync Premium Pro.', 'price' => 17990000, 'category_id' => $categoryMap['monitors'],  'brand' => 'HP',     'stock_quantity' => 18, 'discount' => 10.00, 'is_active' => 1],
                ['name' => 'Lenovo Legion Y27qf-30',                 'description' => '27" 1440p IPS gaming monitor, 240Hz, 0.5ms MPRT, 95% DCI-P3, AMD FreeSync Premium. Exceptional value for competitive gaming.', 'price' => 8990000,  'category_id' => $categoryMap['monitors'],  'brand' => 'Lenovo', 'stock_quantity' => 25, 'discount' => 12.00, 'is_active' => 1],

                // Headphones (category: headphones)
                ['name' => 'Razer BlackShark V2 Pro (2024)',         'description' => 'Wireless esports headset, TriForce Titanium 50mm drivers, HyperClear Super Wideband mic, 70-hour battery. THX Spatial Audio.', 'price' => 4490000,  'category_id' => $categoryMap['headphones'], 'brand' => 'Razer',  'stock_quantity' => 30, 'discount' => 0.00,  'is_active' => 1],
                ['name' => 'ASUS ROG Delta S Wireless',              'description' => 'Dual-mode wireless gaming headset, ESS Quad DAC, 50mm ASUS Essence drivers, 25-hour battery, AI noise canceling mic.', 'price' => 4990000,  'category_id' => $categoryMap['headphones'], 'brand' => 'Asus',   'stock_quantity' => 20, 'discount' => 5.00,  'is_active' => 1],
                ['name' => 'Dell Alienware AW920H Tri-Mode',        'description' => 'Wireless gaming headset, 40mm drivers, Dolby Atmos, ANC, 55-hour battery, Bluetooth 5.3 + 2.4GHz + 3.5mm.', 'price' => 3990000,  'category_id' => $categoryMap['headphones'], 'brand' => 'Dell',   'stock_quantity' => 22, 'discount' => 8.00,  'is_active' => 1],
                ['name' => 'HP HyperX Cloud III Wireless',           'description' => 'Wireless gaming headset, 53mm drivers with DTS Headphone:X, 120-hour battery, detachable noise-canceling mic.', 'price' => 3290000,  'category_id' => $categoryMap['headphones'], 'brand' => 'HP',     'stock_quantity' => 35, 'discount' => 10.00, 'is_active' => 1],
                ['name' => 'Lenovo Legion H600 Wireless',            'description' => '50mm drivers, 7.1 surround sound, 2.4GHz wireless, noise-canceling boom mic, 20-hour battery, memory foam ear cushions.', 'price' => 1890000,  'category_id' => $categoryMap['headphones'], 'brand' => 'Lenovo', 'stock_quantity' => 40, 'discount' => 0.00,  'is_active' => 1],
                ['name' => 'Razer Kraken V4 Pro',                    'description' => 'Wireless RGB gaming headset, Razer TriForce Bio-Cellulose 40mm drivers, haptic feedback, interchangeable ear cushions, Razer Synapse.', 'price' => 6490000,  'category_id' => $categoryMap['headphones'], 'brand' => 'Razer',  'stock_quantity' => 15, 'discount' => 0.00,  'is_active' => 1],
            ];

            foreach ($products as &$p) {
                $p['image']      = null;
                $p['created_at'] = $now->copy()->subDays(rand(1, 60));
                $p['updated_at'] = $now;
            }
            unset($p);
            DB::table('products')->insert($products);
        }

        $productIds = DB::table('products')->pluck('id')->toArray();

        // ─── 3. BLOG POSTS (news & guides) ────────────────────────────────
        if (DB::table('posts')->count() === 0) {
            $posts = [
                [
                    'title'     => 'Top 10 Gaming Laptops of 2026: Ultimate Buying Guide',
                    'excerpt'   => 'We tested and ranked the best gaming laptops available right now, from budget-friendly to no-compromise performance beasts.',
                    'content'   => '<h2>Best Gaming Laptops 2026</h2><p>The gaming laptop market has never been more competitive. With the latest Intel 14th Gen and AMD Ryzen 9000 series processors paired with NVIDIA RTX 40-series GPUs, performance is at an all-time high.</p><h3>1. Razer Blade 16</h3><p>The Razer Blade 16 continues to set the standard for premium gaming laptops. Its 4K OLED display is simply breathtaking, and the RTX 4090 handles anything you throw at it.</p><h3>2. ASUS ROG Strix G16</h3><p>Best value for money in the high-performance segment. The 240Hz QHD display and i9 processor deliver exceptional gaming experiences.</p><h3>3. Dell Alienware m16 R2</h3><p>Alienware\'s Cryo-Tech cooling keeps the m16 running cool under pressure, making it perfect for extended gaming sessions.</p><p>Whether you\'re a competitive esports player or a casual gamer, there\'s a perfect laptop waiting for you in our roundup.</p>',
                    'post_type' => 'news',
                    'views'     => 2450,
                ],
                [
                    'title'     => 'VALORANT Champions 2026: Everything You Need to Know',
                    'excerpt'   => 'The biggest VALORANT tournament of the year is approaching. Here\'s the complete guide to teams, schedule, and what to expect.',
                    'content'   => '<h2>VALORANT Champions 2026</h2><p>The VALORANT Champions Tour 2026 is shaping up to be the most exciting season yet. With new maps, agent updates, and fierce competition, here\'s everything you need to know.</p><h3>Key Teams to Watch</h3><p><strong>Sentinels:</strong> After their dominant run in Masters, they\'re the favorites heading into Champions.</p><p><strong>Fnatic:</strong> The European powerhouse has been consistently strong throughout the season.</p><p><strong>DRX:</strong> The Korean squad is looking to defend their title with renewed vigor.</p><h3>Tournament Format</h3><p>16 teams will compete in a double-elimination bracket over three weeks. Prize pool: $2,000,000.</p>',
                    'post_type' => 'news',
                    'views'     => 5230,
                ],
                [
                    'title'     => 'How to Build the Perfect Gaming Setup on a Budget',
                    'excerpt'   => 'You don\'t need to spend a fortune to get a great gaming experience. Here\'s our step-by-step guide to building an amazing setup under 20 million VND.',
                    'content'   => '<h2>Budget Gaming Setup Guide</h2><p>Building a gaming setup doesn\'t have to break the bank. With smart choices, you can get incredible performance without overspending.</p><h3>Monitor: Lenovo Legion Y27qf-30</h3><p>At under 9 million VND, this 1440p 240Hz monitor punches way above its weight class.</p><h3>Keyboard: Lenovo Legion K500 RGB</h3><p>Mechanical switches, per-key RGB, and a solid build — all under 2 million VND.</p><h3>Mouse: Razer Viper V3 HyperSpeed</h3><p>Wireless gaming mouse with a 280-hour battery at 2.49 million VND is an absolute steal.</p><h3>Headset: Lenovo Legion H600 Wireless</h3><p>7.1 surround sound and great comfort for under 2 million VND.</p>',
                    'post_type' => 'news',
                    'views'     => 1820,
                ],
                [
                    'title'     => 'League of Legends Worlds 2026: Group Stage Predictions',
                    'excerpt'   => 'Our analysts break down the group stage matchups and predict which teams will advance to the knockout rounds.',
                    'content'   => '<h2>Worlds 2026 Group Stage Analysis</h2><p>The League of Legends World Championship returns with 24 teams battling for the Summoner\'s Cup.</p><h3>Group A</h3><p><strong>T1</strong> enters as the defending champions. Faker continues to defy age expectations with his legendary mid-lane play. Gen.G will be their biggest challenge in this group.</p><h3>Group B</h3><p>G2 Esports leads the European charge with a roster that has dominated the LEC. Team Liquid represents North America with their strongest roster in years.</p><h3>Bold Predictions</h3><p>We predict an all-LCK final between T1 and Gen.G, with T1 taking their 5th world championship title.</p>',
                    'post_type' => 'news',
                    'views'     => 8750,
                ],
                [
                    'title'     => 'Mechanical Keyboard Guide: Switches, Layouts, and What Matters',
                    'excerpt'   => 'Everything you need to know about mechanical keyboards — from switch types to form factors to custom builds.',
                    'content'   => '<h2>The Complete Mechanical Keyboard Guide</h2><h3>Switch Types</h3><p><strong>Linear (Red):</strong> Smooth keystroke with no tactile bump. Best for gaming due to fast actuation.</p><p><strong>Tactile (Brown):</strong> A noticeable bump at actuation point. Great balance for gaming and typing.</p><p><strong>Clicky (Blue):</strong> Audible click with tactile bump. Satisfying but can be loud for shared spaces.</p><h3>Form Factors</h3><p><strong>Full-size (100%):</strong> Includes numpad. Best for productivity.</p><p><strong>TKL (80%):</strong> No numpad. More desk space for mouse movement.</p><p><strong>75%:</strong> Compact with function row. The sweet spot for most gamers.</p><p><strong>65%:</strong> Ultra-compact. Arrow keys but no function row.</p>',
                    'post_type' => 'news',
                    'views'     => 3410,
                ],
                [
                    'title'     => 'NVIDIA RTX 5090 Leaked Specs: What We Know So Far',
                    'excerpt'   => 'The next generation of NVIDIA graphics cards is coming. Here are all the leaked specs, release date rumors, and performance predictions.',
                    'content'   => '<h2>RTX 5090: Next-Gen Gaming Performance</h2><p>NVIDIA\'s next-generation Blackwell architecture is expected to bring massive improvements to both gaming and AI workloads.</p><h3>Leaked Specifications</h3><p><strong>GPU:</strong> GB202 Blackwell architecture</p><p><strong>CUDA Cores:</strong> 21,760 (rumored)</p><p><strong>VRAM:</strong> 32GB GDDR7</p><p><strong>Memory Bus:</strong> 512-bit</p><p><strong>TDP:</strong> 600W</p><h3>Expected Performance</h3><p>Based on leaked benchmarks, the RTX 5090 could deliver up to 70% more rasterization performance and 2x the ray-tracing performance compared to the RTX 4090.</p><h3>Release Date</h3><p>Industry insiders suggest a Q1 2026 announcement with availability by Q2 2026.</p>',
                    'post_type' => 'news',
                    'views'     => 12300,
                ],
                [
                    'title'     => 'Best Gaming Headsets for Competitive FPS in 2026',
                    'excerpt'   => 'Audio positioning can make or break your gameplay. We tested the top headsets for competitive shooters like VALORANT and CS2.',
                    'content'   => '<h2>Top Gaming Headsets for FPS</h2><p>In competitive FPS games, hearing footsteps and gunshots accurately can be the difference between winning and losing. Here are our top picks.</p><h3>1. Razer BlackShark V2 Pro (2024)</h3><p>The gold standard for esports audio. THX Spatial Audio provides incredible positional accuracy, and the 70-hour battery means you won\'t be interrupted mid-match.</p><h3>2. ASUS ROG Delta S Wireless</h3><p>The ESS Quad DAC delivers audiophile-grade sound quality that\'s rare in gaming headsets. AI noise canceling keeps your comms crystal clear.</p><h3>3. HP HyperX Cloud III Wireless</h3><p>DTS Headphone:X and 120-hour battery life make this an incredible value proposition. The 53mm drivers deliver punchy bass without muddying the mids.</p>',
                    'post_type' => 'news',
                    'views'     => 4560,
                ],
                [
                    'title'     => 'The Rise of Esports in Vietnam: A Growing Industry',
                    'excerpt'   => 'Vietnam\'s esports scene has exploded in recent years. From League of Legends to VALORANT, here\'s how the country is becoming a global esports powerhouse.',
                    'content'   => '<h2>Vietnam\'s Esports Boom</h2><p>Vietnam has rapidly become one of the most important esports markets in Southeast Asia. With a young, tech-savvy population and growing infrastructure, the industry is thriving.</p><h3>Popular Games</h3><p><strong>League of Legends:</strong> The VCS (Vietnam Championship Series) attracts millions of viewers per season.</p><p><strong>VALORANT:</strong> Vietnam\'s VALORANT scene has produced several internationally competitive teams.</p><p><strong>Mobile Legends & Free Fire:</strong> Mobile esports dominate in terms of player count.</p><h3>Investment & Growth</h3><p>Major brands like TechShop are investing in esports through team sponsorships and tournament hosting, helping grow the ecosystem. The Vietnamese government has also recognized esports as an official sport.</p>',
                    'post_type' => 'news',
                    'views'     => 6890,
                ],
            ];

            foreach ($posts as &$post) {
                $post['image']      = null;
                $post['author_id']  = $adminId;
                $post['status']     = 'published';
                $post['created_at'] = $now->copy()->subDays(rand(1, 45));
                $post['updated_at'] = $now;
            }
            unset($post);
            DB::table('posts')->insert($posts);
        }

        $postIds = DB::table('posts')->pluck('id')->toArray();

        // ─── 4. COMMENTS on posts ─────────────────────────────────────────
        if (DB::table('comments')->count() === 0 && !empty($postIds) && !empty($customerIds)) {
            $commentTexts = [
                'Great article! Very informative and well-written.',
                'Thanks for the detailed breakdown, really helped me make my decision.',
                'I\'ve been looking for exactly this kind of comparison. Appreciated!',
                'The budget setup guide is spot on. Just ordered the Lenovo monitor!',
                'Can\'t wait for the RTX 5090. My wallet is ready!',
                'T1 is definitely winning Worlds again. Faker is the GOAT.',
                'I switched to the Razer DeathAdder V3 Pro and my aim improved instantly.',
                'This is why I love TechShop — always staying on top of the latest news.',
                'The OLED monitors are game-changers. No going back to IPS.',
                'Vietnam\'s esports scene has grown so much. Proud to be part of it!',
                'Excellent review. Would love to see more keyboard content.',
                'Just bought the ASUS ROG Azoth Extreme based on this article. No regrets!',
            ];

            $comments = [];
            foreach ($postIds as $postId) {
                $numComments = rand(1, 3);
                $usedUsers = [];
                for ($i = 0; $i < $numComments; $i++) {
                    $userId = $customerIds[array_rand($customerIds)];
                    if (in_array($userId, $usedUsers)) continue;
                    $usedUsers[] = $userId;
                    $comments[] = [
                        'post_id'    => $postId,
                        'user_id'    => $userId,
                        'content'    => $commentTexts[array_rand($commentTexts)],
                        'created_at' => $now->copy()->subDays(rand(0, 20)),
                        'updated_at' => $now,
                    ];
                }
            }
            if (!empty($comments)) {
                DB::table('comments')->insert($comments);
            }
        }

        // ─── 5. PRODUCT REVIEWS ────────────────────────────────────────────
        if (DB::table('reviews')->count() === 0 && !empty($productIds) && !empty($customerIds)) {
            $reviewTexts = [
                5 => [
                    'Absolutely amazing product! Exceeded all my expectations. Highly recommended!',
                    'Best purchase I\'ve made this year. Quality is outstanding.',
                    'Worth every penny. Performance is incredible and build quality is top-notch.',
                    'Perfect for gaming. Couldn\'t be happier with my purchase.',
                ],
                4 => [
                    'Great product overall. Minor nitpicks but nothing deal-breaking.',
                    'Very good quality for the price. Solid performance in games.',
                    'Really enjoying this product. Minor software issues but hardware is excellent.',
                    'Good value. Does everything I need and more.',
                ],
                3 => [
                    'Decent product. Does the job but nothing spectacular.',
                    'Average experience. Some pros and cons to consider.',
                    'It\'s okay for the price. Expected a bit more from this brand.',
                ],
                2 => [
                    'Below expectations. Quality could be better at this price point.',
                    'Not impressed. Had some issues right out of the box.',
                ],
                1 => [
                    'Disappointed with the purchase. Returning this product.',
                ],
            ];

            $reviews = [];
            // Give roughly 60% of products some reviews
            $reviewedProducts = array_slice($productIds, 0, (int)(count($productIds) * 0.7));
            shuffle($reviewedProducts);

            foreach ($reviewedProducts as $productId) {
                $numReviews = rand(1, 4);
                $usedUsers = [];
                for ($i = 0; $i < $numReviews; $i++) {
                    $userId = $customerIds[array_rand($customerIds)];
                    if (in_array($userId, $usedUsers)) continue;
                    $usedUsers[] = $userId;
                    // Weight towards higher ratings
                    $ratingWeights = [5, 5, 5, 4, 4, 4, 4, 3, 3, 2];
                    $rating = $ratingWeights[array_rand($ratingWeights)];
                    $texts = $reviewTexts[$rating];
                    $reviews[] = [
                        'product_id' => $productId,
                        'user_id'    => $userId,
                        'rating'     => $rating,
                        'comment'    => $texts[array_rand($texts)],
                        'status'     => 'approved',
                        'created_at' => $now->copy()->subDays(rand(0, 30)),
                        'updated_at' => $now,
                    ];
                }
            }
            if (!empty($reviews)) {
                DB::table('reviews')->insert($reviews);
            }
        }

        // ─── 6. ORDERS & ORDER ITEMS ───────────────────────────────────────
        if (DB::table('orders')->where('id', '>', 1)->count() === 0 && !empty($productIds) && !empty($customerIds)) {
            $statuses = ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'delivered', 'delivered'];
            $paymentMethods = ['cod', 'cod', 'bank_transfer', 'momo'];
            $addresses = [
                '123 Le Loi, District 1, Ho Chi Minh City',
                '456 Nguyen Hue, District 1, Ho Chi Minh City',
                '789 Hai Ba Trung, District 3, Ho Chi Minh City',
                '101 Vo Van Tan, District 3, Ho Chi Minh City',
                '55 Pham Ngoc Thach, District 3, Ho Chi Minh City',
                '202 Cach Mang Thang 8, District 10, Ho Chi Minh City',
                '300 Dien Bien Phu, Binh Thanh District, Ho Chi Minh City',
            ];

            for ($o = 0; $o < 12; $o++) {
                $orderDate = $now->copy()->subDays(rand(1, 40));
                $userId = $customerIds[array_rand($customerIds)];

                // Pick 1-3 products for each order
                $numItems = rand(1, 3);
                $orderProducts = array_rand(array_flip($productIds), min($numItems, count($productIds)));
                if (!is_array($orderProducts)) $orderProducts = [$orderProducts];

                $total = 0;
                $items = [];
                foreach ($orderProducts as $pid) {
                    $product = DB::table('products')->where('id', $pid)->first();
                    if (!$product) continue;
                    // Keep qty=1 for expensive items (>20M) to avoid decimal overflow
                    $qty = $product->price > 20000000 ? 1 : rand(1, 2);
                    $price = $product->price * (1 - $product->discount / 100);
                    $lineTotal = $price * $qty;
                    // Cap total at 90M to stay within decimal(10,2)
                    if ($total + $lineTotal > 90000000) continue;
                    $total += $lineTotal;
                    $items[] = [
                        'product_id' => $pid,
                        'quantity'   => $qty,
                        'price'      => $price,
                        'created_at' => $orderDate,
                        'updated_at' => $orderDate,
                    ];
                }

                $orderId = DB::table('orders')->insertGetId([
                    'user_id'          => $userId,
                    'total'            => $total,
                    'status'           => $statuses[array_rand($statuses)],
                    'shipping_address' => $addresses[array_rand($addresses)],
                    'payment_method'   => $paymentMethods[array_rand($paymentMethods)],
                    'created_at'       => $orderDate,
                    'updated_at'       => $orderDate,
                ]);

                foreach ($items as &$item) {
                    $item['order_id'] = $orderId;
                }
                unset($item);
                DB::table('order_items')->insert($items);
            }
        }

        // ─── 7. MATCHES (esports) ─────────────────────────────────────────
        if (DB::table('matches')->count() === 0) {
            $matches = [
                ['game_type' => 'League of Legends', 'tournament_name' => 'Worlds 2026',       'team1_name' => 'T1',           'team2_name' => 'Gen.G',        'match_time' => $now->copy()->addDays(3)->setTime(19, 0),  'score_team1' => null, 'score_team2' => null, 'status' => 'upcoming'],
                ['game_type' => 'League of Legends', 'tournament_name' => 'Worlds 2026',       'team1_name' => 'G2 Esports',   'team2_name' => 'Team Liquid',  'match_time' => $now->copy()->addDays(5)->setTime(20, 0),  'score_team1' => null, 'score_team2' => null, 'status' => 'upcoming'],
                ['game_type' => 'VALORANT',          'tournament_name' => 'Champions 2026',     'team1_name' => 'Sentinels',    'team2_name' => 'Fnatic',       'match_time' => $now->copy()->addHours(2),                  'score_team1' => null, 'score_team2' => null, 'status' => 'live'],
                ['game_type' => 'VALORANT',          'tournament_name' => 'Champions 2026',     'team1_name' => 'DRX',          'team2_name' => 'Paper Rex',    'match_time' => $now->copy()->addDays(1)->setTime(18, 0),  'score_team1' => null, 'score_team2' => null, 'status' => 'upcoming'],
                ['game_type' => 'CS2',               'tournament_name' => 'IEM Katowice 2026',  'team1_name' => 'Natus Vincere','team2_name' => 'FaZe Clan',    'match_time' => $now->copy()->subDays(2)->setTime(21, 0),  'score_team1' => 2,    'score_team2' => 1,    'status' => 'finished'],
                ['game_type' => 'CS2',               'tournament_name' => 'IEM Katowice 2026',  'team1_name' => 'Vitality',     'team2_name' => 'G2 Esports',   'match_time' => $now->copy()->subDays(1)->setTime(19, 30), 'score_team1' => 0,    'score_team2' => 2,    'status' => 'finished'],
                ['game_type' => 'League of Legends', 'tournament_name' => 'LCK Spring 2026',    'team1_name' => 'T1',           'team2_name' => 'DRX',          'match_time' => $now->copy()->subDays(5)->setTime(17, 0),  'score_team1' => 2,    'score_team2' => 0,    'status' => 'finished'],
                ['game_type' => 'VALORANT',          'tournament_name' => 'VCT Pacific 2026',   'team1_name' => 'Paper Rex',    'team2_name' => 'Gen.G',        'match_time' => $now->copy()->subDays(3)->setTime(20, 0),  'score_team1' => 1,    'score_team2' => 2,    'status' => 'finished'],
                ['game_type' => 'League of Legends', 'tournament_name' => 'MSI 2026',           'team1_name' => 'Gen.G',        'team2_name' => 'G2 Esports',   'match_time' => $now->copy()->addDays(7)->setTime(18, 0),  'score_team1' => null, 'score_team2' => null, 'status' => 'upcoming'],
                ['game_type' => 'CS2',               'tournament_name' => 'BLAST Premier 2026', 'team1_name' => 'FaZe Clan',    'team2_name' => 'Vitality',     'match_time' => $now->copy()->addDays(2)->setTime(22, 0),  'score_team1' => null, 'score_team2' => null, 'status' => 'upcoming'],
            ];

            foreach ($matches as &$m) {
                $m['team1_logo']  = null;
                $m['team2_logo']  = null;
                $m['stream_link'] = 'https://twitch.tv/eslcs';
                $m['created_at']  = $now;
                $m['updated_at']  = $now;
            }
            unset($m);
            DB::table('matches')->insert($matches);
        }

        // ─── 8. ADDITIONAL TEAMS ──────────────────────────────────────────
        $existingTeams = DB::table('teams')->pluck('name')->toArray();
        $newTeams = [
            ['name' => 'Sentinels',     'game_type' => 'valorant',          'tournament_name' => 'VCT Americas'],
            ['name' => 'Fnatic',        'game_type' => 'valorant',          'tournament_name' => 'VCT EMEA'],
            ['name' => 'DRX',           'game_type' => 'valorant',          'tournament_name' => 'VCT Pacific'],
            ['name' => 'Paper Rex',     'game_type' => 'valorant',          'tournament_name' => 'VCT Pacific'],
            ['name' => 'Natus Vincere', 'game_type' => 'cs2',              'tournament_name' => 'IEM Katowice'],
            ['name' => 'FaZe Clan',     'game_type' => 'cs2',              'tournament_name' => 'IEM Katowice'],
            ['name' => 'Vitality',      'game_type' => 'cs2',              'tournament_name' => 'BLAST Premier'],
            ['name' => 'Cloud9',        'game_type' => 'lol',              'tournament_name' => 'LCS'],
        ];
        $teamsToInsert = [];
        foreach ($newTeams as $t) {
            if (!in_array($t['name'], $existingTeams)) {
                $teamsToInsert[] = array_merge($t, [
                    'logo'       => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
        if (!empty($teamsToInsert)) {
            DB::table('teams')->insert($teamsToInsert);
        }

        // ─── 9. TEAM RANKINGS ──────────────────────────────────────────────
        $existingRankings = DB::table('team_rankings')->count();
        if ($existingRankings <= 1) {
            $rankings = [
                ['team_name' => 'T1',           'game_type' => 'lol',      'tournament_name' => 'Worlds 2026',       'rank_position' => 1, 'wins' => 28, 'losses' => 5],
                ['team_name' => 'Gen.G',        'game_type' => 'lol',      'tournament_name' => 'Worlds 2026',       'rank_position' => 2, 'wins' => 25, 'losses' => 8],
                ['team_name' => 'G2 Esports',   'game_type' => 'lol',      'tournament_name' => 'Worlds 2026',       'rank_position' => 3, 'wins' => 22, 'losses' => 10],
                ['team_name' => 'Team Liquid',   'game_type' => 'lol',      'tournament_name' => 'Worlds 2026',       'rank_position' => 4, 'wins' => 20, 'losses' => 12],
                ['team_name' => 'Sentinels',     'game_type' => 'valorant', 'tournament_name' => 'Champions 2026',    'rank_position' => 1, 'wins' => 18, 'losses' => 3],
                ['team_name' => 'Fnatic',        'game_type' => 'valorant', 'tournament_name' => 'Champions 2026',    'rank_position' => 2, 'wins' => 16, 'losses' => 5],
                ['team_name' => 'DRX',           'game_type' => 'valorant', 'tournament_name' => 'Champions 2026',    'rank_position' => 3, 'wins' => 15, 'losses' => 6],
                ['team_name' => 'Paper Rex',     'game_type' => 'valorant', 'tournament_name' => 'Champions 2026',    'rank_position' => 4, 'wins' => 14, 'losses' => 7],
                ['team_name' => 'Natus Vincere', 'game_type' => 'cs2',      'tournament_name' => 'IEM Katowice 2026', 'rank_position' => 1, 'wins' => 22, 'losses' => 4],
                ['team_name' => 'FaZe Clan',     'game_type' => 'cs2',      'tournament_name' => 'IEM Katowice 2026', 'rank_position' => 2, 'wins' => 20, 'losses' => 6],
                ['team_name' => 'Vitality',      'game_type' => 'cs2',      'tournament_name' => 'IEM Katowice 2026', 'rank_position' => 3, 'wins' => 18, 'losses' => 8],
            ];

            foreach ($rankings as &$r) {
                $r['team_logo']   = null;
                $r['created_at']  = $now;
                $r['updated_at']  = $now;
            }
            unset($r);
            DB::table('team_rankings')->insert($rankings);
        }

        // ─── 10. COUPONS ──────────────────────────────────────────────────
        if (DB::table('coupons')->count() === 0) {
            DB::table('coupons')->insert([
                ['code' => 'WELCOME10',   'discount_value' => 10.00, 'discount_type' => 'percent', 'min_order_amount' => 500000,    'expiry_date' => $now->copy()->addMonths(3)->toDateString(), 'created_at' => $now, 'updated_at' => $now],
                ['code' => 'GAMER20',     'discount_value' => 20.00, 'discount_type' => 'percent', 'min_order_amount' => 2000000,   'expiry_date' => $now->copy()->addMonths(2)->toDateString(), 'created_at' => $now, 'updated_at' => $now],
                ['code' => 'SAVE500K',    'discount_value' => 500000, 'discount_type' => 'fixed',  'min_order_amount' => 5000000,   'expiry_date' => $now->copy()->addMonths(1)->toDateString(), 'created_at' => $now, 'updated_at' => $now],
                ['code' => 'ESPORTS15',   'discount_value' => 15.00, 'discount_type' => 'percent', 'min_order_amount' => 1000000,   'expiry_date' => $now->copy()->addMonths(6)->toDateString(), 'created_at' => $now, 'updated_at' => $now],
                ['code' => 'FREESHIP',    'discount_value' => 100000, 'discount_type' => 'fixed',  'min_order_amount' => 0,          'expiry_date' => $now->copy()->addYear()->toDateString(),    'created_at' => $now, 'updated_at' => $now],
            ]);
        }

        // ─── 11. NEWSLETTER SUBSCRIBERS ────────────────────────────────────
        if (DB::table('newsletters')->count() === 0) {
            DB::table('newsletters')->insert([
                ['email' => 'gamer1@gmail.com',     'created_at' => $now->copy()->subDays(20), 'updated_at' => $now],
                ['email' => 'techfan@yahoo.com',    'created_at' => $now->copy()->subDays(15), 'updated_at' => $now],
                ['email' => 'esportslover@mail.com','created_at' => $now->copy()->subDays(10), 'updated_at' => $now],
                ['email' => 'pcbuilder@outlook.com','created_at' => $now->copy()->subDays(7),  'updated_at' => $now],
                ['email' => 'streamer99@gmail.com', 'created_at' => $now->copy()->subDays(3),  'updated_at' => $now],
                ['email' => 'proplayer@hotmail.com','created_at' => $now->copy()->subDays(1),  'updated_at' => $now],
                ['email' => 'setupguide@gmail.com', 'created_at' => $now,                       'updated_at' => $now],
                ['email' => 'dealshunter@mail.com', 'created_at' => $now,                       'updated_at' => $now],
            ]);
        }

        $this->command->info('✅ Sample data seeded successfully!');
        $this->command->info('   → Users: ' . DB::table('users')->count());
        $this->command->info('   → Products: ' . DB::table('products')->count());
        $this->command->info('   → Posts: ' . DB::table('posts')->count());
        $this->command->info('   → Comments: ' . DB::table('comments')->count());
        $this->command->info('   → Reviews: ' . DB::table('reviews')->count());
        $this->command->info('   → Orders: ' . DB::table('orders')->count());
        $this->command->info('   → Matches: ' . DB::table('matches')->count());
        $this->command->info('   → Teams: ' . DB::table('teams')->count());
        $this->command->info('   → Team Rankings: ' . DB::table('team_rankings')->count());
        $this->command->info('   → Coupons: ' . DB::table('coupons')->count());
        $this->command->info('   → Newsletters: ' . DB::table('newsletters')->count());
    }
}
