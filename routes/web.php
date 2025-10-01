<?php

use App\Http\Controllers\ActivityAdditionalController;
use App\Http\Controllers\ActivityGensetController;
use App\Http\Controllers\ActivityHarianTowerController;
use App\Http\Controllers\ActivityHarianUnitController;
use App\Http\Controllers\ActivityPergantianBarangController;
use App\Http\Controllers\ActivityTowerController;
use App\Http\Controllers\ActivityUnitController;
use App\Http\Controllers\AllActivityController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\BarangKeluarController;
use App\Http\Controllers\BarangMasukController;
use App\Http\Controllers\DailyActivityController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ListAccessPointController;
use App\Http\Controllers\ListActionProblemController;
use App\Http\Controllers\ListActivityController;
use App\Http\Controllers\ListAreaController;
use App\Http\Controllers\ListDescriptionProblemController;
use App\Http\Controllers\ListRequestAtController;
use App\Http\Controllers\ListStatusController;
use App\Http\Controllers\ListTowerController;
use App\Http\Controllers\ListUnitController;
use App\Http\Controllers\MaintenanceTowerController;
use App\Http\Controllers\MaintenanceUnitController;
use App\Http\Controllers\MonthlyActivityController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RitationPerHourController;
use App\Http\Controllers\SummaryRitationController;
use App\Http\Controllers\WeeklyActivityController;
use App\Http\Controllers\WeeklyPlanController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login/post', [AuthController::class, 'login_post'])->name('login.post');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::group(['middleware' => ['auth']], function(){
    Route::get('/dashboards', [DashboardController::class, 'index'])->name('dashboard.index');

    //List Activity
    Route::get('/listActivity', [ListActivityController::class, 'index'])->name('listActivity.index');
    Route::post('/listActivity/insert', [ListActivityController::class, 'insert'])->name('listActivity.insert');
    Route::get('/listActivity/update/{uuid}', [ListActivityController::class, 'update'])->name('listActivity.update');

    //List Description Problem
    Route::get('/listDescriptionProblem', [ListDescriptionProblemController::class, 'index'])->name('listDescriptionProblem.index');
    Route::post('/listDescriptionProblem/insert', [ListDescriptionProblemController::class, 'insert'])->name('listDescriptionProblem.insert');
    Route::get('/listDescriptionProblem/update/{uuid}', [ListDescriptionProblemController::class, 'update'])->name('listDescriptionProblem.update');

    //List Action Problem
    Route::get('/listActionProblem', [ListActionProblemController::class, 'index'])->name('listActionProblem.index');
    Route::post('/listActionProblem/insert', [ListActionProblemController::class, 'insert'])->name('listActionProblem.insert');
    Route::get('/listActionProblem/update/{uuid}', [ListActionProblemController::class, 'update'])->name('listActionProblem.update');

    //List Status
    Route::get('/listStatus', [ListStatusController::class, 'index'])->name('listStatus.index');
    Route::post('/listStatus/insert', [ListStatusController::class, 'insert'])->name('listStatus.insert');
    Route::get('/listStatus/update/{uuid}', [ListStatusController::class, 'update'])->name('listStatus.update');

    //List Request At
    Route::get('/listRequestAt', [ListRequestAtController::class, 'index'])->name('listRequestAt.index');
    Route::post('/listRequestAt/insert', [ListRequestAtController::class, 'insert'])->name('listRequestAt.insert');
    Route::get('/listRequestAt/update/{uuid}', [ListRequestAtController::class, 'update'])->name('listRequestAt.update');

    //List Area
    Route::get('/listArea', [ListAreaController::class, 'index'])->name('listArea.index');
    Route::post('/listArea/insert', [ListAreaController::class, 'insert'])->name('listArea.insert');
    Route::get('/listArea/update/{uuid}', [ListAreaController::class, 'update'])->name('listArea.update');

    //List Tower
    Route::get('/listTower', [ListTowerController::class, 'index'])->name('listTower.index');
    Route::post('/listTower/insert', [ListTowerController::class, 'insert'])->name('listTower.insert');
    Route::get('/listTower/update/{uuid}', [ListTowerController::class, 'update'])->name('listTower.update');

    //List Access Point
    Route::get('/listAccessPoint', [ListAccessPointController::class, 'index'])->name('listAccessPoint.index');
    Route::post('/listAccessPoint/insert', [ListAccessPointController::class, 'insert'])->name('listAccessPoint.insert');
    Route::get('/listAccessPoint/update/{uuid}', [ListAccessPointController::class, 'update'])->name('listAccessPoint.update');

    //List Unit
    Route::get('/listUnit', [ListUnitController::class, 'index'])->name('listUnit.index');
    Route::post('/listUnit/insert', [ListUnitController::class, 'insert'])->name('listUnit.insert');
    Route::get('/listUnit/update/{uuid}', [ListUnitController::class, 'update'])->name('listUnit.update');

    //Inventory
    Route::get('/inventory', [BarangController::class, 'index'])->name('barang.index');
    Route::post('/inventory/post', [BarangController::class, 'post'])->name('barang.post');
    Route::get('/inventory/delete/{uuid}', [BarangController::class, 'delete'])->name('barang.delete');

    //Inventory Incoming
    Route::get('/inventory/incoming', [BarangMasukController::class, 'index'])->name('barangMasuk.index');
    Route::get('/inventory/incoming/insert', [BarangMasukController::class, 'insert'])->name('barangMasuk.insert');
    Route::post('/inventory/incoming/post', [BarangMasukController::class, 'post'])->name('barangMasuk.post');
    Route::get('/inventory/incoming/delete/{uuid}', [BarangMasukController::class, 'delete'])->name('barangMasuk.delete');
    Route::get('/inventory/incoming/edit/{uuid}', [BarangMasukController::class, 'edit'])->name('barangMasuk.edit');
    Route::post('/inventory/incoming/update/{uuid}', [BarangMasukController::class, 'update'])->name('barangMasuk.update');

    //Inventory Incoming
    Route::get('/inventory/outgoing', [BarangKeluarController::class, 'index'])->name('barangKeluar.index');
    Route::get('/inventory/outgoing/insert', [BarangKeluarController::class, 'insert'])->name('barangKeluar.insert');
    Route::post('/inventory/outgoing/post', [BarangKeluarController::class, 'post'])->name('barangKeluar.post');
    Route::get('/inventory/outgoing/delete/{uuid}', [BarangKeluarController::class, 'delete'])->name('barangKeluar.delete');
    Route::get('/inventory/outgoing/edit/{uuid}', [BarangKeluarController::class, 'edit'])->name('barangKeluar.edit');
    Route::post('/inventory/outgoing/update/{uuid}', [BarangKeluarController::class, 'update'])->name('barangKeluar.update');

    //All Activity
    Route::get('/allActivity', [AllActivityController::class, 'index'])->name('allActivity.index');
    Route::get('/allActivity/api', [AllActivityController::class, 'api'])->name('allActivity.api');

    //Daily Activity
    Route::get('/dailyActivity', [DailyActivityController::class, 'index'])->name('dailyActivity.index');

    //Weekly Activity
    Route::get('/weeklyActivity', [WeeklyActivityController::class, 'index'])->name('weeklyActivity.index');

    //Monthly Activity
    Route::get('/monthlyActivity', [MonthlyActivityController::class, 'index'])->name('monthlyActivity.index');

    //Plan Weekly
    Route::get('/weeklyPlan', [WeeklyPlanController::class, 'index'])->name('weeklyPlan.index');
    Route::get('/weeklyPlan/insert', [WeeklyPlanController::class, 'insert'])->name('weeklyPlan.insert');
    Route::get('/weeklyPlan/delete/{uuid}', [WeeklyPlanController::class, 'delete'])->name('weeklyPlan.delete');
    Route::post('/weeklyPlan/post', [WeeklyPlanController::class, 'post'])->name('weeklyPlan.post');

    //Maintenance Unit
    Route::get('/maintenanceUnit', [MaintenanceUnitController::class, 'index'])->name('maintenanceUnit.index');

    //Maintenance Unit
    Route::get('/maintenanceTower', [MaintenanceTowerController::class, 'index'])->name('maintenanceTower.index');

    //Summary Ritation
    Route::get('/summaryRitation', [SummaryRitationController::class, 'index'])->name('summaryRitation.index');

    //Activity Tower
    Route::get('/ritationPerHour', [RitationPerHourController::class, 'index'])->name('ritationPerHour.index');
    Route::get('/ritationPerHour/edit', [RitationPerHourController::class, 'edit'])->name('ritationPerHour.edit');
    Route::post('/ritationPerHour/update', [RitationPerHourController::class, 'update'])->name('ritationPerHour.update');

    //Activity Tower
    Route::get('/activityTower', [ActivityTowerController::class, 'index'])->name('activityTower.index');
    Route::get('/activityTower/insert', [ActivityTowerController::class, 'insert'])->name('activityTower.insert');
    Route::post('/activityTower/post', [ActivityTowerController::class, 'post'])->name('activityTower.post');
    Route::post('/activityTower/update', [ActivityTowerController::class, 'update'])->name('activityTower.update');
    Route::get('/activityTower/edit', [ActivityTowerController::class, 'edit'])->name('activityTower.edit');
    Route::get('/activityTower/detail', [ActivityTowerController::class, 'detail'])->name('activityTower.detail');
    Route::get('/activityTower/delete/{uuid}', [ActivityTowerController::class, 'delete'])->name('activityTower.delete');
    Route::post('/activityTower/updateWorker/{uuid}', [ActivityTowerController::class, 'updateWorker'])->name('activityTower.updateWorker');

    //Activity Unit
    Route::get('/activityUnit', [ActivityUnitController::class, 'index'])->name('activityUnit.index');
    Route::get('/activityUnit/insert', [ActivityUnitController::class, 'insert'])->name('activityUnit.insert');
    Route::post('/activityUnit/post', [ActivityUnitController::class, 'post'])->name('activityUnit.post');
    Route::post('/activityUnit/update', [ActivityUnitController::class, 'update'])->name('activityUnit.update');
    Route::get('/activityUnit/detail', [ActivityUnitController::class, 'detail'])->name('activityUnit.detail');
    Route::get('/activityUnit/edit', [ActivityUnitController::class, 'edit'])->name('activityUnit.edit');
    Route::get('/activityUnit/delete/{uuid}', [ActivityUnitController::class, 'delete'])->name('activityUnit.delete');
    Route::post('/activityUnit/updateWorker/{uuid}', [ActivityUnitController::class, 'updateWorker'])->name('activityUnit.updateWorker');

    //Activity Genset
    Route::get('/activityGenset', [ActivityGensetController::class, 'index'])->name('activityGenset.index');
    Route::get('/activityGenset/insert', [ActivityGensetController::class, 'insert'])->name('activityGenset.insert');
    Route::post('/activityGenset/post', [ActivityGensetController::class, 'post'])->name('activityGenset.post');
    Route::get('/activityGenset/edit/{uuid}', [ActivityGensetController::class, 'edit'])->name('activityGenset.edit');
    Route::post('/activityGenset/update/{uuid}', [ActivityGensetController::class, 'update'])->name('activityGenset.update');
    Route::get('/activityGenset/delete/{uuid}', [ActivityGensetController::class, 'delete'])->name('activityGenset.delete');

    //Activity Additional
    Route::get('/activityAdditional', [ActivityAdditionalController::class, 'index'])->name('activityAdditional.index');
    Route::get('/activityAdditional/insert', [ActivityAdditionalController::class, 'insert'])->name('activityAdditional.insert');
    Route::post('/activityAdditional/post', [ActivityAdditionalController::class, 'post'])->name('activityAdditional.post');
    Route::post('/activityAdditional/update/{uuid}', [ActivityAdditionalController::class, 'update'])->name('activityAdditional.update');
    Route::get('/activityAdditional/delete/{uuid}', [ActivityAdditionalController::class, 'delete'])->name('activityAdditional.delete');

     //Activity Pergantian Barang
    Route::get('/activityPergantianBarang', [ActivityPergantianBarangController::class, 'index'])->name('activityPergantianBarang.index');
    Route::get('/activityPergantianBarang/insert', [ActivityPergantianBarangController::class, 'insert'])->name('activityPergantianBarang.insert');
    Route::post('/activityPergantianBarang/post', [ActivityPergantianBarangController::class, 'post'])->name('activityPergantianBarang.post');
    Route::get('/activityPergantianBarang/edit/{uuid}', [ActivityPergantianBarangController::class, 'edit'])->name('activityPergantianBarang.edit');
    Route::post('/activityPergantianBarang/update/{uuid}', [ActivityPergantianBarangController::class, 'update'])->name('activityPergantianBarang.update');
    Route::get('/activityPergantianBarang/delete/{uuid}', [ActivityPergantianBarangController::class, 'delete'])->name('activityPergantianBarang.delete');

    //Activity Harian Tower
    Route::get('/activityHarianTower', [ActivityHarianTowerController::class, 'index'])->name('activityHarianTower.index');
    Route::get('/activityHarianTower/insert', [ActivityHarianTowerController::class, 'insert'])->name('activityHarianTower.insert');

    //Activity Harian Unit
    Route::get('/activityHarianUnit', [ActivityHarianUnitController::class, 'index'])->name('activityHarianUnit.index');
    Route::get('/activityHarianUnit/insert', [ActivityHarianUnitController::class, 'insert'])->name('activityHarianUnit.insert');

    //Profile Controller
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile/changeAvatar', [ProfileController::class, 'changeAvatar'])->name('profile.changeAvatar');
    Route::post('/profile/changePassword', [ProfileController::class, 'changePassword'])->name('profile.changePassword');
});
