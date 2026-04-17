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

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::post('/newsletter', [HomeController::class, 'newsletter'])->name('newsletter');

// Auth Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Shop & Products
Route::get('/shop', [ShopController::class, 'index'])->name('shop');
Route::get('/products', [ShopController::class, 'index']); // Optional, redirecting query strings
Route::get('/products/{id}', [ProductController::class, 'show'])->name('product.show');
Route::post('/products/{id}/review', [ProductController::class, 'submitReview'])->name('product.review');

// Cart
Route::get('/cart', [CartController::class, 'index'])->name('cart');
Route::post('/cart/add', [CartController::class, 'add']);
Route::post('/cart/update', [CartController::class, 'update']);
Route::post('/cart/remove', [CartController::class, 'remove']);

// Checkout
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');
Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
Route::get('/checkout/vnpay_return', [CheckoutController::class, 'vnpayReturn'])->name('checkout.vnpay_return');

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
    Route::get('/products', [AdminController::class, 'products'])->name('admin.products');
    Route::get('/products/create', [AdminController::class, 'productsCreate'])->name('admin.products.create');
    Route::post('/products', [AdminController::class, 'productsStore'])->name('admin.products.store');
    Route::get('/products/{id}/edit', [AdminController::class, 'productsEdit'])->name('admin.products.edit');
    Route::post('/products/{id}', [AdminController::class, 'productsUpdate'])->name('admin.products.update');
    Route::delete('/products/{id}', [AdminController::class, 'productsDestroy'])->name('admin.products.destroy');

    Route::get('/blog', [AdminController::class, 'blog'])->name('admin.blog');
    Route::get('/blog/create', [AdminController::class, 'blogCreate'])->name('admin.blog.create');
    Route::post('/blog', [AdminController::class, 'blogStore'])->name('admin.blog.store');
    Route::get('/blog/{id}/edit', [AdminController::class, 'blogEdit'])->name('admin.blog.edit');
    Route::post('/blog/{id}', [AdminController::class, 'blogUpdate'])->name('admin.blog.update');
    Route::delete('/blog/{id}', [AdminController::class, 'blogDestroy'])->name('admin.blog.destroy');

    Route::get('/matches', [AdminController::class, 'matches'])->name('admin.matches');
    Route::get('/matches/create', [AdminController::class, 'matchesCreate'])->name('admin.matches.create');
    Route::post('/matches', [AdminController::class, 'matchesStore'])->name('admin.matches.store');
    Route::get('/matches/{id}/edit', [AdminController::class, 'matchesEdit'])->name('admin.matches.edit');
    Route::post('/matches/{id}', [AdminController::class, 'matchesUpdate'])->name('admin.matches.update');
    Route::delete('/matches/{id}', [AdminController::class, 'matchesDestroy'])->name('admin.matches.destroy');

    Route::get('/orders', [AdminController::class, 'orders'])->name('admin.orders');
    Route::post('/orders/{id}', [AdminController::class, 'updateOrderStatus'])->name('admin.orders.update');
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    Route::get('/users/{id}/delete', [AdminController::class, 'usersDestroy'])->name('admin.users.destroy');

    Route::get('/rankings', [AdminController::class, 'rankings'])->name('admin.rankings');
    Route::post('/rankings', [AdminController::class, 'rankingsStore'])->name('admin.rankings.store');
    Route::get('/rankings/{id}/delete', [AdminController::class, 'rankingsDestroy'])->name('admin.rankings.destroy');

    Route::get('/teams', [AdminController::class, 'teams'])->name('admin.teams');
    Route::post('/teams', [AdminController::class, 'teamsStore'])->name('admin.teams.store');
    Route::get('/teams/{id}/delete', [AdminController::class, 'teamsDestroy'])->name('admin.teams.destroy');

    Route::get('/comments', [AdminController::class, 'comments'])->name('admin.comments');
    Route::get('/comments/{id}/delete', [AdminController::class, 'commentsDestroy'])->name('admin.comments.destroy');

    Route::get('/reviews', [AdminController::class, 'reviews'])->name('admin.reviews');
    Route::get('/reviews/{id}/approve', [AdminController::class, 'reviewsApprove'])->name('admin.reviews.approve');
    Route::get('/reviews/{id}/delete', [AdminController::class, 'reviewsDestroy'])->name('admin.reviews.destroy');

    Route::get('/settings', [AdminController::class, 'settings'])->name('admin.settings');
    Route::post('/settings', [AdminController::class, 'settingsUpdate'])->name('admin.settings.update');

    Route::get('/newsletters', [AdminController::class, 'newsletterSubscribers'])->name('admin.newsletters');
    Route::get('/newsletters/{id}/delete', [AdminController::class, 'newsletterDestroy'])->name('admin.newsletters.destroy');

    Route::get('/brands', [AdminController::class, 'brands'])->name('admin.brands');
    Route::post('/brands', [AdminController::class, 'brandsStore'])->name('admin.brands.store');
    Route::get('/brands/{id}/delete', [AdminController::class, 'brandsDestroy'])->name('admin.brands.destroy');

    Route::get('/categories', [AdminController::class, 'categories'])->name('admin.categories');
    Route::post('/categories', [AdminController::class, 'categoriesStore'])->name('admin.categories.store');
    Route::get('/categories/{id}/delete', [AdminController::class, 'categoriesDestroy'])->name('admin.categories.destroy');
});
