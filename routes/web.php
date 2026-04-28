<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;

use App\Http\Controllers\ShopController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\TournamentController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CouponController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::post('/newsletter', [HomeController::class, 'newsletter'])->name('newsletter');

// Auth Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Email Verification
Route::get('/email/verify/{token}', [AuthController::class, 'verifyEmail'])->name('email.verify');

// Forgot & Reset Password
Route::get('/password/forgot', [AuthController::class, 'showForgotPassword'])->name('password.forgot.form');
Route::post('/password/forgot', [AuthController::class, 'sendResetLink'])->name('password.forgot');
Route::get('/password/reset/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset.form');
Route::post('/password/reset', [AuthController::class, 'resetPassword'])->name('password.reset');

// Social Login Routes
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('login.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);
Route::get('/auth/facebook', [AuthController::class, 'redirectToFacebook'])->name('login.facebook');
Route::get('/auth/facebook/callback', [AuthController::class, 'handleFacebookCallback']);

// Shop & Products
Route::get('/shop', [ShopController::class, 'index'])->name('shop');
Route::get('/products', [ShopController::class, 'index']);
Route::get('/category/{slug}', [ShopController::class, 'category'])->name('category');
Route::get('/products/{id}', [ProductController::class, 'show'])->name('product.show');
Route::post('/products/{id}/review', [ProductController::class, 'submitReview'])->name('product.review');

// Cart
Route::get('/cart', [CartController::class, 'index'])->name('cart');
Route::post('/cart/add', [CartController::class, 'add']);
Route::post('/cart/update', [CartController::class, 'update']);
Route::post('/cart/remove', [CartController::class, 'remove']);

// Coupon AJAX
Route::post('/coupon/apply', [CheckoutController::class, 'applyCoupon'])->name('coupon.apply');

// Checkout
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');
Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
Route::get('/checkout/momo_return', [CheckoutController::class, 'momoReturn'])->name('checkout.momo_return');
Route::post('/checkout/momo_notify', [CheckoutController::class, 'momoNotify'])->name('checkout.momo_notify');

// Tournament & News
Route::get('/tournament', [TournamentController::class, 'index'])->name('tournament');
Route::get('/news', [NewsController::class, 'index'])->name('news');
Route::get('/news/{id}', [NewsController::class, 'show'])->name('news.show');
Route::post('/news/{id}/comment', [NewsController::class, 'submitComment'])->name('news.comment');
Route::get('/about', function () {
    return view('about');
})->name('about');
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'send'])->name('contact.send');

// Profile & Orders
Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
Route::post('/profile/password', [ProfileController::class, 'changePassword'])->name('profile.password');

Route::get('/orders', [OrderController::class, 'index'])->name('orders');
Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist');
Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
Route::get('/search', [SearchController::class, 'index'])->name('search');

