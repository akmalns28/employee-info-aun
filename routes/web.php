<?php

use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\DepartemenController;
use App\Http\Controllers\DivisiController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\PermissionsController;
use App\Http\Controllers\PosisiController;
use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('login', [GoogleController::class, 'login'])->name('login');
Route::get('auth/google', [GoogleController::class, 'redirect']);
Route::get('auth/google/callback', [GoogleController::class, 'callback']);

Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    Route::post('logout', [GoogleController::class, 'logout'])->name('logout');
    Route::get('dashboard', [HomeController::class, 'dashboard'])->name('dashboard');

    Route::resource('user', UserController::class)->except(['show', 'create']);
    Route::get('lengkapi-data', [UserController::class, 'editProfile'])->name('pendaftaran.lengkapi');
    Route::post('lengkapi-data', [UserController::class, 'updateProfile'])->name('pendaftaran.update');
    Route::post('user/get-all-user', [UserController::class, 'getAllUser'])->name('user.getAllUser');
    
    Route::delete('/karyawan/bulk-delete', [KaryawanController::class, 'bulkDestroy'])->name('karyawan.bulkDestroy');
    Route::resource('karyawan', KaryawanController::class)->except('create');
    Route::post('karyawan/get-all-karyawan', [KaryawanController::class, 'getAllKaryawan'])->name('karyawan.getAllKaryawan');
    Route::post('/karyawan/import', [KaryawanController::class, 'import'])->name('karyawan.import');
    Route::post('/karyawan/import/test', [KaryawanController::class, 'testImport'])->name('karyawan.import.test');
    Route::post('/karyawan/export-qrcode', [KaryawanController::class, 'exportQrCode'])->name('karyawan.exportQrCode');
    Route::post('/karyawan/export-qrcode-zip', [KaryawanController::class, 'exportQrCodeZip'])->name('karyawan.exportQrCodeZip');

    Route::resource('departemen', DepartemenController::class)->except(['show', 'create']);
    Route::post('departemen/get-all-departemen', [DepartemenController::class, 'getAllDepartemen'])->name('departemen.getAllDepartemen');

    Route::resource('posisi', PosisiController::class);
    Route::post('posisi/get-all-posisi', [PosisiController::class, 'getAllPosisi'])->name('posisi.getAllPosisi');

    Route::resource('permissions', PermissionsController::class);
    Route::post('permissions/get-all-permissions', [PermissionsController::class, 'getAllPermissions'])->name('permissions.getAllPermissions');
    Route::resource('role', RoleController::class);
    Route::post('role/get-all-role', [RoleController::class, 'getAllRole'])->name('role.getAllRole');
});

Route::get('/id-card/{nama_lengkap}', [KaryawanController::class, 'idCard'])->name('karyawan.idCard');
