<?php

use App\Http\Controllers\apps\asetTabungan\asetTabunganController;
use App\Http\Controllers\apps\Budgets\DaftarBudgetsController;
use App\Http\Controllers\apps\Budgets\KategoriBudgetsController;
use App\Http\Controllers\apps\BudgetsPlan\BudgetPlanController;
use App\Http\Controllers\apps\Dashboard\DashboardController;
use App\Http\Controllers\apps\kategoriTransaksi\kategoriTransaksiController;
use App\Http\Controllers\apps\master\pasien\PasienController;
use App\Http\Controllers\apps\Report\AsetTabunganController as ReportAsetTabunganController;
use App\Http\Controllers\apps\Report\BudgetReportController;
use App\Http\Controllers\apps\Report\TransaksiReportController;
use App\Http\Controllers\apps\Transaksi\TransaksiController;
use App\Http\Controllers\apps\Transer\transferController;
use App\Http\Controllers\apps\userSetting\PermissionController;
use App\Http\Controllers\apps\userSetting\RoleController;
use App\Http\Controllers\apps\userSetting\UserController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {

    Route::prefix('apps')->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

        // Users
        Route::get('/users/users', [UserController::class, 'users'])->name('users.users');
        Route::get('/users/table', [UserController::class, 'table'])->name('users.table');
        Route::post('/users/store', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{id}/update', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{id}/delete', [UserController::class, 'destroy'])->name('users.delete');
        Route::get('/users/roles/list', [UserController::class, 'listRoles'])->name('users.roles.list');
        Route::get('/users/{id}/roles', [UserController::class, 'getUserRoles'])->name('usersRoles.roles');
        Route::post('/users/{userId}/rolesAttach', [UserController::class, 'attachRoles'])->name('users.assign.roles');

        // Roles
        Route::get('/roles/utama', [RoleController::class, 'index'])->name('roles.utama');
        Route::get('/roles/table', [RoleController::class, 'table'])->name('roles.table');
        Route::post('/roles/store', [RoleController::class, 'store'])->name('roles.store');
        Route::get('/roles/{id}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('/roles/{id}/update', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('/roles/{id}/delete', [RoleController::class, 'destroy'])->name('roles.delete');
        Route::get('/roles/permissions/list', [RoleController::class, 'listPermissions'])->name('permissions.list');
        Route::get('/roles/{id}/permissions', [RoleController::class, 'getRolePermissions'])->name('roles.permissions');
        Route::post('/roles/{roleId}/permissionsAttach', [RoleController::class, 'attachPermissions'])->name('roles.assign.permissions');

        // Permissions
        Route::get('/permissions/main', [PermissionController::class, 'index'])->name('permissions.main');
        Route::get('/permissions/table', [PermissionController::class, 'table'])->name('permissions.table');
        Route::post('/permissions/store', [PermissionController::class, 'store'])->name('permissions.store');
        Route::get('/permissions/{id}/edit', [PermissionController::class, 'edit'])->name('permissions.edit');
        Route::put('/permissions/{id}/update', [PermissionController::class, 'update'])->name('permissions.update');
        Route::delete('/permissions/{id}/delete', [PermissionController::class, 'destroy'])->name('permissions.delete');

        // Kategori Transaksi
        Route::get('/kategoriTransaksi/index', [kategoriTransaksiController::class, 'kategori'])->name('kategoriTransaksi.kategori');
        Route::get('/kategoriTransaksi/table', [kategoriTransaksiController::class, 'table'])->name('kategoriTransaksi.table');
        Route::post('/kategoriTransaksi/store', [kategoriTransaksiController::class, 'store'])->name('kategoriTransaksi.store');
        Route::get('/kategoriTransaksi/{id}/edit', [kategoriTransaksiController::class, 'edit'])->name('kategoriTransaksi.edit');
        Route::put('/kategoriTransaksi/{id}/update', [kategoriTransaksiController::class, 'update'])->name('kategoriTransaksi.update');
        Route::delete('/kategoriTransaksi/{id}/delete', [kategoriTransaksiController::class, 'destroy'])->name('kategoriTransaksi.delete');

        // Aset tabungan
        Route::get('/asetTabungan/view', [asetTabunganController::class, 'view'])->name('asetTabungan.view');
        Route::get('/asetTabungan/table', [asetTabunganController::class, 'table'])->name('asetTabungan.table');
        Route::post('/asetTabungan/store', [asetTabunganController::class, 'store'])->name('asetTabungan.store');
        Route::get('/asetTabungan/{id}/edit', [asetTabunganController::class, 'edit'])->name('asetTabungan.edit');
        Route::put('/asetTabungan/{id}/update', [asetTabunganController::class, 'update'])->name('asetTabungan.update');
        Route::delete('/asetTabungan/{id}/delete', [asetTabunganController::class, 'destroy'])->name('asetTabungan.delete');
        Route::get('/asetTabungan/mutasiMasuk', [asetTabunganController::class, 'mutasiMasuk'])->name('asetTabungan.mutasiMasuk');

        // Transfer
        Route::get('/asetTabungan/transfer', [transferController::class, 'transfer'])->name('transfer.transfer');
        Route::get('/transfer/aset', [transferController::class, 'asetTabunganGetData'])->name('transfer.aset');
        Route::get('/transfer/table', [transferController::class, 'table'])->name('transfer.table');
        Route::post('/transfer/store', [transferController::class, 'store'])->name('transfer.store');
        Route::delete('/transfer/{id}/destroy', [transferController::class, 'destroy'])->name('transfer.destroy');

        // Transaksi
        Route::get('/transaksi/transaksi', [TransaksiController::class, 'transaksi'])->name('transaksi.transaksi');
        Route::get('/transaksi/getKategoriTransaksi', [TransaksiController::class, 'getKategoriTransaksi'])->name('transaksi.getKategoriTransaksi');
        Route::post('/transaksi/store', [TransaksiController::class, 'store'])->name('transaksi.store');
        Route::get('/transaksi/tabel', [TransaksiController::class, 'tabel'])->name('transaksi.tabel');
        Route::get('/transaksi/{id}/edit', [TransaksiController::class, 'edit'])->name('transaksi.edit');
        Route::put('/transaksi/{id}/update', [TransaksiController::class, 'update'])->name('transaksi.update');
        Route::delete('/transaksi/{id}/destroy', [TransaksiController::class, 'destroy'])->name('transaksi.destroy');

        // Budgets
        Route::get('/budgets/DaftarBudgets', [DaftarBudgetsController::class, 'budgets'])->name('budgets.daftarBudgets');
        Route::get('/budgets/DaftarBudgets/tabel', [DaftarBudgetsController::class, 'table'])->name('budgets.daftarBudgets.table');
        Route::post('/budgets/DaftarBudgets/store', [DaftarBudgetsController::class, 'store'])->name('budgets.daftarBudgets.store');
        Route::get('/budgets/DaftarBudgets/{id}/edit', [DaftarBudgetsController::class, 'edit'])->name('budgets.daftarBudgets.edit');
        Route::put('/budgets/DaftarBudgets/{id}/update', [DaftarBudgetsController::class, 'update'])->name('budgets.daftarBudgets.update');
        Route::delete('/budgets/DaftarBudgets/{id}/delete', [DaftarBudgetsController::class, 'destroy'])->name('budgets.daftarBudgets.delete');

        // Kategori Budgets
        Route::get('/budgets/KategoriBudgets', [KategoriBudgetsController::class, 'KategoriBudgets'])->name('KategoriBudgets.KategoriBudgets');
        Route::get('/budgets/KategoriBudgets/getKategoriPengeluaran', [KategoriBudgetsController::class, 'getKategoriTransaksi'])->name('KategoriBudgets.KategoriBudgets.getKategoriTransaksi');
        Route::get('/budgets/KategoriBudgets/getKategoriBudgets', [KategoriBudgetsController::class, 'getKategoriBudgets'])->name('KategoriBudgets.KategoriBudgets.getKategoriBudgets');
        Route::post('/budgets/KategoriBudgets/store', [KategoriBudgetsController::class, 'store'])->name('KategoriBudgets.KategoriBudgets.store');
        Route::get('/budgets/KategoriBudgets/table', [KategoriBudgetsController::class, 'table'])->name('KategoriBudgets.KategoriBudgets.table');
        Route::get('/budgets/KategoriBudgets/{id}/edit', [KategoriBudgetsController::class, 'edit'])->name('KategoriBudgets.KategoriBudgets.edit');
        Route::put('/budgets/KategoriBudgets/{id}/update', [KategoriBudgetsController::class, 'update'])->name('KategoriBudgets.KategoriBudgets.update');
        Route::delete('/budgets/KategoriBudgets/{id}/destroy', [KategoriBudgetsController::class, 'destroy'])->name('KategoriBudgets.KategoriBudgets.destroy');
        
        // Report Transaksi
        Route::get('/report/TransaksiReport', [TransaksiReportController::class, 'reportTransaksi'])->name('TransaksiReport.reportTransaksi');
        Route::get('/report/tablePengeluaran', [TransaksiReportController::class, 'tablePengeluaran'])->name('TransaksiReport.tablePengeluaran');
        Route::get('/report/tablePemasukan', [TransaksiReportController::class, 'tablePemasukan'])->name('TransaksiReport.tablePemasukan');
        Route::get('/report/tableAll', [TransaksiReportController::class, 'tableAll'])->name('TransaksiReport.tableAll');
        Route::get('/report/widgetPengeluaran', [TransaksiReportController::class, 'widgetPengeluaran'])->name('TransaksiReport.widgetPengeluaran');
        Route::get('/report/chartPengeluaran', [TransaksiReportController::class, 'chartPengeluaran'])->name('TransaksiReport.chartPengeluaran');
        Route::get('/report/widgetPemasukan', [TransaksiReportController::class, 'widgetPemasukan'])->name('TransaksiReport.widgetPemasukan');
        Route::get('/report/chartPemasukan', [TransaksiReportController::class, 'chartPemasukan'])->name('TransaksiReport.chartPemasukan');
        Route::get('/report/downloadPdf', [TransaksiReportController::class, 'downloadPDF'])->name('TransaksiReport.downloadPdf');
        Route::get('/report/previewPdf', [TransaksiReportController::class, 'previewPDF'])->name('TransaksiReport.previewPdf');

        // Report Aset Tabungan
        Route::get('/report/asetTabunganReport', [ReportAsetTabunganController::class, 'asetTabunganReport'])->name('TransaksiReport.asetTabunganReport');
        Route::get('/report/asetTabunganReport/getData', [ReportAsetTabunganController::class, 'getData'])->name('TransaksiReport.getData');
        Route::get('/report/asetTabunganReport/downloadPdf', [ReportAsetTabunganController::class, 'downloadPDF'])->name('TransaksiReport.asetTabunganReport.downloadPDF');

        // Reeport Budget
        Route::get('/report/budgetReport', [BudgetReportController::class, 'budgetReport'])->name('budgetReport.budgetReport');
        Route::get('/report/budgetReport/table', [BudgetReportController::class, 'table'])->name('budgetReport.table');
        Route::get('/report/budgetReport/BudgetPdf', [BudgetReportController::class, 'BudgetPdf'])->name('budgetReport.BudgetPdf');

        // Dashboard
        Route::get('/dashboard/budgets', [DashboardController::class, 'totalBudget'])->name('dashboard.totalBudget');
        Route::get('/dashboard/pengeluaran', [DashboardController::class, 'Pengeluaran'])->name('dashboard.Pengeluaran');
        Route::get('/dashboard/pemasukan', [DashboardController::class, 'Pemasukan'])->name('dashboard.Pemasukan');
        Route::get('/dashboard/peengeluaranPerHari', [DashboardController::class, 'PengeluaranPerHari'])->name('dashboard.peengeluaranPerHari');
        Route::get('/dashboard/peengeluaranPerBulan', [DashboardController::class, 'PengeluaranPerBulan'])->name('dashboard.peengeluaranPerBulan');

        // Budget Plan
        Route::get('/budgetPlan/budgetPlan', [BudgetPlanController::class, 'BudgetPlan'])->name('budgetPlan.budgetPlan');
        Route::get('/budgetPlan/budgetPlan/table', [BudgetPlanController::class, 'table'])->name('budgetPlan.budgetPlan.table');
        Route::post('/budgetPlan/budgetPlan/store', [BudgetPlanController::class, 'store'])->name('budgetPlan.budgetPlan.store');
        Route::get('/budgetPlan/budgetPlan/{id}/edit', [BudgetPlanController::class, 'edit'])->name('budgetPlan.budgetPlan.edit');
        Route::put('/budgetPlan/budgetPlan/{id}/update', [BudgetPlanController::class, 'update'])->name('budgetPlan.budgetPlan.update');
        Route::delete('/budgetPlan/budgetPlan/{id}/destroy', [BudgetPlanController::class, 'destroy'])->name('budgetPlan.budgetPlan.destroy');
        Route::get('/budgetPlan/budgetPlan/getBudgetPlan', [BudgetPlanController::class, 'getBudgetPlan'])->name('budgetPlan.budgetPlan.getBudgetPlan');
        Route::post('/budgetPlan/budgetPlan/deposit', [BudgetPlanController::class, 'deposit'])->name('budgetPlan.budgetPlan.deposit');

        // Budget Plan Detail
        Route::get('/budgetPlanDetail/budgetPlanDetail/view', [BudgetPlanController::class, 'PlanDetail'])->name('budgetPlan.PlanDetail');
        Route::get('/budgetPlan/budgetPlanDetail/{id}/table', [BudgetPlanController::class, 'tableDetail'])->name('budgetPlan.budgetPlanDetail.tableDetail');
        Route::get('/budgetPlan/budgetPlanDetail/{id}/edit', [BudgetPlanController::class, 'editDetail'])->name('budgetPlan.budgetPlanDetail.edit');
        Route::get('/budgetPlan/BudgetPlanDetail', [BudgetPlanController::class, 'BudgetPlanDetail'])->name('budgetPlan.BudgetPlanDetail.tableDetail');
        Route::put('/budgetPlan/BudgetPlanDetail/{id}/updateDetail', [BudgetPlanController::class, 'updateDetail'])->name('budgetPlan.BudgetPlanDetail.updateDetail');
        Route::delete('/budgetPlan/BudgetPlanDetail/{id}/deleteDetail', [BudgetPlanController::class, 'destroyDetail'])->name('budgetPlan.BudgetPlanDetail.deleteDetail');
    });


    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