// Admin Routes
Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'admin']], function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/dashboard', [AdminController::class, 'dashboard']);
    
    // Products CRUD
    Route::get('/products', [AdminController::class, 'products'])->name('admin.products');
    Route::get('/products/create', [AdminController::class, 'productsCreate'])->name('admin.products.create');
    Route::post('/products', [AdminController::class, 'productsStore'])->name('admin.products.store');
    Route::get('/products/{id}/edit', [AdminController::class, 'productsEdit'])->name('admin.products.edit');
    Route::post('/products/{id}', [AdminController::class, 'productsUpdate'])->name('admin.products.update');
    Route::delete('/products/{id}', [AdminController::class, 'productsDestroy'])->name('admin.products.destroy');

    // Blog CRUD
    Route::get('/blog', [AdminController::class, 'blog'])->name('admin.blog');
    Route::get('/blog/create', [AdminController::class, 'blogCreate'])->name('admin.blog.create');
    Route::post('/blog', [AdminController::class, 'blogStore'])->name('admin.blog.store');
    Route::get('/blog/{id}/edit', [AdminController::class, 'blogEdit'])->name('admin.blog.edit');
    Route::post('/blog/{id}', [AdminController::class, 'blogUpdate'])->name('admin.blog.update');
    Route::delete('/blog/{id}', [AdminController::class, 'blogDestroy'])->name('admin.blog.destroy');

    // Matches CRUD
    Route::get('/matches', [AdminController::class, 'matches'])->name('admin.matches');
    Route::get('/matches/create', [AdminController::class, 'matchesCreate'])->name('admin.matches.create');
    Route::post('/matches', [AdminController::class, 'matchesStore'])->name('admin.matches.store');
    Route::get('/matches/{id}/edit', [AdminController::class, 'matchesEdit'])->name('admin.matches.edit');
    Route::post('/matches/{id}', [AdminController::class, 'matchesUpdate'])->name('admin.matches.update');
    Route::delete('/matches/{id}', [AdminController::class, 'matchesDestroy'])->name('admin.matches.destroy');

    // Orders
    Route::get('/orders', [AdminController::class, 'orders'])->name('admin.orders');
    Route::post('/orders/{id}', [AdminController::class, 'updateOrderStatus'])->name('admin.orders.update');
    
    // Users
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    Route::post('/users/{id}/role', [AdminController::class, 'updateUserRole'])->name('admin.users.update_role');
    Route::get('/users/{id}/delete', [AdminController::class, 'usersDestroy'])->name('admin.users.destroy');

    // Rankings
    Route::get('/rankings', [AdminController::class, 'rankings'])->name('admin.rankings');
    Route::post('/rankings', [AdminController::class, 'rankingsStore'])->name('admin.rankings.store');
    Route::get('/rankings/{id}/delete', [AdminController::class, 'rankingsDestroy'])->name('admin.rankings.destroy');

    // Teams
    Route::get('/teams', [AdminController::class, 'teams'])->name('admin.teams');
    Route::post('/teams', [AdminController::class, 'teamsStore'])->name('admin.teams.store');
    Route::get('/teams/{id}/delete', [AdminController::class, 'teamsDestroy'])->name('admin.teams.destroy');

    // Comments
    Route::get('/comments', [AdminController::class, 'comments'])->name('admin.comments');
    Route::get('/comments/{id}/delete', [AdminController::class, 'commentsDestroy'])->name('admin.comments.destroy');

    // Reviews
    Route::get('/reviews', [AdminController::class, 'reviews'])->name('admin.reviews');
    Route::get('/reviews/{id}/approve', [AdminController::class, 'reviewsApprove'])->name('admin.reviews.approve');
    Route::get('/reviews/{id}/delete', [AdminController::class, 'reviewsDestroy'])->name('admin.reviews.destroy');

    // Settings
    Route::get('/settings', [AdminController::class, 'settings'])->name('admin.settings');
    Route::post('/settings', [AdminController::class, 'settingsUpdate'])->name('admin.settings.update');

    // Newsletters
    Route::get('/newsletters', [AdminController::class, 'newsletterSubscribers'])->name('admin.newsletters');
    Route::get('/newsletters/{id}/delete', [AdminController::class, 'newsletterDestroy'])->name('admin.newsletters.destroy');

    // Brands
    Route::get('/brands', [AdminController::class, 'brands'])->name('admin.brands');
    Route::post('/brands', [AdminController::class, 'brandsStore'])->name('admin.brands.store');
    Route::get('/brands/{id}/delete', [AdminController::class, 'brandsDestroy'])->name('admin.brands.destroy');

    // Categories
    Route::get('/categories', [AdminController::class, 'categories'])->name('admin.categories');
    Route::post('/categories', [AdminController::class, 'categoriesStore'])->name('admin.categories.store');
    Route::get('/categories/{id}/edit', [AdminController::class, 'categoriesEdit'])->name('admin.categories.edit');
    Route::post('/categories/{id}/update', [AdminController::class, 'categoriesUpdate'])->name('admin.categories.update');
    Route::get('/categories/{id}/delete', [AdminController::class, 'categoriesDestroy'])->name('admin.categories.destroy');

    // Contacts
    Route::get('/contacts', [AdminController::class, 'contacts'])->name('admin.contacts');
    Route::get('/contacts/{id}', [AdminController::class, 'contactsShow'])->name('admin.contacts.show');
    Route::post('/contacts/{id}/reply', [AdminController::class, 'contactsReply'])->name('admin.contacts.reply');
    Route::delete('/contacts/{id}', [AdminController::class, 'contactsDestroy'])->name('admin.contacts.destroy');

    // Slides
    Route::get('/slides', [AdminController::class, 'slides'])->name('admin.slides');
    Route::post('/slides', [AdminController::class, 'slidesStore'])->name('admin.slides.store');
    Route::post('/slides/{id}/toggle', [AdminController::class, 'slidesToggle'])->name('admin.slides.toggle');
    Route::delete('/slides/{id}', [AdminController::class, 'slidesDestroy'])->name('admin.slides.destroy');

    // Coupons
    Route::get('/coupons', [AdminController::class, 'coupons'])->name('admin.coupons');
    Route::post('/coupons', [AdminController::class, 'couponsStore'])->name('admin.coupons.store');
    Route::delete('/coupons/{id}', [AdminController::class, 'couponsDestroy'])->name('admin.coupons.destroy');
});
