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
          <a href="{{ url('shop') }}" class="{{ request()->is('shop*') ? 'active' : '' }}">SHOP</a>
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
      <button class="menu-toggle" aria-label="Toggle menu">
          <i class="fas fa-bars"></i>
      </button>
  </header>

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
      // Header Scripts
      document.addEventListener('DOMContentLoaded', function() {
          const menuToggle = document.querySelector('.menu-toggle');
          const mobileMenu = document.querySelector('.navbar');
          
          if (menuToggle) {
              menuToggle.addEventListener('click', function() {
                  mobileMenu.classList.toggle('active');
                  this.classList.toggle('active');
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
              
              if (mobileMenu.classList.contains('active') && 
                  !mobileMenu.contains(e.target) && 
                  !menuToggle.contains(e.target)) {
                  mobileMenu.classList.remove('active');
                  menuToggle.classList.remove('active');
              }
          });
          
          // Back to Top Button Logic
          const backToTopBtn = document.getElementById('backToTop');
          window.addEventListener('scroll', () => {
              if (window.scrollY > 300) {
                  backToTopBtn.classList.add('visible');
              } else {
                  backToTopBtn.classList.remove('visible');
              }
          });
          backToTopBtn.addEventListener('click', () => {
              window.scrollTo({ top: 0, behavior: 'smooth' });
          });
      });
  </script>
  @yield('scripts')
</body>
</html>
