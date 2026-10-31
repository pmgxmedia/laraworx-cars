<?php

use App\Http\Controllers\Admin\AdminCampaignController;
use App\Http\Controllers\Admin\AdminClaimController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminDealershipController;
use App\Http\Controllers\Admin\AdminEnquiryController;
use App\Http\Controllers\Admin\AdminExportController;
use App\Http\Controllers\Admin\AdminPaymentController;
use App\Http\Controllers\Admin\AdminRewardController;
use App\Http\Controllers\Admin\AdminRoleController;
use App\Http\Controllers\Admin\AdminSellerController;
use App\Http\Controllers\Admin\AdminSettingController;
use App\Http\Controllers\Admin\AdminSupportController;
use App\Http\Controllers\Admin\AdminTransactionController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminVehicleController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\ClaimController;
use App\Http\Controllers\ComparisonController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Customer\CustomerDashboardController;
use App\Http\Controllers\Customer\CustomerEnquiryController;
use App\Http\Controllers\Customer\CustomerProfileController;
use App\Http\Controllers\Customer\CustomerSavedSearchController;
use App\Http\Controllers\Customer\CustomerVehicleActionController;
use App\Http\Controllers\Dealer\DealerClaimController;
use App\Http\Controllers\Dealer\DealerDashboardController;
use App\Http\Controllers\Dealer\DealerEnquiryController;
use App\Http\Controllers\Dealer\DealerVehicleController;
use App\Http\Controllers\DealershipController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SellCarController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\VehicleController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/vehicles', [VehicleController::class, 'index'])->name('vehicles.index');
Route::get('/vehicles/compare', [ComparisonController::class, 'index'])->name('vehicles.compare');
Route::get('/vehicles/{vehicle:slug}', [VehicleController::class, 'show'])->name('vehicles.show');

Route::get('/campaigns', [CampaignController::class, 'index'])->name('campaigns.index');
Route::get('/campaigns/{campaign:slug}', [CampaignController::class, 'show'])->name('campaigns.show');

Route::get('/dealerships', [DealershipController::class, 'index'])->name('dealerships.index');
Route::get('/dealerships/join', [DealershipController::class, 'join'])->name('dealerships.join');
Route::post('/dealerships/join', [DealershipController::class, 'storeApplication'])->name('dealerships.join.store');
Route::get('/dealerships/{dealership:ulid}', [DealershipController::class, 'show'])->name('dealerships.show');

Route::get('/sell-your-car', [SellCarController::class, 'create'])->name('sell-car');
Route::post('/sell-your-car', [SellCarController::class, 'store'])->middleware('auth')->name('sell-car.store');

