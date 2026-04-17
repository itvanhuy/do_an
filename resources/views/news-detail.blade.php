@extends('layouts.app')

@section('title', $post->title . ' - TechShop')

@section('styles')
    <style>
        .news-detail-container { max-width: 900px; margin: 40px auto; padding: 0 20px; line-height: 1.8; color: #333; }
        .post-header { margin-bottom: 40px; text-align: center; }
        .post-header h1 { font-size: 2.8rem; margin-bottom: 20px; line-height: 1.3; }
        .post-meta { display: flex; justify-content: center; gap: 30px; color: #777; font-size: 0.95rem; margin-bottom: 30px; }
        .post-banner img { width: 100%; height: 500px; object-fit: cover; border-radius: 15px; margin-bottom: 40px; }
        .post-content { font-size: 1.15rem; }
        .post-content p { margin-bottom: 25px; }
        .post-content img { max-width: 100%; border-radius: 10px; margin: 30px 0; }
        .related-section { margin-top: 80px; padding-top: 40px; border-top: 1px solid #eee; }
        .related-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px; margin-top: 30px; }
        @media (max-width: 768px) { .related-grid { grid-template-columns: 1fr; } }
        .related-card { text-decoration: none; color: inherit; transition: 0.3s; }
        .related-card:hover { transform: translateY(-5px); }
        .related-card img { width: 100%; height: 180px; object-fit: cover; border-radius: 8px; margin-bottom: 15px; }
        .related-card h4 { font-size: 1.1rem; margin: 0; line-height: 1.4; color: #333; }
    </style>
@endsection

@section('content')
<main class="news-detail-container">
    <div class="post-header">
        <h1>{{ $post->title }}</h1>
        <div class="post-meta">
            <span><i class="far fa-calendar-alt"></i> {{ date('F d, Y', strtotime($post->created_at)) }}</span>
            <span><i class="far fa-user"></i> By Admin</span>
            <span><i class="far fa-eye"></i> {{ $post->views }} views</span>
        </div>
    </div>

    <div class="post-banner">
        <img src="{{ asset('img/' . $post->image) }}" alt="{{ $post->title }}" onerror="this.src='{{ asset('img/background/baiviet1.png') }}'">
    </div>

    <div class="post-content">
        {!! $post->content !!}
    </div>

    <div class="comments-section" style="margin-top: 80px; padding-top: 40px; border-top: 1px solid #eee;">
        <h3 style="margin-bottom:30px;">Comments ({{ count($comments) }})</h3>
        
        @if(session('success')) <div style="color:green; margin-bottom:15px;">{{ session('success') }}</div> @endif
        @if(session('error')) <div style="color:red; margin-bottom:15px;">{{ session('error') }}</div> @endif

        @auth
            <form action="{{ route('news.comment', $post->id) }}" method="POST" style="margin-bottom:40px;">
                @csrf
                <textarea name="content" required placeholder="Write a comment..." style="width:100%; border:1px solid #ddd; padding:15px; border-radius:8px; line-height:1.5; min-height:120px;"></textarea>
                <button type="submit" style="margin-top:15px; background:#000; color:#fff; border:none; padding:12px 30px; border-radius:5px; font-weight:bold; cursor:pointer;">Post Comment</button>
            </form>
        @else
            <p style="margin-bottom:40px; color:#666;">Please <a href="{{ route('login') }}" style="color:#000; font-weight:bold;">Log in</a> to comment.</p>
        @endauth

        <div class="comments-list">
            @forelse($comments as $comment)
                <div class="comment-item" style="margin-bottom:30px;">
                    <div style="font-weight:bold; margin-bottom:5px;">{{ $comment->username }} <span style="font-weight:normal; color:#999; font-size:0.85rem; margin-left:10px;">{{ date('M d, Y', strtotime($comment->created_at)) }}</span></div>
                    <div style="color:#555;">{{ $comment->content }}</div>
                </div>
            @empty
                <p style="color:#888; font-style:italic;">No comments yet. Be the first to join the conversation!</p>
            @endforelse
        </div>
    </div>

    <div class="related-section">
        <h3>Related Stories</h3>
        <div class="related-grid">
            @foreach($relatedPosts as $rp)
                <a href="{{ url('news/' . $rp->id) }}" class="related-card">
                    <img src="{{ asset('img/' . $rp->image) }}" alt="{{ $rp->title }}" onerror="this.src='{{ asset('img/background/baiviet2.png') }}'">
                    <h4>{{ Str::limit($rp->title, 60) }}</h4>
                </a>
            @endforeach
        </div>
    </div>
</main>
@endsection
