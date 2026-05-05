<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'TechShop')</title>
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css"
    integrity="sha512-NhSC1YmyruXifcj/KFRWoC561YpHpc5Jtzgvbuzx5VozKpWvQ+4nXhPdFgmx8xqexRcpAglTj9sIBWINXa8x5w=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
  
  <link rel="stylesheet" href="{{ asset('css/header.css') }}">
  <link rel="stylesheet" href="{{ asset('css/footer.css') }}">
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  @yield('styles')
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <style>
      .nav-dropdown {
          position: relative;
          display: inline-block;
      }
      .nav-dropdown-content {
          display: none;
          position: absolute;
          background-color: #f9f9f9;
          min-width: 200px;
          box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
          z-index: 1000;
          border-radius: 5px;
      }
      .nav-dropdown-content a {
          color: black;
          padding: 12px 16px;
          text-decoration: none;
          display: block;
      }
      .nav-dropdown-content a:hover {
          background-color: #f1f1f1;
      }
      .nav-dropdown:hover .nav-dropdown-content {
          display: block;
      }
  </style>
</head>
<body>
  <header class="header">
      <a href="{{ route('home') }}">
          <div class="logo">
              <img src="{{ asset('img/logo.png') }}" alt="Techshop Logo" style="height: 80px; width: auto; object-fit: contain;" />
          </div>
      </a>
      
      <nav class="navbar">
          <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">HOME</a>
          
          <div class="nav-dropdown">
              <a href="{{ url('shop') }}" class="dropbtn {{ request()->is('shop*') || request()->is('category*') ? 'active' : '' }}">SHOP <i class="fas fa-caret-down"></i></a>
              <div class="nav-dropdown-content">
                  <a href="{{ url('shop') }}">All Products</a>
                  @if(isset($globalCategories))
                      @foreach($globalCategories as $cat)
                          <a href="{{ route('category', $cat->slug ?? $cat->id) }}">{{ $cat->name }}</a>
                      @endforeach
                  @endif
              </div>
          </div>
          
          <a href="{{ url('tournament') }}" class="{{ request()->is('tournament*') ? 'active' : '' }}">TOURNAMENT</a>
          <a href="{{ url('about') }}" class="{{ request()->is('about*') ? 'active' : '' }}">ABOUT</a>
          <a href="{{ url('news') }}" class="{{ request()->is('news*') ? 'active' : '' }}">NEWS</a>
          <a href="{{ route('contact') }}" class="{{ request()->routeIs('contact') ? 'active' : '' }}">CONTACT</a>
      </nav>
      
      <div class="header-right">
          <div class="search-bar">
              <form action="{{ url('search') }}" method="GET" class="search-form">
                  <input type="text" name="q" placeholder="Search products..." value="{{ request('q') }}">
                  <button type="submit"><i class="fas fa-search"></i></button>
              </form>
          </div>

          <div class="user-menu">
              @auth
                  <div class="user-dropdown">
                      <button class="user-btn">
                          <i class="fas fa-user-circle"></i> 
                          <span class="username">{{ Auth::user()->username ?? Auth::user()->full_name }}</span>
                          <i class="fas fa-caret-down"></i>
                      </button>
                      <div class="dropdown-content">
                          <a href="{{ url('profile') }}"><i class="fas fa-user"></i> My Profile</a>
                          <a href="{{ url('orders') }}"><i class="fas fa-shopping-bag"></i> My Orders</a>
                          <a href="{{ url('wishlist') }}"><i class="fas fa-heart"></i> My Wishlist</a>
                          <a href="{{ url('cart') }}"><i class="fas fa-shopping-cart"></i> My Cart</a>
                          @if(Auth::user()->role === 'admin')
                              <div class="dropdown-divider"></div>
                              <a href="{{ url('admin') }}"><i class="fas fa-cog"></i> Admin Dashboard</a>
                          @endif
                          <div class="dropdown-divider"></div>
                          <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fas fa-sign-out-alt"></i> Logout</a>
                          <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                              @csrf
                          </form>
                      </div>
                  </div>
                  
                  <a href="{{ url('cart') }}" class="cart-icon">
                      <i class="fas fa-shopping-cart"></i>
                      <span class="cart-count">{{ \App\Http\Controllers\CartController::getCartCount() }}</span>
                  </a>
              @else
                  <div class="login-btn">
                      <a href="{{ route('login') }}" class="btn-login">Login</a>
                  </div>
                  <div class="register-btn">
                      <a href="{{ route('register') }}" class="btn-register">Register</a>
                  </div>
              @endauth
          </div>
      </div>
      
      <!-- Mobile menu toggle -->
      <button class="menu-toggle" id="menuToggle" aria-label="Toggle menu">
          <i class="fas fa-bars"></i>
      </button>
  </header>

  <!-- Mobile Menu -->
  <div class="mobile-menu" id="mobileMenu">
      <form action="{{ url('search') }}" method="GET" class="mobile-search">
          <div class="search-form">
              <input type="text" name="q" placeholder="Search products..." value="{{ request('q') }}">
              <button type="submit"><i class="fas fa-search"></i></button>
          </div>
      </form>

      <nav class="mobile-nav">
          <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}"><i class="fas fa-home"></i> Home</a>
          <a href="{{ url('shop') }}" class="{{ request()->is('shop*') ? 'active' : '' }}"><i class="fas fa-store"></i> Shop</a>
          <a href="{{ url('tournament') }}" class="{{ request()->is('tournament*') ? 'active' : '' }}"><i class="fas fa-trophy"></i> Tournament</a>
          <a href="{{ url('about') }}" class="{{ request()->is('about*') ? 'active' : '' }}"><i class="fas fa-info-circle"></i> About</a>
          <a href="{{ url('news') }}" class="{{ request()->is('news*') ? 'active' : '' }}"><i class="fas fa-newspaper"></i> News</a>
          <a href="{{ route('contact') }}" class="{{ request()->routeIs('contact') ? 'active' : '' }}"><i class="fas fa-envelope"></i> Contact</a>
      </nav>

      @auth
          <div class="mobile-user-info">
              <div class="mobile-user-header">
                  <i class="fas fa-user-circle"></i>
                  <div class="mobile-user-details">
                      <strong>{{ Auth::user()->full_name ?? Auth::user()->username }}</strong>
                      <span>{{ Auth::user()->role }}</span>
                  </div>
              </div>
              <a href="{{ url('profile') }}" class="mobile-menu-link"><span><i class="fas fa-user"></i> My Profile</span></a>
              <a href="{{ url('orders') }}" class="mobile-menu-link"><span><i class="fas fa-shopping-bag"></i> My Orders</span></a>
              <a href="{{ url('wishlist') }}" class="mobile-menu-link"><span><i class="fas fa-heart"></i> Wishlist</span></a>
              <a href="{{ url('cart') }}" class="mobile-menu-link">
                  <span><i class="fas fa-shopping-cart"></i> Cart</span>
                  <span class="mobile-cart-count">{{ \App\Http\Controllers\CartController::getCartCount() }}</span>
              </a>
              @if(Auth::user()->role === 'admin')
                  <a href="{{ url('admin') }}" class="mobile-menu-link"><span><i class="fas fa-cog"></i> Admin</span></a>
              @endif
              <a href="{{ route('logout') }}" class="mobile-menu-link logout-link"
                 onclick="event.preventDefault(); document.getElementById('logout-form-mobile').submit();">
                  <span><i class="fas fa-sign-out-alt"></i> Logout</span>
              </a>
              <form id="logout-form-mobile" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>
          </div>
      @else
          <div class="mobile-auth">
              <a href="{{ route('login') }}" class="mobile-btn btn-login">Login</a>
              <a href="{{ route('register') }}" class="mobile-btn btn-register">Register</a>
          </div>
      @endauth
  </div>

  <main>
    @yield('content')
  </main>

  <footer class="footer">
      <div class="footer-content">
          <div class="footer-logo">
              <img src="{{ asset('img/logo.png') }}" alt="TechShop Logo">
              <h3>TECHSHOP</h3>
              <p>Your premier destination for high-end gaming gear and professional eSports tournaments.</p>
          </div>
          
          <div class="footer-links">
              <h4>Quick Links</h4>
              <ul>
                  <li><a href="{{ route('home') }}">Home</a></li>
                  <li><a href="{{ url('shop') }}">Shop</a></li>
                  <li><a href="{{ url('tournament') }}">Tournament</a></li>
                  <li><a href="{{ url('news') }}">News</a></li>
                  <li><a href="{{ url('about') }}">About</a></li>
              </ul>
          </div>
          
          <div class="footer-contact">
              <h4>Contact Us</h4>
              <p><i class="fas fa-map-marker-alt"></i> 123 Gaming Street, Da Nang</p>
              <p><i class="fas fa-phone"></i> +84 896 492 400</p>
              <p><i class="fas fa-envelope"></i> levahuy06042003@gmail.com</p>
          </div>
          
          <div class="footer-social">
              <h4>Follow Us</h4>
              <div class="social-icons">
                  <a href="https://www.facebook.com/huylv.2k3" target="_blank"><i class="fab fa-facebook-f"></i></a>
                  <a href="#" target="_blank"><i class="fab fa-twitter"></i></a>
                  <a href="#" target="_blank"><i class="fab fa-instagram"></i></a>
                  <a href="#" target="_blank"><i class="fab fa-youtube"></i></a>
              </div>
              
              <div class="footer-newsletter">
                  <h5>Subscribe to Newsletter</h5>
                  <form class="newsletter-form" action="{{ url('newsletter') }}" method="POST">
                      @csrf
                      <input type="email" name="email" placeholder="Your email address" required>
                      <button type="submit" class="btn-subscribe"><i class="fas fa-paper-plane"></i></button>
                  </form>
              </div>
          </div>
      </div>
      
      <div class="footer-bottom">
          <div class="footer-bottom-content">
              <p>&copy; <span class="current-year">{{ date('Y') }}</span> TechShop. All rights reserved.</p>
              
              <div class="footer-legal">
                  <a href="#">Privacy Policy</a>
                  <a href="#">Terms of Service</a>
                  <a href="#">Cookie Policy</a>
              </div>
              
              <div class="payment-methods">
                  <i class="fab fa-cc-visa" title="Visa"></i>
                  <i class="fab fa-cc-mastercard" title="Mastercard"></i>
                  <i class="fab fa-cc-paypal" title="Paypal"></i>
                  <i class="fas fa-money-bill-wave" title="Cash on Delivery"></i>
              </div>
          </div>
      </div>
      
      <button id="backToTop" class="back-to-top" aria-label="Back to top">
          <i class="fas fa-arrow-up"></i>
      </button>
  </footer>

  <script>
      document.addEventListener('DOMContentLoaded', function() {
          const menuToggle = document.getElementById('menuToggle');
          const mobileMenu = document.getElementById('mobileMenu');

          if (menuToggle && mobileMenu) {
              menuToggle.addEventListener('click', function() {
                  mobileMenu.classList.toggle('active');
                  this.classList.toggle('active');
                  document.body.classList.toggle('menu-open');
              });
          }

          const userBtn = document.querySelector('.user-btn');
          if (userBtn) {
              userBtn.addEventListener('click', function(e) {
                  e.preventDefault();
                  e.stopPropagation();
                  this.parentElement.classList.toggle('active');
              });
          }

          document.addEventListener('click', function(e) {
              const dropdown = document.querySelector('.user-dropdown');
              if (dropdown && !dropdown.contains(e.target)) {
                  dropdown.classList.remove('active');
              }
              if (mobileMenu && mobileMenu.classList.contains('active') &&
                  !mobileMenu.contains(e.target) &&
                  menuToggle && !menuToggle.contains(e.target)) {
                  mobileMenu.classList.remove('active');
                  menuToggle.classList.remove('active');
                  document.body.classList.remove('menu-open');
              }
          });

          // Back to Top
          const backToTopBtn = document.getElementById('backToTop');
          if (backToTopBtn) {
              window.addEventListener('scroll', () => {
                  backToTopBtn.classList.toggle('visible', window.scrollY > 300);
              });
              backToTopBtn.addEventListener('click', () => {
                  window.scrollTo({ top: 0, behavior: 'smooth' });
              });
          }
      });
  </script>
  
  <!--Start of Live Chat Widget-->
  <style>
    #chat-widget { position: fixed; bottom: 20px; right: 20px; z-index: 9999; font-family: 'Segoe UI', sans-serif; }
    #chat-toggle { width: 56px; height: 56px; border-radius: 50%; background: #9147ff; color: white; border: none; cursor: pointer; font-size: 22px; box-shadow: 0 4px 15px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; transition: all 0.3s; }
    #chat-toggle:hover { background: #772ce8; transform: scale(1.1); }
    #chat-badge { position: absolute; top: -4px; right: -4px; background: #ff3b3b; color: white; border-radius: 50%; width: 18px; height: 18px; font-size: 11px; display: none; align-items: center; justify-content: center; font-weight: bold; }
    #chat-box { display: none; position: absolute; bottom: 65px; right: 0; width: 320px; background: white; border-radius: 12px; box-shadow: 0 8px 30px rgba(0,0,0,0.2); overflow: hidden; flex-direction: column; }
    #chat-header { background: #9147ff; color: white; padding: 14px 16px; display: flex; align-items: center; justify-content: space-between; }
    #chat-header span { font-weight: 600; font-size: 15px; }
    #chat-close { background: none; border: none; color: white; font-size: 18px; cursor: pointer; }
    #chat-messages { height: 280px; overflow-y: auto; padding: 12px; display: flex; flex-direction: column; gap: 8px; background: #f9f9f9; }
    .chat-msg { max-width: 80%; padding: 8px 12px; border-radius: 12px; font-size: 13px; line-height: 1.4; word-break: break-word; }
    .chat-msg.user { background: #9147ff; color: white; align-self: flex-end; border-bottom-right-radius: 3px; }
    .chat-msg.admin { background: white; color: #333; align-self: flex-start; border-bottom-left-radius: 3px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
    .chat-msg .time { font-size: 10px; opacity: 0.7; margin-top: 3px; }
    #chat-input-area { display: flex; padding: 10px; border-top: 1px solid #eee; gap: 8px; background: white; }
    #chat-input { flex: 1; border: 1px solid #ddd; border-radius: 20px; padding: 8px 14px; font-size: 13px; outline: none; }
    #chat-input:focus { border-color: #9147ff; }
    #chat-send { background: #9147ff; color: white; border: none; border-radius: 50%; width: 36px; height: 36px; cursor: pointer; font-size: 14px; display: flex; align-items: center; justify-content: center; }
    #chat-login-msg { padding: 20px; text-align: center; color: #666; font-size: 13px; }
    #chat-login-msg a { color: #9147ff; font-weight: 600; }
  </style>

  <div id="chat-widget">
    <div id="chat-box">
      <div id="chat-header">
        <span>💬 TechShop Support</span>
        <button id="chat-close"><i class="fas fa-times"></i></button>
      </div>
      @auth
        <div id="chat-messages"></div>
        <div id="chat-input-area">
          <input type="text" id="chat-input" placeholder="Type a message..." maxlength="500">
          <button id="chat-send"><i class="fas fa-paper-plane"></i></button>
        </div>
      @else
        <div id="chat-login-msg">
          <i class="fas fa-lock" style="font-size:2rem; color:#9147ff; margin-bottom:10px; display:block;"></i>
          Please <a href="{{ route('login') }}">login</a> to chat with us.
        </div>
      @endauth
    </div>
    <button id="chat-toggle" title="Chat with us">
      <i class="fas fa-comment-dots"></i>
    </button>
    <div id="chat-badge"></div>
  </div>

  @auth
  <script>
    const chatToggle = document.getElementById('chat-toggle');
    const chatBox = document.getElementById('chat-box');
    const chatClose = document.getElementById('chat-close');
    const chatMessages = document.getElementById('chat-messages');
    const chatInput = document.getElementById('chat-input');
    const chatSend = document.getElementById('chat-send');
    const chatBadge = document.getElementById('chat-badge');
    let lastMessageId = 0;
    let isOpen = false;
    let initialized = false;

    chatToggle.addEventListener('click', () => {
        isOpen = !isOpen;
        chatBox.style.display = isOpen ? 'flex' : 'none';
        if (isOpen) {
            chatBadge.style.display = 'none';
            if (!initialized) { loadAllMessages(); initialized = true; }
        }
    });

    chatClose.addEventListener('click', () => {
        isOpen = false;
        chatBox.style.display = 'none';
    });

    function formatTime(dateStr) {
        const d = new Date(dateStr);
        return d.getHours().toString().padStart(2,'0') + ':' + d.getMinutes().toString().padStart(2,'0');
    }

    function appendMessages(msgs) {
        if (msgs.length === 0) return;
        if (chatMessages.querySelector('p')) chatMessages.innerHTML = '';
        msgs.forEach(m => {
            const div = document.createElement('div');
            div.className = 'chat-msg ' + m.sender;
            div.innerHTML = `${m.message}<div class="time">${formatTime(m.created_at)}</div>`;
            chatMessages.appendChild(div);
            lastMessageId = Math.max(lastMessageId, m.id);
        });
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function loadAllMessages() {
        fetch('{{ route("chat.messages") }}')
            .then(r => r.json())
            .then(data => {
                if (!data.success) return;
                chatMessages.innerHTML = '';
                if (data.messages.length === 0) {
                    chatMessages.innerHTML = '<p style="text-align:center; color:#aaa; font-size:12px; margin-top:20px;">No messages yet. Say hello! 👋</p>';
                    return;
                }
                appendMessages(data.messages);
            });
    }

    function pollNewMessages() {
        if (lastMessageId === 0 && !initialized) return;
        fetch(`{{ route("chat.messages") }}?last_id=${lastMessageId}`)
            .then(r => r.json())
            .then(data => {
                if (!data.success || data.messages.length === 0) return;
                if (isOpen) {
                    appendMessages(data.messages);
                } else {
                    // Có tin mới từ admin khi đang đóng
                    const hasAdmin = data.messages.some(m => m.sender === 'admin');
                    if (hasAdmin) {
                        chatBadge.style.display = 'flex';
                        chatBadge.textContent = '!';
                    }
                    data.messages.forEach(m => lastMessageId = Math.max(lastMessageId, m.id));
                }
            });
    }

    function sendMessage() {
        const msg = chatInput.value.trim();
        if (!msg) return;
        chatInput.value = '';
        fetch('{{ route("chat.send") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ message: msg })
        }).then(r => r.json()).then(data => {
            if (data.success) pollNewMessages();
        });
    }

    chatSend.addEventListener('click', sendMessage);
    chatInput.addEventListener('keypress', e => { if (e.key === 'Enter') sendMessage(); });

    // Polling mỗi 4 giây
    setInterval(pollNewMessages, 4000);
  </script>
  @endauth
  <!--End of Live Chat Widget-->

  @yield('scripts')
</body>
</html>
