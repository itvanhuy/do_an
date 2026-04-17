@extends('layouts.app')

@section('title', 'News & Blog - '.env('APP_NAME', 'TechShop'))

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/news.css') }}">
    <style>
        .news-container { max-width: 1200px; margin: 40px auto; padding: 0 20px; }
        .page-title { margin-bottom: 40px; color: #333; text-align: center; font-size: 2.5rem; }
        .featured-post { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-bottom: 60px; background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        @media (max-width: 768px) { .featured-post { grid-template-columns: 1fr; } }
        .featured-image img { width: 100%; height: 100%; object-fit: cover; }
        .featured-content { padding: 40px; display: flex; flex-direction: column; justify-content: center; }
        .post-meta { font-size: 0.9rem; color: #777; margin-bottom: 15px; display: flex; gap: 20px; }
        .featured-content h2 { font-size: 2rem; margin: 0 0 20px; color: #333; }
        .featured-content p { color: #666; line-height: 1.6; margin-bottom: 30px; }
        .read-more { color: var(--accent-color); text-decoration: none; font-weight: bold; font-size: 1.1rem; }
        .news-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 30px; }
        .news-card { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.03); transition: 0.3s; }
        .news-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.08); }
        .card-image img { width: 100%; height: 220px; object-fit: cover; }
        .card-content { padding: 25px; }
        .card-content h3 { font-size: 1.3rem; margin: 0 0 15px; color: #333; }
        .card-content p { color: #666; font-size: 0.95rem; line-height: 1.6; }
    </style>
@endsection

@section('content')
<main class="news-container">
    <h1 class="page-title">Latest & Featured News</h1>

    @if($featuredPost)
    <article class="featured-post">
        <div class="featured-image">
            <img src="{{ asset('img/' . $featuredPost->image) }}" alt="{{ $featuredPost->title }}" onerror="this.src='{{ asset('img/background/baiviet1.png') }}'">
        </div>
        <div class="featured-content">
            <div class="post-meta">
                <span><i class="far fa-calendar-alt"></i> {{ date('M d, Y', strtotime($featuredPost->created_at)) }}</span>
                <span><i class="far fa-eye"></i> {{ $featuredPost->views }} views</span>
            </div>
            <h2>{{ $featuredPost->title }}</h2>
            <p>{{ $featuredPost->excerpt }}</p>
            <a href="{{ url('news/' . $featuredPost->id) }}" class="read-more">Read Full Story <i class="fas fa-arrow-right"></i></a>
        </div>
    </article>
    @endif

    <div class="news-grid">
        @foreach($posts as $post)
        <article class="news-card">
            <div class="card-image">
                <img src="{{ asset('img/' . $post->image) }}" alt="{{ $post->title }}" onerror="this.src='{{ asset('img/background/baiviet2.png') }}'">
            </div>
            <div class="card-content">
                <div class="post-meta">
                    <span><i class="far fa-calendar-alt"></i> {{ date('M d, Y', strtotime($post->created_at)) }}</span>
                </div>
                <h3>{{ $post->title }}</h3>
                <p>{{ Str::limit($post->excerpt, 120) }}</p>
                <a href="{{ url('news/' . $post->id) }}" class="read-more" style="font-size: 0.95rem;">Keep Reading <i class="fas fa-chevron-right"></i></a>
            </div>
        </article>
        @endforeach
    </div>

    @if(!$featuredPost && $posts->isEmpty())
        <div style="text-align: center; padding: 100px 0;">
            <i class="fas fa-newspaper" style="font-size: 5rem; color: #ddd; margin-bottom: 20px;"></i>
            <h2 style="color: #999;">Stay tuned for the latest news!</h2>
        </div>
    @endif
</main>
@endsection
