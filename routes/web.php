<?php

use App\Http\Controllers\{
    DashboardController,
    LaporanController,
    ProdukController,
    CustomerController,
    PenjualanController,
    TransaksiController,
    SettingController,
    UserController,
};
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::group(['middleware' => 'auth'], function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::group(['middleware' => 'level:1'], function () {

        Route::get('/produk/data', [ProdukController::class, 'data'])->name('produk.data');
        Route::post('/produk/delete-selected', [ProdukController::class, 'deleteSelected'])->name('produk.delete_selected');
        Route::resource('/produk', ProdukController::class);
        Route::post('/produk/store', [ProdukController::class, 'store'])->name('produk.store');

        Route::get('/customer/data', [CustomerController::class, 'data'])->name('customer.data');
        Route::get('/customer', [CustomerController::class, 'index'])->name('customer.index');
        Route::resource('/customer', CustomerController::class);
        Route::post('/customer/store', [CustomerController::class, 'store'])->name('customer.store');


        Route::get('/penjualan/data', [PenjualanController::class, 'data'])->name('penjualan.data');
        Route::get('/penjualan', [PenjualanController::class, 'index'])->name('penjualan.index');
        Route::get('/penjualan/{id}', [PenjualanController::class, 'show'])->name('penjualan.show');
        Route::delete('/penjualan/{id}', [PenjualanController::class, 'destroy'])->name('penjualan.destroy');

        Route::get('/transaksi/baru', [PenjualanController::class, 'create'])->name('transaksi.baru');
        Route::post('/transaksi/simpan', [PenjualanController::class, 'store'])->name('transaksi.simpan');
        Route::get('/transaksi/selesai', [PenjualanController::class, 'selesai'])->name('transaksi.selesai');
        Route::get('/transaksi/nota-penjualan', [PenjualanController::class, 'notaPenjualan'])->name('transaksi.nota_penjualan');

        Route::get('/transaksi/{id}/data', [TransaksiController::class, 'data'])->name('transaksi.data');
        Route::resource('/transaksi', TransaksiController::class)
            ->except('create', 'show', 'edit');

        Route::get('/user/data', [UserController::class, 'data'])->name('user.data');
        Route::get('/user/index', [UserController::class, 'data'])->name('user.index');
        Route::resource('/user', UserController::class);
        Route::get('/user/delete', [UserController::class, 'deleteSelected'])->name('user.delete');
        Route::get('/user/update', [UserController::class, 'update'])->name('user.update');

        Route::get('/setting', [SettingController::class, 'index'])->name('setting.index');

    });

    Route::group(['middleware' => 'level:1,2'], function () {
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/data/{awal}/{akhir}', [LaporanController::class, 'data'])->name('laporan.data');
        Route::get('/laporan/{id}', [PenjualanController::class, 'show'])->name('laporan.show');
    });
});