Route::get('/how-it-works', [PageController::class, 'howItWorks'])->name('how-it-works');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [ContactController::class, 'show'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
Route::get('/page/{page:slug}', [PageController::class, 'show'])->name('page.show');

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');

Route::get('/register', [RegisteredUserController::class, 'create'])->middleware('guest')->name('register');
Route::post('/register', [RegisteredUserController::class, 'store'])->middleware('guest');
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->middleware('guest')->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware('guest')->name('login.store');
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth')->name('logout');

Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->middleware('guest')->name('password.request');
Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->middleware('guest')->name('password.email');
Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->middleware('guest')->name('password.reset');
Route::post('/reset-password', [NewPasswordController::class, 'store'])->middleware('guest')->name('password.store');

Route::middleware(['auth'])->group(function () {
    Route::get('/email/verify', fn () => view('auth.verify-email'))->name('verification.notice');
    Route::middleware('signed')->get('/email/verify/{id}/{hash}', [RegisteredUserController::class, 'verifyEmail'])->name('verification.verify');
    Route::post('/email/verification-notification', [RegisteredUserController::class, 'resendVerification'])->middleware('throttle:6,1')->name('verification.send');
});

Route::middleware(['auth', 'active'])->group(function () {
    Route::prefix('my')->group(function () {
        Route::get('/', [CustomerDashboardController::class, 'index'])->name('customer.dashboard');
        Route::get('/profile', [CustomerProfileController::class, 'edit'])->name('customer.profile');
        Route::put('/profile', [CustomerProfileController::class, 'update'])->name('customer.profile.update');
        Route::get('/security', [CustomerProfileController::class, 'security'])->name('customer.security');
        Route::put('/security', [CustomerProfileController::class, 'updateSecurity'])->name('customer.security.update');
        Route::get('/saved-vehicles', [CustomerVehicleActionController::class, 'favorites'])->name('customer.favorites');
        Route::get('/enquiries', [CustomerEnquiryController::class, 'index'])->name('customer.enquiries');
        Route::get('/claims', [ClaimController::class, 'mine'])->name('customer.claims');
        Route::get('/claims/{claim}', [ClaimController::class, 'show'])->name('customer.claims.show');
        Route::get('/rewards', [ClaimController::class, 'rewards'])->name('customer.rewards');
        Route::get('/saved-searches', [CustomerSavedSearchController::class, 'index'])->name('customer.searches');
        Route::post('/saved-searches', [CustomerSavedSearchController::class, 'store'])->name('customer.searches.store');
        Route::delete('/saved-searches/{search}', [CustomerSavedSearchController::class, 'destroy'])->name('customer.searches.destroy');
        Route::get('/notifications', [CustomerDashboardController::class, 'notifications'])->name('customer.notifications');
        Route::post('/notifications/{notification}/read', [CustomerDashboardController::class, 'markRead'])->name('customer.notifications.read');
    });

    Route::post('/vehicles/{vehicle}/enquire', [VehicleController::class, 'enquire'])->name('vehicles.enquire');
    Route::post('/vehicles/{vehicle}/enquiry-response', [VehicleController::class, 'respond'])->name('vehicles.respond');
    Route::post('/vehicles/{vehicle}/favorite', [CustomerVehicleActionController::class, 'toggleFavorite'])->name('vehicles.favorite');
    Route::post('/vehicles/{vehicle}/view', [VehicleController::class, 'recordView'])->name('vehicles.view');

    Route::get('/claim/{vehicle}', [ClaimController::class, 'create'])->name('claims.create');
    Route::post('/claim/{vehicle}', [ClaimController::class, 'store'])->name('claims.store');
});

Route::middleware(['auth', 'active', 'role:dealership,private_seller'])->prefix('dealer')->group(function () {
    Route::get('/', [DealerDashboardController::class, 'index'])->name('dealer.dashboard');
    Route::get('/profile', [DealerDashboardController::class, 'profile'])->name('dealer.profile');
    Route::put('/profile', [DealerDashboardController::class, 'updateProfile'])->name('dealer.profile.update');
    Route::get('/vehicles', [DealerVehicleController::class, 'index'])->name('dealer.vehicles');
    Route::get('/vehicles/create', [DealerVehicleController::class, 'create'])->name('dealer.vehicles.create');
    Route::post('/vehicles', [DealerVehicleController::class, 'store'])->name('dealer.vehicles.store');
    Route::get('/vehicles/{vehicle}', [DealerVehicleController::class, 'edit'])->name('dealer.vehicles.edit');
    Route::put('/vehicles/{vehicle}', [DealerVehicleController::class, 'update'])->name('dealer.vehicles.update');
    Route::delete('/vehicles/{vehicle}', [DealerVehicleController::class, 'destroy'])->name('dealer.vehicles.destroy');
    Route::post('/vehicles/{vehicle}/images', [DealerVehicleController::class, 'uploadImages'])->name('dealer.vehicles.images');
    Route::delete('/vehicles/images/{image}', [DealerVehicleController::class, 'deleteImage'])->name('dealer.vehicles.image.delete');
    Route::post('/vehicles/images/reorder', [DealerVehicleController::class, 'reorderImages'])->name('dealer.vehicles.images.reorder');
    Route::post('/vehicles/{vehicle}/submit', [DealerVehicleController::class, 'submit'])->name('dealer.vehicles.submit');
    Route::get('/enquiries', [DealerEnquiryController::class, 'index'])->name('dealer.enquiries');
    Route::put('/enquiries/{enquiry}', [DealerEnquiryController::class, 'update'])->name('dealer.enquiries.update');
    Route::get('/claims', [DealerClaimController::class, 'index'])->name('dealer.claims');
    Route::post('/claims/{claim}/confirm', [DealerClaimController::class, 'confirm'])->name('dealer.claims.confirm');
    Route::get('/commissions', [DealerDashboardController::class, 'commissions'])->name('dealer.commissions');
    Route::get('/reports', [DealerDashboardController::class, 'reports'])->name('dealer.reports');
});

Route::middleware(['auth', 'active', 'admin'])->prefix(config('worxcars.admin_prefix', 'admin'))->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::resource('vehicles', AdminVehicleController::class);
    Route::post('/vehicles/{vehicle}/approve', [AdminVehicleController::class, 'approve'])->name('vehicles.approve');
    Route::post('/vehicles/{vehicle}/reject', [AdminVehicleController::class, 'reject'])->name('vehicles.reject');
    Route::post('/vehicles/{vehicle}/feature', [AdminVehicleController::class, 'toggleFeature'])->name('vehicles.feature');
    Route::post('/vehicles/{vehicle}/status', [AdminVehicleController::class, 'changeStatus'])->name('vehicles.status');
    Route::post('/vehicles/{vehicle}/set-campaign', [AdminVehicleController::class, 'setCampaign'])->name('vehicles.set-campaign');

    Route::resource('dealerships', AdminDealershipController::class);
    Route::post('/dealerships/{dealership}/verify', [AdminDealershipController::class, 'verify'])->name('dealerships.verify');
    Route::post('/dealerships/{dealership}/suspend', [AdminDealershipController::class, 'suspend'])->name('dealerships.suspend');

    Route::resource('sellers', AdminSellerController::class);
    Route::post('/sellers/{seller}/verify', [AdminSellerController::class, 'verify'])->name('sellers.verify');
    Route::post('/sellers/{seller}/suspend', [AdminSellerController::class, 'suspend'])->name('sellers.suspend');

    Route::resource('campaigns', AdminCampaignController::class);
    Route::post('/campaigns/{campaign}/status', [AdminCampaignController::class, 'changeStatus'])->name('campaigns.status');
    Route::post('/campaigns/{campaign}/add-vehicle', [AdminCampaignController::class, 'addVehicle'])->name('campaigns.add-vehicle');

    Route::get('/claims', [AdminClaimController::class, 'index'])->name('claims.index');
    Route::get('/claims/{claim}', [AdminClaimController::class, 'show'])->name('claims.show');
    Route::post('/claims/{claim}/verify', [AdminClaimController::class, 'verify'])->name('claims.verify');
    Route::post('/claims/{claim}/approve', [AdminClaimController::class, 'approve'])->name('claims.approve');
    Route::post('/claims/{claim}/reject', [AdminClaimController::class, 'reject'])->name('claims.reject');
    Route::post('/claims/{claim}/dispute', [AdminClaimController::class, 'dispute'])->name('claims.dispute');

    Route::get('/rewards', [AdminRewardController::class, 'index'])->name('rewards.index');
    Route::get('/rewards/{reward}', [AdminRewardController::class, 'show'])->name('rewards.show');
    Route::post('/rewards/{reward}/approve', [AdminRewardController::class, 'approve'])->name('rewards.approve');
    Route::post('/rewards/{reward}/reject', [AdminRewardController::class, 'reject'])->name('rewards.reject');
    Route::post('/rewards/{reward}/pay', [AdminRewardController::class, 'pay'])->name('rewards.pay');

    Route::get('/payouts', [AdminPaymentController::class, 'index'])->name('payouts.index');
    Route::post('/payouts/{payout}/execute', [AdminPaymentController::class, 'execute'])->name('payouts.execute');

    Route::get('/enquiries', [AdminEnquiryController::class, 'index'])->name('enquiries.index');
    Route::get('/enquiries/{enquiry}', [AdminEnquiryController::class, 'show'])->name('enquiries.show');
    Route::put('/enquiries/{enquiry}', [AdminEnquiryController::class, 'update'])->name('enquiries.update');

    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
    Route::post('/users/{user}/roles', [AdminUserController::class, 'assignRoles'])->name('users.roles');
    Route::post('/users/{user}/toggle', [AdminUserController::class, 'toggleActive'])->name('users.toggle');

    Route::get('/roles', [AdminRoleController::class, 'index'])->name('roles.index');
    Route::put('/roles/{role}', [AdminRoleController::class, 'update'])->name('roles.update');

    Route::get('/transactions', [AdminTransactionController::class, 'index'])->name('transactions.index');
    Route::get('/finance', [AdminTransactionController::class, 'finance'])->name('finance');

    Route::get('/pages', [AdminSupportController::class, 'pages'])->name('pages.index');
    Route::get('/pages/{page}/edit', [AdminSupportController::class, 'editPage'])->name('pages.edit');
    Route::put('/pages/{page}', [AdminSupportController::class, 'updatePage'])->name('pages.update');
    Route::get('/faqs', [AdminSupportController::class, 'faqs'])->name('faqs.index');
    Route::post('/faqs', [AdminSupportController::class, 'storeFaq'])->name('faqs.store');
    Route::put('/faqs/{faq}', [AdminSupportController::class, 'updateFaq'])->name('faqs.update');
    Route::delete('/faqs/{faq}', [AdminSupportController::class, 'destroyFaq'])->name('faqs.destroy');
    Route::get('/banners', [AdminSupportController::class, 'banners'])->name('banners.index');
    Route::post('/banners', [AdminSupportController::class, 'storeBanner'])->name('banners.store');
    Route::delete('/banners/{banner}', [AdminSupportController::class, 'destroyBanner'])->name('banners.destroy');

    Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [AdminSettingController::class, 'update'])->name('settings.update');

    Route::get('/audit-logs', [AdminDashboardController::class, 'auditLogs'])->middleware('can:view_audit_logs')->name('audit-logs');

    Route::get('/reports', [AdminExportController::class, 'index'])->name('reports.index');
    Route::get('/reports/{report}/export', [AdminExportController::class, 'export'])->name('reports.export');
});

require __DIR__.'/api.php';
