<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DealerController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VettingController;
use App\Http\Controllers\WorkflowController;
use App\Models\District;
use App\Models\License;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public Welcome Page
Route::get('/', function () {
    $stats = [
        'total_licenses' => License::count(),
        'total_districts' => District::count(),
    ];

    return view('welcome', compact('stats'));
});

// Public Verification Page
Route::get('/verify', function (Request $request) {
    $licenseNumber = $request->query('license_number');
    $license = null;
    $status = null;

    if ($licenseNumber) {
        $license = License::where('license_number', $licenseNumber)->first();
        $status = $license ? ($license->status === 'active' ? 'valid' : $license->status) : 'not_found';
    }

    $sampleLicenses = License::with('user')->latest()->take(3)->get();

    return view('verify', compact('license', 'status', 'licenseNumber', 'sampleLicenses'));
})->name('verify');

// Authentication
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/api/districts/{district}/upazilas', [AuthController::class, 'getUpazilas'])->name('api.upazilas');

Route::match(['get', 'post'], '/payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');

// Authenticated Routes
Route::middleware(['auth'])->group(function () {

    // PayStation Checkout Actions
    Route::get('/payment/initiate/{application}', [PaymentController::class, 'initiate'])->name('payment.initiate');
    Route::get('/payment/check-status/{application}', [PaymentController::class, 'checkApplicationPaymentStatus'])->name('payment.check_status');

    // Profile — available to all logged-in users
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Citizen / Dealer Applicant
    Route::middleware(['role:citizen_applicant,dealer_applicant'])->group(function () {
        Route::get('/citizen/dashboard', [ApplicationController::class, 'index'])->name('citizen.dashboard');
        Route::get('/citizen/apply', [ApplicationController::class, 'create'])->name('citizen.apply');
        Route::post('/citizen/apply', [ApplicationController::class, 'store']);
        Route::get('/citizen/applications/{application}', [ApplicationController::class, 'show'])->name('citizen.show');
        Route::get('/citizen/renew', [ApplicationController::class, 'renewalGeneral'])->name('citizen.renew_general');
        Route::get('/citizen/licenses/{license}/renew', [ApplicationController::class, 'renewalForm'])->name('citizen.renew');
        Route::post('/citizen/licenses/{license}/renew', [ApplicationController::class, 'storeRenewal']);
    });

    // Dealer Portal — dedicated routes (no /citizen/* for dealers)
    Route::middleware(['role:dealer_applicant'])->group(function () {
        Route::get('/dealer/dashboard', [DealerController::class, 'dashboard'])->name('dealer.dashboard');
        Route::get('/dealer/apply', [DealerController::class, 'applyForm'])->name('dealer.apply');
        Route::post('/dealer/apply', [DealerController::class, 'applyStore'])->name('dealer.apply.store');
        Route::get('/dealer/renew', [DealerController::class, 'renewForm'])->name('dealer.renew');
        Route::get('/dealer/stock-ledger', [DealerController::class, 'stockLedger'])->name('dealer.stock_ledger');
        Route::post('/dealer/stock-ledger', [DealerController::class, 'saveStock'])->name('dealer.stock_ledger.save');
        Route::delete('/dealer/stock-ledger/{stock}', [DealerController::class, 'deleteStock'])->name('dealer.stock_ledger.delete');
    });

    // DC Front Desk
    Route::middleware(['role:dc_front_desk'])->group(function () {
        Route::get('/office/front-desk', [WorkflowController::class, 'frontDeskDashboard'])->name('front_desk.dashboard');
        Route::get('/office/front-desk/applications/{application}', [WorkflowController::class, 'applicationDetail'])->name('front_desk.show');
        Route::post('/office/front-desk/applications/{application}', [WorkflowController::class, 'frontDeskAction'])->name('front_desk.action');
    });

    // DC JM Branch
    Route::middleware(['role:dc_jm_branch'])->group(function () {
        Route::get('/office/jm-branch', [WorkflowController::class, 'jmBranchDashboard'])->name('jm_branch.dashboard');
        Route::get('/office/jm-branch/applications/{application}', [WorkflowController::class, 'applicationDetail'])->name('jm_branch.show');
        Route::post('/office/jm-branch/applications/{application}', [WorkflowController::class, 'jmBranchAction'])->name('jm_branch.action');
    });

    // District Commissioner
    Route::middleware(['role:district_commissioner'])->group(function () {
        Route::get('/office/dc', [WorkflowController::class, 'dcDashboard'])->name('dc.dashboard');
        Route::get('/office/dc/applications/{application}', [WorkflowController::class, 'applicationDetail'])->name('dc.show');
        Route::post('/office/dc/applications/{application}', [WorkflowController::class, 'dcAction'])->name('dc.action');
    });

    // Vetting Agencies (Police, SB, NSI, DGFI)
    Route::middleware(['role:police_officer,special_branch,nsi_officer,dgfi_officer'])->group(function () {
        Route::get('/office/vetting', [VettingController::class, 'index'])->name('vetting.dashboard');
        Route::get('/office/vetting/reports/{vetting}', [VettingController::class, 'show'])->name('vetting.show');
        Route::post('/office/vetting/reports/{vetting}', [VettingController::class, 'submit'])->name('vetting.submit');
    });

    // MoHA Desk & Committee
    Route::middleware(['role:moha_desk,joint_secretary,senior_secretary,national_screening_committee'])->group(function () {
        Route::get('/office/moha', [WorkflowController::class, 'mohaDashboard'])->name('moha.dashboard');
        Route::get('/office/moha/applications/{application}', [WorkflowController::class, 'applicationDetail'])->name('moha.show');
        Route::post('/office/moha/applications/{application}', [WorkflowController::class, 'mohaAction'])->name('moha.action');
    });

    // Executive Dashboard
    Route::middleware(['role:executive'])->group(function () {
        Route::get('/office/executive', [WorkflowController::class, 'executiveDashboard'])->name('executive.dashboard');
        Route::get('/office/executive/licenses', [WorkflowController::class, 'allLicenses'])->name('executive.licenses');
        Route::get('/office/executive/dealers', [DealerController::class, 'executiveDealers'])->name('executive.dealers');
        Route::get('/office/executive/dealing-central', [DealerController::class, 'dealingCentral'])->name('executive.dealing_central');
    });

    // System Administrator
    Route::middleware(['role:system_admin'])->group(function () {
        Route::get('/admin', [AdminController::class, 'userManagement'])->name('admin.dashboard');
        Route::post('/admin/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
        Route::post('/admin/users/{user}/toggle', [AdminController::class, 'toggleUser'])->name('admin.users.toggle');
        Route::get('/admin/users/{user}/edit', [AdminController::class, 'editUser'])->name('admin.users.edit');
        Route::put('/admin/users/{user}', [AdminController::class, 'updateUser'])->name('admin.users.update');
        Route::delete('/admin/users/{user}', [AdminController::class, 'destroyUser'])->name('admin.users.destroy');
        Route::post('/admin/acl', [AdminController::class, 'saveAcl'])->name('admin.acl.save');
        Route::post('/admin/acl/role', [AdminController::class, 'addCustomRole'])->name('admin.acl.role.store');
        Route::post('/admin/api-config', [AdminController::class, 'saveApiConfig'])->name('admin.api_config.save');
        Route::get('/admin/fee-config', [AdminController::class, 'feeConfig'])->name('admin.fee_config');
        Route::post('/admin/fee-config', [AdminController::class, 'saveFeeConfig'])->name('admin.fee_config.save');
        Route::get('/admin/acl', [AdminController::class, 'acl'])->name('admin.acl');
        Route::get('/admin/api-config', [AdminController::class, 'apiConfig'])->name('admin.api_config');
        Route::get('/admin/audit-log', [AdminController::class, 'auditLog'])->name('admin.audit_log');
        Route::get('/admin/reports', [AdminController::class, 'reports'])->name('admin.reports');
    });
});
