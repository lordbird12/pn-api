<?php

use App\Http\Controllers\AreaController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\BrandModelController;
use App\Http\Controllers\CategoryProductController;
use App\Http\Controllers\CCController;
use App\Http\Controllers\CleamHistoryController;
use App\Http\Controllers\ClientsController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\DeductPaidController;
use App\Http\Controllers\DeductTypeController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\IncomePaidController;
use App\Http\Controllers\IncomeTypeController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\PaymentPeriodController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\SubCategoryProductController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TimeAttendanceController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UploadController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

//////////////////////////////////////////web no route group/////////////////////////////////////////////////////
//Login Admin
Route::post('/login', [LoginController::class, 'login']);

Route::post('/check_login', [LoginController::class, 'checkLogin']);

//user
Route::post('/create_admin', [UserController::class, 'createUserAdmin']);
Route::post('/forgot_password_user', [UserController::class, 'ForgotPasswordUser']);

// Category Product
Route::resource('category_product', CategoryProductController::class);
Route::post('/category_product_page', [CategoryProductController::class, 'getPage']);
Route::get('/get_category_product', [CategoryProductController::class, 'getList']);

// area
Route::resource('area', AreaController::class);
Route::post('/area_page', [AreaController::class, 'getPage']);
Route::get('/get_area', [AreaController::class, 'getList']);
Route::post('/update_area', [AreaController::class, 'updateData']);

// comp
Route::resource('companie', CompanyController::class);
Route::post('/companie_page', [CompanyController::class, 'getPage']);
Route::get('/get_companie', [CompanyController::class, 'getList']);
Route::post('/update_companie', [CompanyController::class, 'updateData']);

// finance
Route::resource('finance', FinanceController::class);
Route::post('/finance_page', [FinanceController::class, 'getPage']);
Route::get('/get_finance', [FinanceController::class, 'getList']);
Route::post('/update_finance', [FinanceController::class, 'updateData']);

// Product
Route::resource('product', ProductController::class);
Route::post('/product_page', [ProductController::class, 'getPage']);
Route::get('/get_product/{id}', [ProductController::class, 'getList']);
Route::post('/update_product', [ProductController::class, 'updateData']);

// Brand
Route::resource('brand', BrandController::class);
Route::post('/brand_page', [BrandController::class, 'getPage']);
Route::get('/get_brand', [BrandController::class, 'getList']);
Route::post('/update_brand', [BrandController::class, 'updateData']);
Route::get('/get_brand_count', [BrandController::class, 'getListCount']);

// Brand Model
Route::resource('brand_model', BrandModelController::class);
Route::post('/brand_model_page', [BrandModelController::class, 'getPage']);
Route::get('/get_brand_model/{id}', [BrandModelController::class, 'getList']);
Route::post('/update_brand_model', [BrandModelController::class, 'updateData']);
Route::get('/get_brand_model_count/{id}', [BrandModelController::class, 'getListCount']);

// CC
Route::resource('c_c', CCController::class);
Route::post('/c_c_page', [CCController::class, 'getPage']);
Route::get('/get_c_c', [CCController::class, 'getList']);

// Color
Route::resource('color', ColorController::class);
Route::post('/color_page', [ColorController::class, 'getPage']);
Route::get('/get_color', [ColorController::class, 'getList']);

// Client
Route::resource('client', ClientsController::class);
Route::post('/client_page', [ClientsController::class, 'getPage']);
Route::get('/get_client', [ClientsController::class, 'getList']);
Route::post('/update_client', [ClientsController::class, 'updateData']);

// Order
Route::resource('orders', OrdersController::class);
Route::post('/orders_page', [OrdersController::class, 'getPage']);
Route::get('/get_orders', [OrdersController::class, 'getList']);

// Department
Route::resource('department', DepartmentController::class);
Route::post('/department_page', [DepartmentController::class, 'getPage']);
Route::get('/get_department', [DepartmentController::class, 'getList']);

// Postion
Route::resource('position', PositionController::class);
Route::post('/position_page', [PositionController::class, 'getPage']);
Route::get('/get_position', [PositionController::class, 'getList']);

// Supplier
Route::resource('supplier', SupplierController::class);
Route::post('/supplier_page', [SupplierController::class, 'getPage']);
Route::get('/get_supplier', [SupplierController::class, 'getList']);

// Cleam History
Route::resource('cleam', CleamHistoryController::class);
Route::post('/cleam_page', [CleamHistoryController::class, 'getPage']);
Route::get('/get_cleam/{id}', [CleamHistoryController::class, 'getList']);

