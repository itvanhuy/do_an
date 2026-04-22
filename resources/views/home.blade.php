@extends('layouts.app')

@section('title', 'Trang chủ - TechShop')

@section('styles')
  <link rel="stylesheet" href="{{ asset('css/home.css') }}">
  <style>
    /* Fix Responsive Grid */
    .grid { display: grid; gap: 30px; }
    .grid-3 { grid-template-columns: repeat(3, 1fr); }
    .grid-2 { grid-template-columns: repeat(2, 1fr); }
    @media (max-width: 992px) { .grid-3 { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 768px) {
        .grid-3, .grid-2 { grid-template-columns: 1fr; }
        .hero-content h1 { font-size: 1.8rem; }
        .hero-buttons { flex-direction: column; gap: 15px; }
        .newsletter-form { flex-direction: column; gap: 10px; }
        .newsletter-input, .newsletter-btn { width: 100%; border-radius: 5px; }
    }
    /* Căn giữa ảnh trong card */
    .card img { display: block; margin: 0 auto 15px; object-fit: contain; width: 100%; }
    /* Tournament Styles Update */
    .tournament-banner { position: relative; height: 100%; min-height: 300px; border-radius: 10px; overflow: hidden; }
    .tournament-banner img.main-banner { width: 100%; height: 100%; object-fit: cover; }
    .match-teams-overlay {
        position: absolute; bottom: 0; left: 0; width: 100%;
        background: linear-gradient(to top, rgba(0,0,0,0.9), transparent);
        padding: 20px; display: flex; justify-content: space-around; align-items: center; color: white;
    }
    .match-teams-overlay .team img {
        width: 60px; height: 60px; object-fit: contain;
        background: rgba(255,255,255,0.1); border-radius: 50%; padding: 5px; margin-bottom: 5px;
    }
    .countdown-timer { display: flex; gap: 15px; margin: 20px 0; }
    .time-box { background: #333; color: #fff; padding: 10px; border-radius: 8px; text-align: center; min-width: 60px; }
    .time-box span { display: block; font-size: 20px; font-weight: bold; color: #ff6b6b; }
  </style>
@endsection

@section('content')
  <section class="hero">
    <div class="hero-slider">
      <img src="{{ asset('img/slide1.png') }}" class="active" alt="Esports Slide 1" />
      @if(isset($slides) && $slides->count() > 0)
          @foreach($slides as $index => $slide)
              <img src="{{ asset('img/slides/' . $slide->image) }}" class="{{ $index == 0 ? 'active' : '' }}" alt="{{ $slide->title }}" />
          @endforeach
      @endif
      <button class="prev-btn">&#10094;</button>
      <button class="next-btn">&#10095;</button>
    </div>
    <div class="hero-content">
      <h1>Welcome to TECHSHOP – The World of Technology & eSports</h1>
      <p>Discover high-quality gaming gear, join thrilling tournaments, and connect with the gaming community.</p>
      <div class="hero-buttons">
        <a href="{{ url('shop') }}" class="btn shop-btn">Shop Now</a>
        <a href="{{ url('tournament') }}" class="btm tour-btn">View Tournament</a>
      </div>
    </div>
  </section>

  <section class="main-content">


    <!-- New Products -->
    <section class="featured-products enhanced">
      <h2>New Products</h2>
      <div class="grid grid-3">
        @foreach ($newProducts as $product)
            @include('components.product_card', ['product' => $product])
        @endforeach
      </div>
    </section>
  
    <!-- Flash Sale Products -->
    @if($promoProducts->count() > 0)
    <section class="featured-products enhanced">
      <h2>Flash Sale</h2>
      <div class="grid grid-3">
        @foreach ($promoProducts as $product)
            @include('components.product_card', ['product' => $product])
        @endforeach
      </div>
    </section>
    @endif
  
    <!-- Recommended Products -->
    <section class="featured-products enhanced">
      <h2>Recommended For You</h2>
      <div class="grid grid-3">
        @foreach ($recommendedProducts as $product)
            @include('components.product_card', ['product' => $product])
        @endforeach
      </div>
    </section>
  
    <!-- All Products -->
    <section class="featured-products enhanced">
      <h2>All Products</h2>
      <div class="grid grid-3">
        @foreach ($allProducts as $product)
            @include('components.product_card', ['product' => $product])
        @endforeach
      </div>
      <div style="text-align:center; margin-top:30px;">
        <a href="{{ url('shop') }}" style="display:inline-block; padding:12px 30px; background:#333; color:white; text-decoration:none; border-radius:5px; font-weight:bold;">View Full Store →</a>
      </div>
    </section>

    <!-- News & Blog -->
    <section class="news-section">
      <h2>News & Blog</h2>
      <div class="grid grid-3 dark-bg">
        @if ($latestNews->isNotEmpty())
            @foreach ($latestNews as $news)
            <div class="card">
              <img src="{{ asset('img/' . $news->image) }}" 
                   alt="{{ $news->title }}"
                   onerror="this.src='{{ asset('img/background/baiviet1.png') }}'">
              <h4>{{ $news->title }}</h4>
              <p>{{ Str::limit($news->excerpt, 100) }}</p>
              <a href="{{ url('news', $news->id) }}"><button class="more">Read More</button></a>
            </div>
            @endforeach
        @else
            <p style="text-align: center; color: #ccc; width: 100%;">No news available.</p>
        @endif
      </div>
    </section>

    <!-- Upcoming Tournament -->
    <section class="tournament enhanced">
      <h2>Upcoming Tournament</h2>
      @if ($upcomingMatch)
      <div class="tournament-content grid grid-2">
        <div class="tournament-banner">
            <img src="{{ asset('img/background/bannergiaidau.jpg') }}" alt="Tournament Banner" class="main-banner">
            <div class="match-teams-overlay">
                <div class="team" style="text-align: center;">
                    <img src="{{ asset('img/teams/' . ($upcomingMatch->team1_logo ?? '')) }}" onerror="this.src='{{ asset('img/product/default.jpg') }}'" alt="Team 1">
                    <div style="font-weight: bold;">{{ $upcomingMatch->team1_name }}</div>
                </div>
                <div class="vs" style="font-size: 24px; color: #ff4757; font-weight: bold; font-style: italic;">VS</div>
                <div class="team" style="text-align: center;">
                    <img src="{{ asset('img/teams/' . ($upcomingMatch->team2_logo ?? '')) }}" onerror="this.src='{{ asset('img/product/default.jpg') }}'" alt="Team 2">
                    <div style="font-weight: bold;">{{ $upcomingMatch->team2_name }}</div>
                </div>
            </div>
        </div>
        <div class="info">
          <h3>{{ $upcomingMatch->team1_name }} vs {{ $upcomingMatch->team2_name }}</h3>
          <p><i class="fas fa-gamepad"></i> <strong>Game:</strong> {{ strtoupper($upcomingMatch->game_type) }}</p>
          <p><i class="far fa-clock"></i> <strong>Time:</strong> {{ date('F j, Y - H:i', strtotime($upcomingMatch->match_time)) }}</p>
          
          <div class="countdown-timer" data-time="{{ $upcomingMatch->match_time }}">
            <div class="time-box"><span class="days">00</span><small>Days</small></div>
            <div class="time-box"><span class="hours">00</span><small>Hours</small></div>
            <div class="time-box"><span class="minutes">00</span><small>Mins</small></div>
            <div class="time-box"><span class="seconds">00</span><small>Secs</small></div>
          </div>
          
          <a href="{{ url('tournament') }}"><button>View Tournament</button></a>
          @if (!empty($upcomingMatch->stream_link))
            <a href="{{ $upcomingMatch->stream_link }}" target="_blank"><button style="background: #6441a5; margin-left: 10px;"><i class="fab fa-twitch"></i> Watch Stream</button></a>
          @endif
        </div>
      </div>
      @else
      <div class="tournament-content" style="text-align: center;">
          <p>No upcoming tournaments scheduled at the moment.</p>
          <a href="{{ url('tournament') }}"><button>Check Schedule</button></a>
      </div>
      @endif
    </section>
  </section>
@endsection

@section('scripts')
  <script src="{{ asset('js/home.js') }}"></script>
  <script>
  document.addEventListener('DOMContentLoaded', function() {
    @auth
      console.log('Welcome back, {{ Auth::user()->username }}!');
      const welcomeMsg = document.createElement('div');
      welcomeMsg.className = 'welcome-message';
      welcomeMsg.innerHTML = `
        <div style="position: fixed; top: 80px; right: 20px; background: #4CAF50; color: white; padding: 10px 20px; border-radius: 5px; z-index: 1000; box-shadow: 0 4px 12px rgba(0,0,0,0.2);">
          <i class="fas fa-check-circle"></i> Welcome back, {{ Auth::user()->username }}!
        </div>
      `;
      document.body.appendChild(welcomeMsg);
      setTimeout(() => welcomeMsg.remove(), 5000);
    @endauth
    
    // Tournament Countdown Logic
    const countdownEl = document.querySelector('.countdown-timer');
    if (countdownEl) {
        const targetDate = new Date(countdownEl.dataset.time).getTime();
        const updateTimer = () => {
            const now = new Date().getTime();
            const distance = targetDate - now;
            
            if (distance < 0) {
                countdownEl.innerHTML = '<div style="width:100%; text-align:center; color:#e63946; font-weight:bold; font-size: 1.2rem;">MATCH IS LIVE!</div>';
                return;
            }
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            countdownEl.querySelector('.days').innerText = String(days).padStart(2, '0');
            countdownEl.querySelector('.hours').innerText = String(hours).padStart(2, '0');
            countdownEl.querySelector('.minutes').innerText = String(minutes).padStart(2, '0');
            countdownEl.querySelector('.seconds').innerText = String(seconds).padStart(2, '0');
        };
        setInterval(updateTimer, 1000);
        updateTimer();
    }
  });
  </script>
@endsection
