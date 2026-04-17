@extends('layouts.app')

@section('title', 'About Us - '.env('APP_NAME', 'TechShop'))

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/about.css') }}">
    <style>
        .about-container { max-width: 1200px; margin: 0 auto; padding: 40px 20px; }
        .about-hero { text-align: center; padding: 80px 0; background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); color: white; border-radius: 20px; margin-bottom: 60px; position: relative; overflow: hidden; }
        .hero-stats { display: flex; justify-content: center; gap: 40px; margin-top: 40px; flex-wrap: wrap; }
        .stat-item { background: rgba(255, 255, 255, 0.1); padding: 25px; border-radius: 15px; min-width: 180px; backdrop-filter: blur(10px); }
        .stat-item h4 { font-size: 2rem; margin: 0 0 5px; color: var(--accent-color); }
        .section-divider { height: 1px; background: #eee; margin: 60px 0; }
        .story-section, .mission-section { max-width: 800px; margin: 0 auto; line-height: 1.8; }
        .story-section h2, .mission-section h2 { display: flex; align-items: center; gap: 15px; margin-bottom: 25px; }
        .section-img { width: 100%; border-radius: 15px; margin: 30px 0; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .products-grid, .team-grid, .values-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px; margin-top: 30px; }
        .card { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.03); transition: 0.3s; height: 100%; }
        .card:hover { transform: translateY(-10px); box-shadow: 0 15px 35px rgba(0,0,0,0.1); }
        .member-card img { width: 120px; height: 120px; border-radius: 50%; object-fit: cover; margin-bottom: 20px; }
        .timeline { position: relative; max-width: 800px; margin: 40px auto; padding-left: 30px; border-left: 3px solid var(--accent-color); }
        .timeline-item { margin-bottom: 30px; position: relative; }
        .timeline-item::before { content: ''; position: absolute; left: -39px; top: 5px; width: 15px; height: 15px; background: white; border: 3px solid var(--accent-color); border-radius: 50%; }
    </style>
@endsection

@section('content')
<main class="about-container">
    <section class="about-hero">
        <h1>About TechShop</h1>
        <p style="max-width: 700px; margin: 20px auto 40px; font-size: 1.1rem; opacity: 0.9;">Discover our story and mission to bring exceptional technology experiences. TechShop proudly stands as a leading destination for genuine tech products and dedicated customer service.</p>
        <div class="hero-stats">
            <div class="stat-item">
                <i class="fas fa-users" style="font-size: 1.5rem; margin-bottom: 10px;"></i>
                <h4>10,000+</h4>
                <p>Trusted Customers</p>
            </div>
            <div class="stat-item">
                <i class="fas fa-box" style="font-size: 1.5rem; margin-bottom: 10px;"></i>
                <h4>5,000+</h4>
                <p>Products Sold</p>
            </div>
            <div class="stat-item">
                <i class="fas fa-award" style="font-size: 1.5rem; margin-bottom: 10px;"></i>
                <h4>10+</h4>
                <p>Awards Won</p>
            </div>
        </div>
    </section>

    <section class="story-section">
        <h2><i class="fas fa-book-open" style="color: var(--accent-color);"></i> Our Story</h2>
        <p>Founded by a group of young tech enthusiasts, TechShop has continuously evolved through every project and product. We’ve overcome challenges to build a trusted brand and deliver real value to our customers.</p>
        <p>At TechShop, technology is not just about products — it's a bridge that connects people and enhances daily life.</p>
        <img src="{{ asset('img/congty.jpg') }}" alt="TechShop HQ" class="section-img" onerror="this.src='/api/placeholder/1200/600'">
    </section>

    <div class="section-divider"></div>

    <section class="mission-section">
        <h2><i class="fas fa-rocket" style="color: var(--accent-color);"></i> Our Mission</h2>
        <ul style="list-style: none; padding: 0;">
            <li style="margin-bottom: 15px;"><i class="fas fa-check-circle" style="color: #4CAF50; margin-right: 10px;"></i> Deliver authentic tech products at the most competitive prices.</li>
            <li style="margin-bottom: 15px;"><i class="fas fa-check-circle" style="color: #4CAF50; margin-right: 10px;"></i> Provide fast, caring, and professional customer support.</li>
            <li style="margin-bottom: 15px;"><i class="fas fa-check-circle" style="color: #4CAF50; margin-right: 10px;"></i> Continuously innovate to create a convenient shopping experience.</li>
            <li style="margin-bottom: 15px;"><i class="fas fa-check-circle" style="color: #4CAF50; margin-right: 10px;"></i> Build a dynamic and creative young tech community.</li>
        </ul>
    </section>

    <div class="section-divider"></div>

    <section class="team-section">
        <h2 style="text-align:center;"><i class="fas fa-user-friends"></i> Meet the Founders</h2>
        <div class="team-grid">
            <div class="card member-card" style="text-align:center;">
                <img src="{{ asset('img/le_van_huy.png') }}" alt="Le Van Huy" onerror="this.src='/api/placeholder/150/150'">
                <h4>Le Van Huy</h4>
                <p style="color: var(--accent-color); font-weight: bold;">CEO & Founder</p>
                <p style="font-size: 0.9rem; color: #666;">Software expert and technology strategy leader.</p>
            </div>
            <div class="card member-card" style="text-align:center;">
                <img src="{{ asset('img/A_vi_trieu.jpg') }}" alt="A Vi Trieu" onerror="this.src='/api/placeholder/150/150'">
                <h4>A Vi Trieu</h4>
                <p style="color: var(--accent-color); font-weight: bold;">Co-Founder</p>
                <p style="font-size: 0.9rem; color: #666;">Tech enthusiast and co-leader in platform development.</p>
            </div>
            <div class="card member-card" style="text-align:center;">
                <img src="{{ asset('img/hovanhoang.jpg') }}" alt="Ho Van Hoang" onerror="this.src='/api/placeholder/150/150'">
                <h4>Ho Van Hoang</h4>
                <p style="color: var(--accent-color); font-weight: bold;">Co-Founder</p>
                <p style="font-size: 0.9rem; color: #666;">Software engineer passionate about innovative solutions.</p>
            </div>
        </div>
    </section>

    <div class="section-divider"></div>

    <section class="history-section">
        <h2 style="text-align:center;"><i class="fas fa-history"></i> Our Journey</h2>
        <div class="timeline">
            <div class="timeline-item">
                <strong>2025</strong> – TechShop was founded by 5 passionate creators.
            </div>
            <div class="timeline-item">
                <strong>2026</strong> – Expanded to online retail with a dedicated shopping app.
            </div>
            <div class="timeline-item">
                <strong>2027</strong> – Integrated AI & Cloud for enhanced customer experience.
            </div>
            <div class="timeline-item">
                <strong>2028</strong> – Nationwide support with modern offline stores.
            </div>
        </div>
    </section>

    <section class="contact-section" style="text-align:center; padding: 60px 0;">
        <h2>Connect with Us</h2>
        <p style="margin-bottom: 30px; color: #666;">Always ready to support you on your tech journey. TechShop is here to help, anytime.</p>
        <a href="https://www.facebook.com/huylv.2k3" target="_blank" class="btn" style="background: var(--accent-color); color: white; padding: 15px 40px; border-radius: 30px; text-decoration: none; font-weight: bold; font-size: 1.1rem; box-shadow: 0 10px 20px rgba(255, 71, 87, 0.3);">Contact TechShop</a>
    </section>
</main>
@endsection
