<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\BlockController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\InterestController;
use App\Http\Controllers\MemberProfileController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\PremiumProfileController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfileWizardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SavedSearchController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\SuccessStoryController;
use App\Http\Controllers\Admin\UserPdfExportController;
use App\Http\Controllers\ThreadController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;
use App\Http\Controllers\LocationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Debug routes removed

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact', [PageController::class, 'contactSubmit'])
    ->middleware('throttle:contact')
    ->name('contact.submit');
Route::post('/contact/subscribe', [PageController::class, 'subscribe'])
    ->middleware('throttle:contact')
    ->name('contact.subscribe');
Route::get('/terms', [PageController::class, 'terms'])->name('terms');
Route::get('/privacy', [PageController::class, 'privacy'])->name('privacy');
Route::get('/faqs', [PageController::class, 'faqs'])->name('faqs');
Route::get('/career', [PageController::class, 'career'])->name('career');
Route::get('/safety', [PageController::class, 'safety'])->name('safety');
Route::get('/idltech', [PageController::class, 'idltech'])->name('idltech');

// About Us menu pages
Route::get('/hire-consultant', [PageController::class, 'service'])->defaults('page', 'hire-consultant')->name('about.hire-consultant');
Route::get('/our-services', [PageController::class, 'service'])->defaults('page', 'our-services')->name('about.our-services');
Route::get('/legal-advisor', [PageController::class, 'service'])->defaults('page', 'legal-advisor')->name('about.legal-advisor');
Route::get('/leadership', [PageController::class, 'service'])->defaults('page', 'leadership')->name('about.leadership');
Route::get('/grooming-consultant', [PageController::class, 'service'])->defaults('page', 'grooming-consultant')->name('about.grooming-consultant');
Route::get('/marriage-counseling', [PageController::class, 'service'])->defaults('page', 'marriage-counseling')->name('about.marriage-counseling');
Route::get('/affiliate', [PageController::class, 'service'])->defaults('page', 'affiliate')->name('about.affiliate');

Route::get('/coming-soon', function () {
    return view('pages.coming-soon');
})->name('coming-soon');

// Package Routes
Route::get('/packages', [\App\Http\Controllers\PackageController::class, 'index'])->name('packages.index');

Route::middleware(['auth'])->group(function () {
    Route::get('/packages/{plan}', [\App\Http\Controllers\PackageController::class, 'select'])->name('packages.select');
});

Route::get('/success-stories', [SuccessStoryController::class, 'index'])->name('success-stories.index');
Route::get('/success-stories/create', [SuccessStoryController::class, 'create'])->name('success-stories.create');
Route::post('/success-stories', [SuccessStoryController::class, 'store'])->name('success-stories.store');
Route::get('/success-stories/{successStory}', [SuccessStoryController::class, 'show'])->name('success-stories.show');

Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{blogPost}', [BlogController::class, 'show'])->name('blog.show');

Route::get('/search', [SearchController::class, 'index'])->name('profiles.index');
Route::get('/profiles/{displayId}', [MemberProfileController::class, 'show'])->name('profiles.show');

// Premium Profile Routes
Route::get('/abroad-proposals', [PremiumProfileController::class, 'abroadProposals'])->name('abroad.proposals');
Route::get('/elite-matrimonial', [PremiumProfileController::class, 'eliteMatrimonial'])->name('elite.matrimonial');



Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'verified.profile'])
    ->name('dashboard');

Route::middleware(['auth', 'verified.profile'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/update-section/{section}', [App\Http\Controllers\ProfileSectionController::class, 'update'])->name('profile.update-section');
});

