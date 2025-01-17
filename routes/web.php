<?php

use App\Exports\CategoryExport;
use App\Exports\ProductTemplateExport;
use App\Livewire\Auth\ForgotPasswordPage;
use App\Livewire\Auth\LoginPage;
use App\Livewire\Auth\RegisterPage;
use App\Livewire\Auth\ResetPasswordPage;
use App\Livewire\CancelPage;
use App\Livewire\CartPage;
use App\Livewire\CategoriesPage;
use App\Livewire\CheckoutPage;
use App\Livewire\HomePage;
use App\Livewire\MyOrdersPage;
use App\Livewire\ProductDetailPage;
use App\Livewire\ProductsPage;
use App\Livewire\SuccessPage;
use Filament\Notifications\Auth\ResetPassword;
use Filament\Support\Exceptions\Cancel;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;

//Route::get('/', function () {
//return view('welcome');
//});
Route::get('/', HomePage::class);
Route::get('/categories', CategoriesPage::class);
Route::get('/products', ProductsPage::class);
Route::get('/cart', CartPage::class);
Route::get('/products/{slug}', ProductDetailPage::class);


    Route::middleware('guest')->group(function(){
        Route::get('/login', LoginPage::class)->name('login');
        Route::get('/register', RegisterPage::class);
        Route::get('/forgot', ForgotPasswordPage::class)->name('password.request');
        Route::get('/reset/{token}', ResetPasswordPage::class)->name('password.reset');    
    });

    Route::middleware('auth')->group(function(){
        Route::get('/logout', function(){
            auth()->logout();
            return redirect('/');
        });
    Route::get('/checkout', CheckoutPage::class);
    Route::get('/my-orders', MyOrdersPage::class);
    Route::get('/success', SuccessPage::class)->name('success');
    Route::get('/cancel', CancelPage::class)->name('cancel');
});

Route::get('/export-categories', function () {
    return Excel::download(new CategoryExport, 'categories.xlsx');
})->name('categories.export');

Route::get('/download-product-template', function () {
    return Excel::download(new ProductTemplateExport, 'product-template.xlsx');
})->name('products.export-product-template');

