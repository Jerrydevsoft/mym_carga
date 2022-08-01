<?php

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
    return view('welcome');
});

Auth::routes();

Route::prefix('admin')->group(function () {
    Route::get('/extraccion/import', [App\Http\Controllers\Admin\ExtractionController::class, 'import'])->name('extraccion.import');
    Route::get('/extraccion/import/subidas', [App\Http\Controllers\Admin\ExtractionController::class, 'getListChargeHeader'])->name('extraccion.data');
    Route::get('/extraccion/import/subidas/revision/{idHeader}', [App\Http\Controllers\Admin\ExtractionController::class, 'getListByHeaderId'])->name('extraccion.data.revision');
    Route::post('/extraccion/import/subidas/table', [App\Http\Controllers\Admin\ExtractionController::class, 'getTableByHeaderId'])->name('extraccion.data.table');
    Route::post('/extraccion/importData', [App\Http\Controllers\Admin\ExtractionController::class, 'importData'])->name('extraccion.importData');

    Route::get('/extraccion/exportData/Excel/{idHeader}',[App\Http\Controllers\Admin\ExtractionController::class, 'getExcelDataByHeader'])->name('extraccion.exportData.excel');
});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