Route::middleware(['auth', 'verified.profile'])->group(function () {
    Route::get('/profile/wizard/step-1', [ProfileWizardController::class, 'stepOne'])->name('profile.wizard.step-1');
    Route::post('/profile/wizard/step-1', [ProfileWizardController::class, 'storeStepOne'])->name('profile.wizard.step-1.store');
    Route::get('/profile/wizard/step-2', [ProfileWizardController::class, 'stepTwo'])->name('profile.wizard.step-2');
    Route::post('/profile/wizard/step-2', [ProfileWizardController::class, 'storeStepTwo'])->name('profile.wizard.step-2.store');
    Route::get('/profile/wizard/step-3', [ProfileWizardController::class, 'stepThree'])->name('profile.wizard.step-3');
    Route::post('/profile/wizard/step-3', [ProfileWizardController::class, 'storeStepThree'])->name('profile.wizard.step-3.store');
    Route::get('/profile/wizard/step-4', [ProfileWizardController::class, 'stepFour'])->name('profile.wizard.step-4');
    Route::post('/profile/wizard/step-4', [ProfileWizardController::class, 'storeStepFour'])->name('profile.wizard.step-4.store');
    Route::get('/profile/wizard/step-5', [ProfileWizardController::class, 'stepFive'])->name('profile.wizard.step-5');
    Route::post('/profile/wizard/step-5', [ProfileWizardController::class, 'storeStepFive'])->name('profile.wizard.step-5.store');
    Route::get('/profile/wizard/step-6', [ProfileWizardController::class, 'stepSix'])->name('profile.wizard.step-6');
    Route::post('/profile/wizard/step-6', [ProfileWizardController::class, 'storeStepSix'])->name('profile.wizard.step-6.store');
    Route::get('/profile/wizard/step-7', [ProfileWizardController::class, 'stepSeven'])->name('profile.wizard.step-7');
    Route::post('/profile/wizard/step-7', [ProfileWizardController::class, 'storeStepSeven'])->name('profile.wizard.step-7.store');
    Route::get('/profile/wizard/step-8', [ProfileWizardController::class, 'stepEight'])->name('profile.wizard.step-8');
    Route::post('/profile/wizard/step-8', [ProfileWizardController::class, 'storeStepEight'])->name('profile.wizard.step-8.store');
    Route::get('/profile/complete', [ProfileWizardController::class, 'complete'])->name('profile.complete');

    // New simplified photo/verification flow
    Route::get('/profile/photos', [ProfileWizardController::class, 'showPhotos'])->name('profile.photos');
    Route::post('/profile/photos', [ProfileWizardController::class, 'storePhotos'])->name('profile.photos.store');
    Route::get('/profile/verify', [ProfileWizardController::class, 'showVerify'])->name('profile.verify');
    Route::post('/profile/verify', [ProfileWizardController::class, 'storeVerify'])->name('profile.verify.store');


    Route::get('/profile/photo-check', [ProfileWizardController::class, 'checkPhotoStatus'])
        ->name('profile.photo-check');
    Route::post('/profile/photo-delete', [ProfileWizardController::class, 'deletePhoto'])->name('profile.photo-delete');

    // Consolidated Completion
    Route::get('/profile/complete-setup', [ProfileWizardController::class, 'completeProfile'])->name('profile.complete-setup');
    Route::post('/profile/complete-setup', [ProfileWizardController::class, 'storeCompleteProfile'])->name('profile.complete.store');

    Route::get('/profile/preferences', [ProfileWizardController::class, 'showPreferences'])->name('profile.preferences');
    Route::post('/profile/preferences', [ProfileWizardController::class, 'storePreferences'])->name('profile.preferences.store');

    Route::post('/profiles/{displayId}/favorite', [FavoriteController::class, 'store'])->name('favorites.store');
    Route::delete('/profiles/{displayId}/favorite', [FavoriteController::class, 'destroy'])->name('favorites.destroy');
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');

    Route::post('/profiles/{displayId}/block', [BlockController::class, 'store'])->name('blocks.store');
    Route::delete('/profiles/{displayId}/block', [BlockController::class, 'destroy'])->name('blocks.destroy');

    Route::post('/profiles/{displayId}/report', [ReportController::class, 'store'])->name('reports.store');

    Route::post('/interests/send', [InterestController::class, 'send'])
        ->middleware('throttle:interests')
        ->name('interests.send');
    Route::post('/interests/{interest}/accept', [InterestController::class, 'accept'])->name('interests.accept');
    Route::post('/interests/{interest}/decline', [InterestController::class, 'decline'])->name('interests.decline');

    Route::post('/saved-searches', [SavedSearchController::class, 'store'])->name('saved-searches.store');
    Route::delete('/saved-searches/{savedSearch}', [SavedSearchController::class, 'destroy'])->name('saved-searches.destroy');

    Route::get('/threads', [ThreadController::class, 'index'])->name('threads.index');
    Route::get('/threads/{thread}', [ThreadController::class, 'show'])->name('threads.show');
    Route::get('/threads/{thread}/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::post('/threads/{thread}/messages', [MessageController::class, 'store'])
        ->middleware('throttle:messages')
        ->name('messages.store');

    Route::post('/photos/{photo}/primary', [PhotoController::class, 'setPrimary'])->name('photos.primary');
    Route::delete('/photos/{photo}', [PhotoController::class, 'destroy'])->name('photos.destroy');

    Route::get('/plans/{plan}/subscribe', [SubscriptionController::class, 'create'])->name('plans.subscribe');
    Route::post('/plans/{plan}/subscribe', [SubscriptionController::class, 'store'])->name('plans.subscribe.store');
});

// Mobile Photo Upload (QR Code) - Outside auth middleware, protected by custom token
Route::get('/profile/mobile-upload/{user}/{token}', [ProfileWizardController::class, 'showMobileUpload'])
    ->middleware('throttle:30,1')
    ->name('profile.mobile-upload');
Route::post('/profile/mobile-upload/{user}/{token}', [ProfileWizardController::class, 'storeMobileUpload'])
    ->middleware('throttle:20,1')
    ->name('profile.mobile-upload.store');

Route::get('/install', function (Request $request) {
    if (app()->environment('production')) {
        abort(404);
    }

    if (!config('app.install_enabled')) {
        abort(404);
    }

    $installToken = (string) config('app.install_token', '');
    if ($installToken === '' || !hash_equals($installToken, (string) $request->query('token', ''))) {
        abort(403);
    }

    Artisan::call('migrate', ['--force' => true]);
    Artisan::call('db:seed', ['--force' => true]);

    return 'Install complete. Disable INSTALL_ENABLED in .env.';
})->name('install');


// Location Routes (AJAX)
Route::get('/locations/countries', [LocationController::class, 'getCountries']);
Route::get('/locations/states/{country}', [LocationController::class, 'getStates']);
Route::get('/locations/cities/{state}', [LocationController::class, 'getCities']);

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/users/{user}/download-pdf', [UserPdfExportController::class, 'download'])
        ->name('admin.users.download-pdf');
});

require __DIR__ . '/auth.php';

