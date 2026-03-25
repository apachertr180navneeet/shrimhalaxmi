<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Admin\{
    AdminAuthController,
    VendorController,
    JobWorkerController,    
    JobWorkerInwardController,    
    JobWorkAssignmentController,
    CustomerController,
    ItemController,
    PurchaseController,    
    OrderDispatchController,
    RoleController
};

Route::get('/', [HomeController::class, 'index'])->name('/');
Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::name('admin.')->prefix('admin')->group(function () {
    Route::get('/', [AdminAuthController::class, 'index']);
    Route::get('login', [AdminAuthController::class, 'login'])->name('login');
    Route::post('login', [AdminAuthController::class, 'postLogin'])->name('login.post');
    Route::get('forget-password', [AdminAuthController::class, 'showForgetPasswordForm'])->name('forget.password.get');
    Route::post('forget-password', [AdminAuthController::class, 'submitForgetPasswordForm'])->name('forget.password.post');
    Route::get('reset-password/{token}', [AdminAuthController::class, 'showResetPasswordForm'])->name('reset.password.get');
    Route::post('reset-password', [AdminAuthController::class, 'submitResetPasswordForm'])->name('reset.password.post');

    Route::middleware(['admin'])->group(function () {
        Route::get('dashboard', [AdminAuthController::class, 'adminDashboard'])->name('dashboard');
        Route::get('change-password', [AdminAuthController::class, 'changePassword'])->name('change.password');
        Route::post('update-password', [AdminAuthController::class, 'updatePassword'])->name('update.password');
        Route::get('logout', [AdminAuthController::class, 'logout'])->name('logout');
        Route::get('profile', [AdminAuthController::class, 'adminProfile'])->name('profile');
        Route::post('profile', [AdminAuthController::class, 'updateAdminProfile'])->name('update.profile');

        Route::prefix('vendors')->name('vendors.')->group(function () {
            Route::get('/', [VendorController::class, 'index'])->name('index');
            Route::get('/getall', [VendorController::class, 'getAll'])->name('getall');
            Route::get('/create', [VendorController::class, 'create'])->name('create');
            Route::post('/store', [VendorController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [VendorController::class, 'edit'])->name('edit');
            Route::post('/update/{id}', [VendorController::class, 'update'])->name('update');
            Route::post('/status', [VendorController::class, 'changeStatus'])->name('status');
            Route::delete('/delete/{id}', [VendorController::class, 'delete'])->name('delete');
        });

        Route::prefix('jobworkers')->name('jobworkers.')->group(function () {
            Route::get('/', [JobWorkerController::class, 'index'])->name('index');
            Route::get('/getall', [JobWorkerController::class, 'getAll'])->name('getall');
            Route::get('/create', [JobWorkerController::class, 'create'])->name('create');
            Route::post('/store', [JobWorkerController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [JobWorkerController::class, 'edit'])->name('edit');
            Route::post('/update/{id}', [JobWorkerController::class, 'update'])->name('update');
            Route::post('/status', [JobWorkerController::class, 'changeStatus'])->name('status');
            Route::delete('/delete/{id}', [JobWorkerController::class, 'delete'])->name('delete');
        });

        Route::prefix('customers')->name('customers.')->group(function () {
            Route::get('/', [CustomerController::class, 'index'])->name('index');
            Route::get('/getall', [CustomerController::class, 'getAll'])->name('getall');
            Route::get('/create', [CustomerController::class, 'create'])->name('create');
            Route::post('/store', [CustomerController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [CustomerController::class, 'edit'])->name('edit');
            Route::post('/update/{id}', [CustomerController::class, 'update'])->name('update');
            Route::post('/status', [CustomerController::class, 'changeStatus'])->name('status');
            Route::delete('/delete/{id}', [CustomerController::class, 'delete'])->name('delete');
        });

        Route::prefix('items')->name('items.')->group(function () {
            Route::get('/', [ItemController::class, 'index'])->name('index');
            Route::get('/getall', [ItemController::class, 'getAll'])->name('getall');
            Route::get('/create', [ItemController::class, 'create'])->name('create');
            Route::post('/store', [ItemController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [ItemController::class, 'edit'])->name('edit');
            Route::post('/update/{id}', [ItemController::class, 'update'])->name('update');
            Route::post('/status', [ItemController::class, 'changeStatus'])->name('status');
            Route::delete('/delete/{id}', [ItemController::class, 'delete'])->name('delete');
        });

        Route::prefix('purchases')->name('purchases.')->group(function () {
            Route::get('/', [PurchaseController::class, 'index'])->name('index');
            Route::get('/getall', [PurchaseController::class, 'getAll'])->name('getall');
            Route::get('/create', [PurchaseController::class, 'create'])->name('create');
            Route::post('/store', [PurchaseController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [PurchaseController::class, 'edit'])->name('edit');
            Route::post('/update/{id}', [PurchaseController::class, 'update'])->name('update');
            Route::delete('/delete/{id}', [PurchaseController::class, 'delete'])->name('delete');
        });

        Route::prefix('order-dispatches')->name('orderdispatches.')->group(function () {
            Route::get('/', [OrderDispatchController::class, 'index'])->name('index');
            Route::get('/getall', [OrderDispatchController::class, 'getAll'])->name('getall');
            Route::get('/create', [OrderDispatchController::class, 'create'])->name('create');
            Route::post('/store', [OrderDispatchController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [OrderDispatchController::class, 'edit'])->name('edit');
            Route::post('/update/{id}', [OrderDispatchController::class, 'update'])->name('update');
            Route::delete('/delete/{id}', [OrderDispatchController::class, 'delete'])->name('delete');
        });

        Route::prefix('job-work-assignments')->name('jobworkassignments.')->group(function () {
            Route::get('/', [JobWorkAssignmentController::class, 'index'])->name('index');
            Route::get('/getall', [JobWorkAssignmentController::class, 'getAll'])->name('getall');
            Route::get('/create', [JobWorkAssignmentController::class, 'create'])->name('create');
            Route::post('/store', [JobWorkAssignmentController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [JobWorkAssignmentController::class, 'edit'])->name('edit');
            Route::post('/update/{id}', [JobWorkAssignmentController::class, 'update'])->name('update');
            Route::delete('/delete/{id}', [JobWorkAssignmentController::class, 'delete'])->name('delete');
        });

        Route::prefix('job-worker-inwards')->name('jobworkerinwards.')->group(function () {
            Route::get('/', [JobWorkerInwardController::class, 'index'])->name('index');
            Route::get('/getall', [JobWorkerInwardController::class, 'getAll'])->name('getall');
            Route::get('/create', [JobWorkerInwardController::class, 'create'])->name('create');
            Route::post('/store', [JobWorkerInwardController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [JobWorkerInwardController::class, 'edit'])->name('edit');
            Route::post('/update/{id}', [JobWorkerInwardController::class, 'update'])->name('update');
            Route::delete('/delete/{id}', [JobWorkerInwardController::class, 'delete'])->name('delete');
        });


        Route::prefix('roles')->name('roles.')->group(function () {

            Route::get('/', [RoleController::class, 'index'])->name('index');
            Route::get('/create', [RoleController::class, 'create'])->name('create');
            Route::post('/store', [RoleController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [RoleController::class, 'edit'])->name('edit');
            Route::post('/update/{id}', [RoleController::class, 'update'])->name('update');
            Route::delete('/delete/{id}', [RoleController::class, 'destroy'])->name('delete');
            Route::get('roles/getall', [RoleController::class, 'getAll'])->name('getall');

        });
    });
});

Route::middleware(['auth'])->group(function () {
});
