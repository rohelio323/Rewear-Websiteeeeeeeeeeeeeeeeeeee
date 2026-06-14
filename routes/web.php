<?php
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CO2Controller;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\Admin\CO2CategoryController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\PostVoteController;


Route::get('/', function () {
    return view('home');
})->name('home');

Route::post('/admin/categories', [CO2Controller::class, 'addCategory']);
Route::put('/admin/categories/{id}/co2-constant', [CO2Controller::class, 'updateCategoryCO2']);
Route::delete('/admin/categories/{id}', [CO2Controller::class, 'deleteCategory']);

Route::get('/marketplace', [ItemController::class, 'index'])->name('marketplace.index');
Route::get('/item/detail/{item}', [ItemController::class, 'show'])->name('items.show');
Route::get('/community', [PostController::class, 'index'])->name('community.index');

Route::get('/seller/{user}', [ReviewController::class, 'sellerProfile'])->name('seller.profile');

Route::get('/challenges', [App\Http\Controllers\ChallengeController::class, 'index'])->name('challenges.index');
Route::get('/challenges/{challenge}', [App\Http\Controllers\ChallengeController::class, 'show'])->name('challenges.show');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/my-stats', [App\Http\Controllers\ProfileStatsController::class, 'index'])->name('profile.stats');
    Route::post('/profile/seller-apply', [ProfileController::class, 'applyAsSeller'])->name('seller.apply.submit');

    // Orders
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/voucher', [OrderController::class, 'applyVoucher'])->name('orders.applyVoucher');
    Route::post('/orders/{order}/voucher/remove', [OrderController::class, 'removeVoucher'])->name('orders.removeVoucher');
    Route::get('/orders/{order}/payment', [OrderController::class, 'paymentForm'])->name('orders.payment');
    Route::post('/orders/{order}/confirm-payment', [OrderController::class, 'confirmPayment'])->name('orders.confirmPayment');
    Route::get('/orders/{order}/confirmed', [OrderController::class, 'confirmed'])->name('orders.confirmed');
    Route::delete('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('/orders/{order}/ship', [OrderController::class, 'ship'])->name('orders.ship');
    Route::post('/orders/{order}/receive', [OrderController::class, 'receive'])->name('orders.receive');
    Route::get('/transactions', [OrderController::class, 'transactions'])->name('transactions.index');

    Route::post('/community/create', [PostController::class, 'store'])->name('community.store');
    Route::put('/community/update/{id}', [PostController::class, 'update'])->name('community.update');
    Route::delete('/community/delete/{id}', [PostController::class, 'destroy'])->name('community.destroy');
    Route::post('/community/{id}/vote', [PostVoteController::class, 'vote'])->name('community.vote');
    Route::post('/challenges/{challenge}/submit', [App\Http\Controllers\ChallengeController::class, 'submitPost'])->name('challenges.submit');
    Route::get('/community/hashtag/lookup', [App\Http\Controllers\PostController::class, 'hashtagLookup'])->name('community.hashtag.lookup');
    Route::get('/community/posts/{id}/breakdown', [PostVoteController::class, 'details'])->name('community.posts.breakdown')->middleware('auth');

    Route::get('/favorites', [WishlistController::class, 'index'])->name('favorites.index');
    Route::post('/favorites/{item}/toggle', [WishlistController::class, 'toggle'])->name('favorites.toggle');

    Route::post('/rewards/redeem/{voucher}', [ProfileController::class, 'redeem'])->name('rewards.redeem')->middleware('auth');

    // ===== PBI-38: Reviews =====
    Route::get('/orders/{order}/review', [ReviewController::class, 'create'])->name('reviews.create');
    Route::post('/orders/{order}/review', [ReviewController::class, 'store'])->name('reviews.store');
    Route::get('/my-reviews', [ReviewController::class, 'index'])->name('reviews.index');

    Route::post('/reports', [ReportsController::class, 'store'])->name('reports.store');

});

// Seller Middleware
Route::middleware(['auth', 'seller'])->group(function () {
    Route::get('/items/create', [ItemController::class, 'create'])->name('items.create');
    Route::post('/items', [ItemController::class, 'store'])->name('items.store');
    Route::get('items/{item}/edit', [ItemController::class, 'edit'])->name('items.edit');
    Route::put('/items/{item}', [ItemController::class, 'update'])->name('items.update');
    Route::delete('/items/{item}', [ItemController::class, 'destroy'])->name('items.destroy');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/chart-data', [AdminDashboardController::class, 'chartData'])->name('dashboard.chartData');
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
    Route::post('/users/{user}/restore', [AdminUserController::class, 'restore'])->name('users.restore');
    Route::get('/co2-categories', [CO2Controller::class, 'index'])->name('co2.index');
    Route::post('/seller-requests/{user}/approve', [AdminUserController::class, 'approveSeller'])->name('seller-requests.approve');
    Route::post('/seller-requests/{user}/reject', [AdminUserController::class, 'rejectSeller'])->name('seller-requests.reject');
    Route::get('/moderation', [\App\Http\Controllers\Admin\AdminModerationController::class, 'index'])->name('moderation.index');
    Route::get('/moderation/{report}', [\App\Http\Controllers\Admin\AdminModerationController::class, 'show'])->name('moderation.show');
    Route::post('/moderation/{report}/hide', [\App\Http\Controllers\Admin\AdminModerationController::class, 'hide'])->name('moderation.hide');
    Route::post('/moderation/{report}/delete', [\App\Http\Controllers\Admin\AdminModerationController::class, 'delete'])->name('moderation.delete');
    Route::post('/moderation/{report}/dismiss', [\App\Http\Controllers\Admin\AdminModerationController::class, 'dismiss'])->name('moderation.dismiss');
    Route::post('/moderation/{report}/warn',[\App\Http\Controllers\Admin\AdminModerationController::class, 'warn'])->name('moderation.warn');
    Route::resource('vouchers', \App\Http\Controllers\Admin\AdminVoucherController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::get('/challenges', [App\Http\Controllers\Admin\AdminChallengeController::class, 'index'])->name('challenges.index');
    Route::post('/challenges', [App\Http\Controllers\Admin\AdminChallengeController::class, 'store'])->name('challenges.store');
    Route::get('/challenges/{id}/edit', [App\Http\Controllers\Admin\AdminChallengeController::class, 'edit'])->name('challenges.edit');
    Route::put('/challenges/{id}', [App\Http\Controllers\Admin\AdminChallengeController::class, 'update'])->name('challenges.update');
    Route::delete('/challenges/{id}', [App\Http\Controllers\Admin\AdminChallengeController::class, 'destroy'])->name('challenges.destroy');
});

require __DIR__.'/auth.php';