// Time
Route::resource('time', TimeAttendanceController::class);
Route::post('/import_time', [TimeAttendanceController::class, 'Import']);
Route::post('/time_page', [TimeAttendanceController::class, 'getPage']);
Route::get('/get_time/{month}/{year}', [TimeAttendanceController::class, 'getList']);
Route::post('/get_time_check', [TimeAttendanceController::class, 'getTimeCheck']);

// Payment
Route::resource('payment_period', PaymentPeriodController::class);
Route::post('/payment_period_page', [PaymentPeriodController::class, 'getPage']);
Route::get('/get_payment_period/{id}', [PaymentPeriodController::class, 'getList']);
Route::post('/update_payment_period', [PaymentPeriodController::class, 'updateData']);

// Permission
Route::resource('permission', PermissionController::class);
Route::post('/permission_page', [PermissionController::class, 'getPage']);
Route::get('/get_permission', [PermissionController::class, 'getList']);
Route::post('/get_permisson_menu', [PermissionController::class, 'getPermissonMenu']);

// Transfer
Route::resource('transfer', TransferController::class);
Route::post('/transfer_page', [TransferController::class, 'getPage']);
Route::get('/get_transfer', [TransferController::class, 'getList']);
Route::post('/update_status_transfer', [TransferController::class, 'updateStatus']);

// purchase_order
Route::resource('purchase_order', PurchaseOrderController::class);
Route::post('/purchase_order_page', [PurchaseOrderController::class, 'getPage']);
Route::get('/get_purchase_order', [PurchaseOrderController::class, 'getList']);
Route::post('/update_status_purchase_order', [PurchaseOrderController::class, 'updateStatus']);

// income type
Route::resource('income', IncomeTypeController::class);
Route::post('/income_page', [IncomeTypeController::class, 'getPage']);
Route::get('/get_income', [IncomeTypeController::class, 'getList']);

// deduct type
Route::resource('deduct', DeductTypeController::class);
Route::post('/deduct_page', [DeductTypeController::class, 'getPage']);
Route::get('/get_deduct', [DeductTypeController::class, 'getList']);

// income paid
Route::resource('income_paid', IncomePaidController::class);
Route::post('/income_paid_page', [IncomePaidController::class, 'getPage']);
Route::get('/get_income_paid/{userid}/{month}', [IncomePaidController::class, 'getList']);

// deduct paid
Route::resource('deduct_paid', DeductPaidController::class);
Route::post('/deduct_paid_page', [DeductPaidController::class, 'getPage']);
Route::get('/get_deduct_paid/{userid}/{month}', [DeductPaidController::class, 'getList']);

//Main Menu
// Route::resource('main_menu', MainMenuController::class);
// Route::get('/get_main_menu', [MainMenuController::class, 'getList']);

// //Menu
// Route::resource('menu', MenuController::class);
// Route::get('/get_menu', [MenuController::class, 'getList']);

// //Menu Permission
// Route::resource('menu_permission', MenuPermissionController::class);
// Route::get('/get_menu_permission', [BannerController::class, 'getList']);
// Route::post('checkAll', [MenuPermissionController::class, 'checkAll']);

//controller
Route::post('upload_images', [Controller::class, 'uploadImages']);
Route::post('upload_file', [Controller::class, 'uploadFile']);

//user
Route::resource('user', UserController::class);
Route::get('/get_user', [UserController::class, 'getList']);
Route::post('/user_page', [UserController::class, 'getPage']);
Route::get('/user_profile', [UserController::class, 'getProfileUser']);
Route::post('/update_user', [UserController::class, 'update']);
//  Route::post('/user_page', [UserController::class, 'UserPage']);
Route::put('/reset_password_user/{id}', [UserController::class, 'ResetPasswordUser']);
Route::post('/update_profile_user', [UserController::class, 'updateProfileUser']);
Route::get('/get_profile_user', [UserController::class, 'getProfileUser']);

Route::put('/update_password_user/{id}', [UserController::class, 'updatePasswordUser']);
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////

Route::group(['middleware' => 'checkjwt'], function () {
});

Route::get('/export_pdf_payroll/{id}', [Controller::class, 'pay_slip']);

//upload

Route::post('/upload_file', [UploadController::class, 'uploadFile']);

//export pdf excel word
Route::get('/excel_payslip', [FileController::class,'excel_payslip']);
Route::get('/pdf_payslip', [FileController::class,'pdf_payslip']);
