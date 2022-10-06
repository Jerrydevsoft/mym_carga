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
    return redirect()->to(url('login'));
})->middleware('auth');

Auth::routes();

Route::group(['prefix' => 'admin', 'middleware' => ['auth']],function () {
    Route::get('/extraccion/import', [App\Http\Controllers\Admin\ExtractionController::class, 'import'])->name('extraccion.import');
    Route::get('/extraccion/import/subidas', [App\Http\Controllers\Admin\ExtractionController::class, 'showChargeHeader'])->name('extraccion.data');
    Route::post('/extraccion/import/subidas/list', [App\Http\Controllers\Admin\ExtractionController::class, 'getListChargeHeader'])->name('extraccion.data.list');
    Route::get('/extraccion/import/subidas/revision/{idHeader}', [App\Http\Controllers\Admin\ExtractionController::class, 'getListByHeaderId'])->name('extraccion.data.revision');
    Route::post('/extraccion/import/subidas/reprocesar', [App\Http\Controllers\Admin\ExtractionController::class, 'repeatProcess'])->name('extraccion.data.reprocesar');
    Route::post('/extraccion/import/subidas/upload', [App\Http\Controllers\Admin\ExtractionController::class, 'getListDataUpload'])->name('extraccion.data.upload');
    Route::post('/extraccion/import/subidas/upload/action', [App\Http\Controllers\Admin\ExtractionController::class, 'executeActionData'])->name('extraccion.data.upload.action');
    Route::post('/extraccion/import/subidas/table', [App\Http\Controllers\Admin\ExtractionController::class, 'getTableByHeaderId'])->name('extraccion.data.table');
    Route::post('/extraccion/importData', [App\Http\Controllers\Admin\ExtractionController::class, 'importData'])->name('extraccion.importData');
    Route::post('/extraccion/subidas/mostrarResultado', [App\Http\Controllers\Admin\ExtractionController::class, 'showDetailResult'])->name('extraccion.data.mostrar.result');
    Route::get('/extraccion/import/marcas', [App\Http\Controllers\Admin\ExtractionController::class, 'showBrands'])->name('extraccion.import.brand');
    Route::post('/extraccion/import/marcas/list',[App\Http\Controllers\Admin\ExtractionController::class, 'getListBrands'])->name('extraccion.import.brand.list');
    Route::post('/extraccion/import/marcas/edit',[App\Http\Controllers\Admin\ExtractionController::class, 'editBrandById'])->name('extraccion.import.brand.edit');
    Route::post('/extraccion/import/marcas/importData', [App\Http\Controllers\Admin\ExtractionController::class, 'importBrandData'])->name('extraccion.importBrandData');
    Route::get('/extraccion/import/marcas/articulos/{idBrand}', [App\Http\Controllers\Admin\ExtractionController::class, 'showArticlesByBrand'])->name('extraccion.import.brand.article');
    Route::post('/extraccion/import/marcas/articulos/list',[App\Http\Controllers\Admin\ExtractionController::class, 'getListArticlesByBrandId'])->name('extraccion.import.brand.article.list');
    Route::post('/extraccion/import/marcas/articulos/importData', [App\Http\Controllers\Admin\ExtractionController::class, 'importArticleBrandData'])->name('extraccion.importArticleBrandData');
    Route::get('/logout', [App\Http\Controllers\Auth\LogoutController::class, 'perform'])->name('logout.perform');
});

Route::group(['prefix' => 'Dashboard', 'middleware' => ['auth']],function () {
    Route::get('/general',[App\Http\Controllers\Admin\DashboardController::class, 'showGeneralDashboard'])->name('dashboard.general');
});

Route::group(['prefix' => 'seguridad', 'middleware' => ['auth']],function () {
    Route::get('/usuarios',[App\Http\Controllers\Security\UsersController::class, 'showUsers'])->name('security.users');
    Route::post('/Usuarios/list',[App\Http\Controllers\Security\UsersController::class, 'getListUsers'])->name('security.users.list');
    Route::post('/Usuarios/id',[App\Http\Controllers\Security\UsersController::class, 'getUserById'])->name('security.users.id');
    Route::post('/Usuarios/save',[App\Http\Controllers\Security\UsersController::class, 'saveUser'])->name('security.users.save');
});

Route::group(['prefix' => 'catalogo', 'middleware' => ['auth']],function () {
    Route::get('/vin',[App\Http\Controllers\Catalogo\CatalogoController::class, 'showVin'])->name('catalogo.show');
});

Route::prefix('Reportes')->group(function () {
    Route::get('/reporte/exportData/Excel/{idHeader}',[App\Http\Controllers\Admin\ExtractionController::class, 'getExcelDataByHeader'])->name('reporte.exportData.excel');
    Route::get('/reporte/exportDataGeneral/Excel/{idHeader}',[App\Http\Controllers\Admin\ExtractionController::class, 'getExcelDataGeneralByHeader'])->name('reporte.exportDataGeneral.excel');
});
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

