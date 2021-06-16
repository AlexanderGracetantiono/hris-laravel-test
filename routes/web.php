<?php

// URL::forceScheme('https');
use Illuminate\Support\Facades\Route;

//Routes without session checking
Route::get('/', 'Authentication\LoginController@index')->name('login_view');
Route::get('/authentication/logout', 'Authentication\LogoutController@index')->name('logout_process');
Route::get('/forgotpassword/view', 'Authentication\ForgotPasswordController@view')->name('forgot_password_form_view');
Route::get('/forgotaccount/view', 'Authentication\ForgotAccountController@view')->name('forgot_account_form_view');
Route::get('/unblock/view', 'MasterData\Employees\UnblockController@view')->name('unblock_account_view');
Route::post('/unblock/save', 'MasterData\Employees\UnblockController@index')->name('unblock_account_send_mail');
Route::post('/authentication/login', 'Authentication\LoginController@process')->name('login_process');
Route::post('/forgotacc/send_email', 'Authentication\ForgotAccountController@send_email')->name('forgot_account_save');
// SEND EMAIL FORGOT PASSWORD
Route::post('/authentication/forgotPassword', 'Authentication\ForgotPasswordController@send_email')->name('forgot_password');
// FORM FOR FORGOT PASSWORD (WEB VIEW)
Route::get('/authentication/forgotPasswordView', 'Authentication\ForgotPasswordController@forgot_password_view')->name('forgot_password_view');
Route::post('/authentication/forgotPasswordSave', 'Authentication\ForgotPasswordController@forgot_password_save')->name('forgot_password_save');
Route::get('/authentication/verificationLogin', 'Authentication\OTPController@otp_form_view')->name('view_otp_form');
Route::post('/authentication/verificationOtp', 'Authentication\OTPController@sent_otp_code')->name('sent_otp_code');
Route::post('/authentication/resendVerificationOtp', 'Authentication\OTPController@resend_otp_code')->name('resend_verification_code');
// Activate Account
Route::get('/account_verification', 'Authentication\VerifyAccountController@index');

// Route::get('/terms-of-service', function () {
//     return redirect('http://134.209.124.184/terms-of-service');
//     // return view('terms-of-service');
// });
// Route::get('/privacy', function () {
//     return redirect('http://134.209.124.184/privacy');
//     // return view('privacy-policy');
// });

// test qr
// Route::get('/test_qr', 'Transaction\GenerateQrController@index');
// Route::get('/transaction/qr/order_qr/test_qr', 'Transaction\OrderQr\TestQrController@alpha')->name('test_download_qr');


//Detail Delivery Note
    // Route::get('/delivery_note', 'DetailDeliveryNoteController@index')->name('delivery_note'); 
    // Route::post('/login_delivery_note', 'DetailDeliveryNoteController@login')->name('login_delivery_note'); 
    // Route::get('/detail_delivery_note', 'DetailDeliveryNoteController@detail_delivery_note')->name('detail_delivery_note'); 

//Routes with session checking
// Route::group(['middleware' => 'check_user_session'], function () {
    Route::get('/home', 'HomeController@index')->name('home');
// log output
// Route::get('/log', 'Log\ViewController@index')->name('master_log');
    //Master Data Companies
    // Route::get('/master_data/chat_api/view', 'MasterData\Chat_api\ViewController@index')->name('dashboard_chat');
    // Route::get('/master_data/chat_api/delete', 'MasterData\Chat_api\DeleteController@index')->name('delete_chat');

    //Master Data Companies
        // Route::get('/master_data/companies/company/view', 'MasterData\Companies\ViewController@index')->name('master_data_company_view');
        // Route::get('/master_data/companies/company/add', 'MasterData\Companies\AddController@index')->name('master_data_company_add');
        // Route::get('/master_data/companies/company/edit', 'MasterData\Companies\EditController@index')->name('master_data_company_edit');
        // Route::get('/master_data/companies/company/edit_pic', 'MasterData\Companies\EditController@edit_pic')->name('master_data_company_edit_pic');
        // Route::post('/master_data/companies/company/save', 'MasterData\Companies\AddController@save')->name('master_data_company_save');
        // Route::get('/master_data/companies/company/verif_company', 'MasterData\Companies\AddController@verif_company')->name('master_data_company_verify');
        // Route::post('/master_data/companies/company/update', 'MasterData\Companies\EditController@update')->name('master_data_company_update');
        // Route::post('/master_data/companies/company/update_pic', 'MasterData\Companies\EditController@update_pic')->name('master_data_company_update_pic');
        // Route::post('/master_data/companies/company/delete', 'MasterData\Companies\DeleteController@index')->name('master_data_company_delete');
        // Route::get('/master_data/companies/company/update_detail', 'MasterData\Companies\EditController@change_company_by_email')->name('master_data_company_change_company_by_email');

    //Master Data Brand Company
        // Route::get('/master_data/companies/brand/view', 'MasterData\Brand\ViewController@index')->name('master_data_brand_view');
        // Route::get('/master_data/companies/brand/add', 'MasterData\Brand\AddController@index')->name('master_data_brand_add');
        // Route::get('/master_data/companies/brand/edit', 'MasterData\Brand\EditController@index')->name('master_data_brand_edit');
        // Route::post('/master_data/companies/brand/save', 'MasterData\Brand\AddController@save')->name('master_data_brand_save');
        // Route::post('/master_data/companies/brand/update', 'MasterData\Brand\EditController@update')->name('master_data_brand_update');
        // Route::post('/master_data/companies/brand/delete', 'MasterData\Brand\DeleteController@index')->name('master_data_brand_delete');
    //Upload Logo Brand 
        // Route::get('/master_data/companies/logo_brand/view', 'MasterData\LogoBrand\ViewController@index')->name('logo_brand_view');
        // Route::post('/master_data/companies/logo_brand/update', 'MasterData\LogoBrand\EditController@update')->name('logo_brand_update');
    //Transaction Order & Approval QR
        // Route::get('/transaction/qr/order_qr/view', 'Transaction\OrderQr\ViewController@index')->name('order_qr_view');
        // Route::get('/transaction/qr/order_qr/order', 'Transaction\OrderQr\AddController@index')->name('order_qr_add');
        // Route::post('/transaction/qr/order_qr/save', 'Transaction\OrderQr\AddController@save')->name('order_qr_save');
        // Route::get('/transaction/qr/order_qr/approval', 'Transaction\OrderQr\ApprovalController@index')->name('order_qr_approval');
        // Route::post('/transaction/qr/order_qr/approval_process', 'Transaction\OrderQr\ApprovalController@approval')->name('order_qr_approval_process');
        // Route::get('/transaction/qr/order_qr/qr_alpha', 'Transaction\OrderQr\DownloadQrController@alpha')->name('download_qr_alpha');
        // Route::get('/transaction/qr/order_qr/qr_zeta', 'Transaction\OrderQr\DownloadQrController@zeta')->name('download_qr_zeta');
        // Route::get('/transaction/qr/order_qr/sticker_code', 'Transaction\OrderQr\DownloadQrController@sticker_code')->name('download_sticker_code');
        // //by alex
        // Route::get('/transaction/qr/order_qr/qr_alpha_option', 'Transaction\OrderQr\QrSetting@index')->name('order_qr_alpha_option');
        // Route::get('/transaction/qr/order_qr/download_qr', 'Transaction\OrderQr\DownloadQrController@download_qr')->name('download_qr');
       
    //Dashboard PIC
        // Route::get('/dashboard', 'DashboardController@index')->name('dashboard');
        // Route::get('/dashboard/customer_scan', 'DashboardController@scan_qr_zeta')->name('dashboard_scan_zeta');
        // Route::get('/dashboard/category', 'DashboardController@category');
        // Route::get('/dashboard/product', 'DashboardController@product');
        // Route::get('/dashboard/model', 'DashboardController@model');
        // Route::get('/dashboard/version', 'DashboardController@version');
        // Route::get('/dashboard/location', 'DashboardController@location');
        // Route::get('/dashboard/download', 'DashboardController@download_excel');

    //Dashboard Admin
    //     Route::get('/dashboard_admin', 'DashboardAdminController@index')->name('dashboard_admin');
    //     Route::get('/dashboard_admin/brand', 'DashboardAdminController@brand');
    //     Route::get('/dashboard_admin/category', 'DashboardAdminController@category');
    //     Route::get('/dashboard_admin/product', 'DashboardAdminController@product');
    //     Route::get('/dashboard_admin/model', 'DashboardAdminController@model');
    //     Route::get('/dashboard_admin/version', 'DashboardAdminController@version');
    //     Route::get('/dashboard_admin/location', 'DashboardAdminController@location');
    //     Route::get('/dashboard_admin/location_zeta', 'DashboardAdminController@location_zeta');

    // //Master Data Brand Employees
    //     Route::get('/master_data/companies/employees_brand/view', 'MasterData\EmployeesBrand\ViewController@index')->name('master_data_employee_brand_view');
    //     Route::get('/master_data/companies/employees_brand/add', 'MasterData\EmployeesBrand\AddController@index')->name('master_data_employee_brand_add');
    //     Route::get('/master_data/companies/employees_brand/edit', 'MasterData\EmployeesBrand\EditController@index')->name('master_data_employee_brand_edit');
    //     Route::post('/master_data/companies/employees_brand/save', 'MasterData\EmployeesBrand\AddController@save')->name('master_data_employee_brand_save');
    //     Route::post('/master_data/companies/employees_brand/update', 'MasterData\EmployeesBrand\EditController@update')->name('master_data_employee_brand_update');
    //     Route::post('/master_data/companies/employees_brand/delete', 'MasterData\EmployeesBrand\DeleteController@index')->name('master_data_employee_brand_delete');
    
    // //Master Data Employees
        Route::get('/master_data/master_employees/view', 'MasterData\Employees\ViewController@index')->name('master_data_employee_view');
        Route::get('/master_data/master_employees/add', 'MasterData\Employees\AddController@index')->name('master_data_employee_add');
        Route::get('/master_data/master_employees/edit', 'MasterData\Employees\EditController@index')->name('master_data_employee_edit');
        Route::get('/master_data/master_employees/forgotaccount', 'MasterData\Employees\ForgotAccountName@index')->name('master_data_employee_account_name_forget');
        Route::post('/master_data/master_employees/save', 'MasterData\Employees\AddController@save')->name('master_data_employee_save');
        Route::post('/master_data/master_employees/update', 'MasterData\Employees\EditController@update')->name('master_data_employee_update');
        Route::post('/master_data/master_employees/delete', 'MasterData\Employees\DeleteController@index')->name('master_data_employee_delete');
        Route::post('/master_data/master_employees/unblock', 'MasterData\Employees\UnblockController@index')->name('master_data_employee_unblock');
        Route::post('/master_data/master_employees/forgotaccountsave', 'MasterData\Employees\ForgotAccountName@update')->name('master_data_employee_account_name_save');
        Route::post('/master_data/master_employees/reset_password', 'MasterData\Employees\ResetPasswordController@index')->name('master_data_employee_reset_password');

    // //Application Version
    //     Route::get('/application/outdated_application_version/view', 'Application\OutdatedApplicationVersion\ViewController@index')->name('outdated_application_version_view');
    //     Route::get('/application/outdated_application_version/add', 'Application\OutdatedApplicationVersion\AddController@index')->name('outdated_application_version_add');
    //     Route::get('/application/outdated_application_version/edit', 'Application\OutdatedApplicationVersion\EditController@index')->name('outdated_application_version_edit');
    //     Route::post('/application/outdated_application_version/save', 'Application\OutdatedApplicationVersion\AddController@save')->name('outdated_application_version_save');
    //     Route::post('/application/outdated_application_version/update', 'Application\OutdatedApplicationVersion\EditController@update')->name('outdated_application_version_update');
    //     Route::post('/application/outdated_application_version/delete', 'Application\OutdatedApplicationVersion\DeleteController@index')->name('outdated_application_version_delete');
    // //Legal Version
    //     Route::get('/application/legal_version/view', 'Application\LegalVersion\ViewController@index')->name('legal_version_view');
    //     Route::get('/application/legal_version/edit', 'Application\LegalVersion\EditController@index')->name('legal_version_edit');
    //     Route::post('/application/legal_version/update', 'Application\LegalVersion\EditController@update')->name('legal_version_update');
    // //Customer Report
    //     Route::get('/report/customer_qr_report/view', 'Report\CustomerReport\ViewController@index')->name('customer_report_qr_view');
    //     Route::get('/report/customer_qr_report/detail', 'Report\CustomerReport\ViewController@detail')->name('customer_report_qr_detail');
    // // 

    // // Manufacture
    //     //Master Data Backend User
    //         Route::get('/manufacture/master_data/backend_users/view', 'MasterData\BackendUsers\ViewController@index')->name('master_data_backend_user_view');
    //         Route::get('/manufacture/master_data/backend_users/detail', 'MasterData\BackendUsers\ViewController@detail')->name('master_data_backend_user_view_detail');
    //         Route::get('/manufacture/master_data/backend_users/add', 'MasterData\BackendUsers\AddController@index')->name('master_data_backend_user_add');
    //         Route::get('/manufacture/master_data/backend_users/edit', 'MasterData\BackendUsers\EditController@index')->name('master_data_backend_user_edit');
    //         Route::post('/manufacture/master_data/backend_users/save', 'MasterData\BackendUsers\AddController@save')->name('master_data_backend_user_save');
    //         Route::post('/manufacture/master_data/backend_users/update', 'MasterData\BackendUsers\EditController@update')->name('master_data_backend_user_update');
    //         // Route::post('/manufacture/master_data/backend_users/delete', 'MasterData\BackendUsers\DeleteController@index')->name('master_data_backend_user_delete');
    //         Route::get('/manufacture/master_data/backend_users/changePassword', 'MasterData\BackendUsers\ChangePasswordController@index')->name('master_data_backend_user_change_password');
    //         Route::post('/manufacture/master_data/backend_users/changePassword', 'MasterData\BackendUsers\ChangePasswordController@update')->name('master_data_backend_user_change_password_update');
    //         Route::get('/manufacture/master_data/backend_users/editProfile', 'MasterData\BackendUsers\EditProfileController@index')->name('master_data_backend_user_edit_profile');
    //         Route::post('/manufacture/master_data/backend_users/editProfile', 'MasterData\BackendUsers\EditProfileController@update')->name('master_data_backend_user_edit_profile_update');

    //     //Master Data Super Admin
    //         Route::get('/manufacture/master_data/companies/super_admin/view', 'MasterData\SuperAdmin\ViewController@index')->name('master_data_super_admin_view');
    //         Route::get('/manufacture/master_data/companies/super_admin/add', 'MasterData\SuperAdmin\AddController@index')->name('master_data_super_admin_add');
    //         Route::get('/manufacture/master_data/companies/super_admin/edit', 'MasterData\SuperAdmin\EditController@index')->name('master_data_super_admin_edit');
    //         Route::post('/manufacture/master_data/companies/super_admin/save', 'MasterData\SuperAdmin\AddController@save')->name('master_data_super_admin_save');
    //         Route::post('/manufacture/master_data/companies/super_admin/update', 'MasterData\SuperAdmin\EditController@update')->name('master_data_super_admin_update');
    //         Route::post('/manufacture/master_data/companies/super_admin/delete', 'MasterData\SuperAdmin\DeleteController@index')->name('master_data_super_admin_delete');

    //     //Master Data Admin Company
    //         Route::get('/manufacture/master_data/companies/admin_company/view', 'MasterData\AdminCompany\ViewController@index')->name('master_data_admin_company_view');
    //         Route::get('/manufacture/master_data/companies/admin_company/add', 'MasterData\AdminCompany\AddController@index')->name('master_data_admin_company_add');
    //         Route::get('/manufacture/master_data/companies/admin_company/edit', 'MasterData\AdminCompany\EditController@index')->name('master_data_admin_company_edit');
    //         Route::post('/manufacture/master_data/companies/admin_company/save', 'MasterData\AdminCompany\AddController@save')->name('master_data_admin_company_save');
    //         Route::post('/manufacture/master_data/companies/admin_company/update', 'MasterData\AdminCompany\EditController@update')->name('master_data_admin_company_update');
    //         Route::post('/manufacture/master_data/companies/admin_company/delete', 'MasterData\AdminCompany\DeleteController@index')->name('master_data_admin_company_delete');

    //     //Master Data Admin Vendor
    //         Route::get('/manufacture/master_data/companies/admin_vendor/view', 'MasterData\AdminVendor\ViewController@index')->name('master_data_admin_vendor_view');
    //         Route::get('/manufacture/master_data/companies/admin_vendor/add', 'MasterData\AdminVendor\AddController@index')->name('master_data_admin_vendor_add');
    //         Route::get('/manufacture/master_data/companies/admin_vendor/edit', 'MasterData\AdminVendor\EditController@index')->name('master_data_admin_vendor_edit');
    //         Route::post('/manufacture/master_data/companies/admin_vendor/save', 'MasterData\AdminVendor\AddController@save')->name('master_data_admin_vendor_save');
    //         Route::post('/manufacture/master_data/companies/admin_vendor/update', 'MasterData\AdminVendor\EditController@update')->name('master_data_admin_vendor_update');
    //         Route::post('/manufacture/master_data/companies/admin_vendor/delete', 'MasterData\AdminVendor\DeleteController@index')->name('master_data_admin_vendor_delete');

    //     //Master Data Categories
    //         Route::get('/manufacture/master_data/product/categories/view', 'MasterData\Categories\ViewController@index')->name('master_data_category_view');
    //         Route::get('/manufacture/master_data/product/categories/add', 'MasterData\Categories\AddController@index')->name('master_data_category_add');
    //         Route::get('/manufacture/master_data/product/categories/edit', 'MasterData\Categories\EditController@index')->name('master_data_category_edit');
    //         Route::post('/manufacture/master_data/product/categories/save', 'MasterData\Categories\AddController@save')->name('master_data_category_save');
    //         Route::post('/manufacture/master_data/product/categories/update', 'MasterData\Categories\EditController@update')->name('master_data_category_update');
    //         Route::post('/manufacture/master_data/product/categories/delete', 'MasterData\Categories\DeleteController@index')->name('master_data_category_delete');
    //     //Master Data Product
    //         Route::get('/manufacture/master_data/product/product/view', 'MasterData\Product\ViewController@index')->name('master_data_product_view');
    //         Route::get('/manufacture/master_data/product/product/add', 'MasterData\Product\AddController@index')->name('master_data_product_add');
    //         Route::get('/manufacture/master_data/product/product/edit', 'MasterData\Product\EditController@index')->name('master_data_product_edit');
    //         Route::post('/manufacture/master_data/product/product/save', 'MasterData\Product\AddController@save')->name('master_data_product_save');
    //         Route::post('/manufacture/master_data/product/product/update', 'MasterData\Product\EditController@update')->name('master_data_product_update');
    //         Route::post('/manufacture/master_data/product/product/delete', 'MasterData\Product\DeleteController@index')->name('master_data_product_delete');
    //         Route::get('/manufacture/master_data/product/product/category', 'MasterData\Product\AddController@category');
    //         Route::get('/manufacture/master_data/product/product/product', 'MasterData\Product\AddController@product');

    //     //Master Data Product Model
    //         Route::get('/manufacture/master_data/product/product_model/view', 'MasterData\ProductModel\ViewController@index')->name('master_data_product_model_view');
    //         Route::get('/manufacture/master_data/product/product_model/add', 'MasterData\ProductModel\AddController@index')->name('master_data_product_model_add');
    //         Route::get('/manufacture/master_data/product/product_model/edit', 'MasterData\ProductModel\EditController@index')->name('master_data_product_model_edit');
    //         Route::post('/manufacture/master_data/product/product_model/save', 'MasterData\ProductModel\AddController@save')->name('master_data_product_model_save');
    //         Route::post('/manufacture/master_data/product/product_model/update', 'MasterData\ProductModel\EditController@update')->name('master_data_product_model_update');
    //         Route::post('/manufacture/master_data/product/product_model/delete', 'MasterData\ProductModel\DeleteController@index')->name('master_data_product_model_delete');
    //         Route::get('/manufacture/master_data/product/product_model/category', 'MasterData\ProductModel\AddController@category');
    //         Route::get('/manufacture/master_data/product/product_model/product', 'MasterData\ProductModel\AddController@product');
    //         Route::get('/manufacture/master_data/product/product_model/model', 'MasterData\ProductModel\AddController@model');
    //     //Master Data Product Version
    //         Route::get('/manufacture/master_data/product/product_version/view', 'MasterData\ProductVersion\ViewController@index')->name('master_data_product_version_view');
    //         Route::get('/manufacture/master_data/product/product_version/add', 'MasterData\ProductVersion\AddController@index')->name('master_data_product_version_add');
    //         Route::get('/manufacture/master_data/product/product_version/edit', 'MasterData\ProductVersion\EditController@index')->name('master_data_product_version_edit');
    //         Route::post('/manufacture/master_data/product/product_version/save', 'MasterData\ProductVersion\AddController@save')->name('master_data_product_version_save');
    //         Route::post('/manufacture/master_data/product/product_version/update', 'MasterData\ProductVersion\EditController@update')->name('master_data_product_version_update');
    //         Route::post('/manufacture/master_data/product/product_version/delete', 'MasterData\ProductVersion\DeleteController@index')->name('master_data_product_version_delete');
    //         Route::get('/manufacture/master_data/product/product_version/category', 'MasterData\ProductVersion\AddController@category');
    //         Route::get('/manufacture/master_data/product/product_version/product', 'MasterData\ProductVersion\AddController@product');
    //         Route::get('/manufacture/master_data/product/product_version/model', 'MasterData\ProductVersion\AddController@model');
    //         Route::get('/manufacture/master_data/product/product_version/generate_sku', 'MasterData\ProductVersion\AddController@generate_sku');
    //     //Master Data Plant
    //         Route::get('/manufacture/master_data/companies/plant/view', 'MasterData\Plant\ViewController@index')->name('master_data_plant_view');
    //         Route::get('/manufacture/master_data/companies/plant/add', 'MasterData\Plant\AddController@index')->name('master_data_plant_add');
    //         Route::get('/manufacture/master_data/companies/plant/edit', 'MasterData\Plant\EditController@index')->name('master_data_plant_edit');
    //         Route::post('/manufacture/master_data/companies/plant/save', 'MasterData\Plant\AddController@save')->name('master_data_plant_save');
    //         Route::post('/manufacture/master_data/companies/plant/update', 'MasterData\Plant\EditController@update')->name('master_data_plant_update');
    //         Route::post('/manufacture/master_data/companies/plant/delete', 'MasterData\Plant\DeleteController@index')->name('master_data_plant_delete');
    //     //Master Data Batch Packaging
    //         Route::get('/manufacture/master_data/companies/batch_packaging/view', 'MasterData\BatchPackaging\ViewController@index')->name('master_data_batch_packaging_view');
    //         Route::get('/manufacture/master_data/companies/batch_packaging/add', 'MasterData\BatchPackaging\AddController@index')->name('master_data_batch_packaging_add');
    //         Route::get('/manufacture/master_data/companies/batch_packaging/edit', 'MasterData\BatchPackaging\EditController@index')->name('master_data_batch_packaging_edit');
    //         Route::post('/manufacture/master_data/companies/batch_packaging/save', 'MasterData\BatchPackaging\AddController@save')->name('master_data_batch_packaging_save');
    //         Route::post('/manufacture/master_data/companies/batch_packaging/update', 'MasterData\BatchPackaging\EditController@update')->name('master_data_batch_packaging_update');
    //         Route::post('/manufacture/master_data/companies/batch_packaging/delete', 'MasterData\BatchPackaging\DeleteController@index')->name('master_data_batch_packaging_delete');
    //         Route::get('/manufacture/master_data/companies/batch_packaging/get_batch_production', 'MasterData\BatchPackaging\AddController@get_batch_production');
    //         Route::get('/manufacture/master_data/companies/batch_packaging/detail', 'MasterData\BatchPackaging\ViewController@detail')->name('master_data_batch_packaging_detail');
    //         Route::get('/manufacture/master_data/companies/batch_packaging/scanned_qr', 'MasterData\BatchPackaging\ViewController@scanned_qr')->name('master_data_batch_packaging_scanned_qr');
    //         Route::post('/manufacture/master_data/companies/batch_packaging/activate', 'MasterData\BatchPackaging\ActivationBatchController@activate')->name('master_data_batch_packaging_activate');
    //         Route::post('/manufacture/master_data/companies/batch_packaging/close', 'MasterData\BatchPackaging\ActivationBatchController@close')->name('master_data_batch_packaging_close');
    //         Route::post('/manufacture/master_data/companies/batch_packaging/ready_to_sell', 'MasterData\BatchPackaging\ActivationBatchController@ready_to_sell')->name('master_data_batch_packaging_ready_to_sell');
        
    //     //Pool Product
    //         Route::get('/manufacture/master_data/companies/pool_product/view', 'MasterData\PoolProduct\ViewController@index')->name('master_data_pool_product_view');
    //         Route::get('/manufacture/master_data/companies/pool_product/detail', 'MasterData\PoolProduct\ViewController@detail')->name('master_data_pool_product_detail');
        
    //     //Master Data Sub-batch Packaging
    //         Route::get('/manufacture/master_data/companies/sub_batch_packaging/view', 'MasterData\SubBatchPackaging\ViewController@index')->name('master_data_sub_batch_packaging_view');
    //         Route::get('/manufacture/master_data/companies/sub_batch_packaging/add', 'MasterData\SubBatchPackaging\AddController@index')->name('master_data_sub_batch_packaging_add');
    //         Route::get('/manufacture/master_data/companies/sub_batch_packaging/edit', 'MasterData\SubBatchPackaging\EditController@index')->name('master_data_sub_batch_packaging_edit');
    //         Route::get('/manufacture/master_data/companies/sub_batch_packaging/edit_progress', 'MasterData\SubBatchPackaging\EditProgressController@index')->name('master_data_sub_batch_packaging_edit_progress');
    //         Route::post('/manufacture/master_data/companies/sub_batch_packaging/update_progress', 'MasterData\SubBatchPackaging\EditProgressController@update')->name('master_data_sub_batch_packaging_update_progress');
    //         Route::post('/manufacture/master_data/companies/sub_batch_packaging/save', 'MasterData\SubBatchPackaging\AddController@save')->name('master_data_sub_batch_packaging_save');
    //         Route::post('/manufacture/master_data/companies/sub_batch_packaging/update', 'MasterData\SubBatchPackaging\EditController@update')->name('master_data_sub_batch_packaging_update');
    //         Route::post('/manufacture/master_data/companies/sub_batch_packaging/delete', 'MasterData\SubBatchPackaging\DeleteController@index')->name('master_data_sub_batch_packaging_delete');
    //         Route::post('/manufacture/master_data/companies/sub_batch_packaging/delete_staff', 'MasterData\SubBatchPackaging\DeleteController@staff')->name('master_data_sub_batch_packaging_delete_staff');
    //         Route::get('/manufacture/master_data/companies/sub_batch_packaging/get_pool_product', 'MasterData\SubBatchPackaging\AddController@get_pool_product');
    //         Route::get('/manufacture/master_data/companies/sub_batch_packaging/get_staff_packaging', 'MasterData\SubBatchPackaging\AddController@get_staff_packaging');
    //         Route::get('/manufacture/master_data/companies/sub_batch_packaging/detail', 'MasterData\SubBatchPackaging\ViewController@detail')->name('master_data_sub_batch_packaging_detail');
    //         Route::get('/manufacture/master_data/companies/sub_batch_packaging/scanned_qr', 'MasterData\SubBatchPackaging\ViewController@scanned_qr')->name('master_data_sub_batch_packaging_scanned_qr');
    //         Route::get('/manufacture/master_data/companies/sub_batch_packaging/scanned_qr_closed', 'MasterData\SubBatchPackaging\ViewController@scanned_qr_closed')->name('master_data_sub_batch_packaging_scanned_qr_closed');
    //         Route::post('/manufacture/master_data/companies/sub_batch_packaging/activate', 'MasterData\SubBatchPackaging\ActivationBatchController@activate')->name('master_data_sub_batch_packaging_activate');
    //         Route::post('/manufacture/master_data/companies/sub_batch_packaging/close', 'MasterData\SubBatchPackaging\ActivationBatchController@close')->name('master_data_sub_batch_packaging_close');
    //         Route::post('/manufacture/master_data/companies/sub_batch_packaging/reject_qr', 'MasterData\SubBatchPackaging\ActivationBatchController@reject_qr')->name('master_data_sub_batch_reject_qr');
    //         Route::get('/manufacture/master_data/companies/sub_batch_packaging/delivery_note', 'MasterData\SubBatchPackaging\ViewController@delivery_note')->name('master_data_sub_batch_packaging_delivery_note');

    //     //Master Data Batch Production
    //         Route::get('/manufacture/master_data/companies/batch_production/view', 'MasterData\BatchProduction\ViewController@index')->name('master_data_batch_production_view');
    //         Route::get('/manufacture/master_data/companies/batch_production/add', 'MasterData\BatchProduction\AddController@index')->name('master_data_batch_production_add');
    //         Route::get('/manufacture/master_data/companies/batch_production/edit', 'MasterData\BatchProduction\EditController@index')->name('master_data_batch_production_edit');
    //         Route::post('/manufacture/master_data/companies/batch_production/save', 'MasterData\BatchProduction\AddController@save')->name('master_data_batch_production_save');
    //         Route::post('/manufacture/master_data/companies/batch_production/update', 'MasterData\BatchProduction\EditController@update')->name('master_data_batch_production_update');
    //         Route::post('/manufacture/master_data/companies/batch_production/delete', 'MasterData\BatchProduction\DeleteController@index')->name('master_data_batch_production_delete');
    //         Route::get('/manufacture/master_data/companies/batch_production/category', 'MasterData\BatchProduction\AddController@category');
    //         Route::get('/manufacture/master_data/companies/batch_production/product', 'MasterData\BatchProduction\AddController@product');
    //         Route::get('/manufacture/master_data/companies/batch_production/model', 'MasterData\BatchProduction\AddController@model');
    //         Route::get('/manufacture/master_data/companies/batch_production/version', 'MasterData\BatchProduction\AddController@version');
    //         Route::get('/manufacture/master_data/companies/batch_production/description', 'MasterData\BatchProduction\AddController@description');
    //         Route::get('/manufacture/master_data/companies/batch_production/get_staff_production', 'MasterData\BatchProduction\AddController@get_staff_production');
    //         Route::post('/manufacture/master_data/companies/batch_production/delete_staff', 'MasterData\BatchProduction\DeleteController@staff')->name('master_data_batch_production_delete_staff');
    //         Route::get('/manufacture/master_data/companies/batch_production/detail', 'MasterData\BatchProduction\ViewController@detail')->name('master_data_batch_production_detail');
    //         Route::get('/manufacture/master_data/companies/batch_production/detail_in_progress', 'MasterData\BatchProduction\ViewController@detail_in_progress')->name('master_data_batch_production_detail_in_progress');
    //         Route::get('/manufacture/master_data/companies/batch_production/scanned_qr', 'MasterData\BatchProduction\ViewController@scanned_qr')->name('master_data_batch_production_scanned_qr');
    //         Route::get('/manufacture/master_data/companies/batch_production/scanned_qr_closed', 'MasterData\BatchProduction\ViewController@scanned_qr_closed')->name('master_data_batch_production_scanned_qr_closed');
    //         Route::post('/manufacture/master_data/companies/batch_production/activate', 'MasterData\BatchProduction\ActivationBatchController@activate')->name('master_data_batch_production_activate');
    //         Route::post('/manufacture/master_data/companies/batch_production/close', 'MasterData\BatchProduction\ActivationBatchController@close')->name('master_data_batch_production_close');
    //         Route::get('/manufacture/master_data/companies/batch_production/delivery_note', 'MasterData\BatchProduction\ViewController@delivery_note')->name('master_data_batch_production_delivery_note');
        
    //         //Master Data Batch Delivery
    //         Route::get('/manufacture/master_data/companies/batch_delivery/view', 'MasterData\SubBatchDelivery\ViewController@index')->name('master_data_batch_delivery_view');
    //         Route::get('/manufacture/master_data/companies/batch_delivery/add', 'MasterData\SubBatchDelivery\AddController@index')->name('master_data_batch_delivery_add');
    //         Route::get('/manufacture/master_data/companies/batch_delivery/edit', 'MasterData\SubBatchDelivery\EditController@index')->name('master_data_batch_delivery_edit');
    //         Route::get('/manufacture/master_data/companies/batch_delivery/edit_progress', 'MasterData\SubBatchDelivery\EditProgressController@index')->name('master_data_batch_delivery_edit_progress');
    //         Route::post('/manufacture/master_data/companies/batch_delivery/update_progress', 'MasterData\SubBatchDelivery\EditProgressController@update')->name('master_data_batch_delivery_update_progress');
    //         Route::post('/manufacture/master_data/companies/batch_delivery/save', 'MasterData\SubBatchDelivery\AddController@save')->name('master_data_batch_delivery_save');
    //         Route::post('/manufacture/master_data/companies/batch_delivery/update', 'MasterData\SubBatchDelivery\EditController@update')->name('master_data_batch_delivery_update');
    //         Route::post('/manufacture/master_data/companies/batch_delivery/delete', 'MasterData\SubBatchDelivery\DeleteController@index')->name('master_data_batch_delivery_delete');
    //         Route::post('/manufacture/master_data/companies/batch_delivery/delete_staff', 'MasterData\SubBatchDelivery\DeleteController@staff')->name('master_data_batch_delivery_delete_staff');
    //         Route::get('/manufacture/master_data/companies/batch_delivery/get_pool_product', 'MasterData\SubBatchDelivery\AddController@get_pool_product');
    //         Route::get('/manufacture/master_data/companies/batch_delivery/get_staff_packaging', 'MasterData\SubBatchDelivery\AddController@get_staff_packaging');
    //         Route::get('/manufacture/master_data/companies/batch_delivery/detail', 'MasterData\SubBatchDelivery\ViewController@detail')->name('master_data_batch_delivery_detail');
    //         Route::get('/manufacture/master_data/companies/batch_delivery/scanned_qr', 'MasterData\SubBatchDelivery\ViewController@scanned_qr')->name('master_data_batch_delivery_scanned_qr');
    //         Route::get('/manufacture/master_data/companies/batch_delivery/scanned_qr_closed', 'MasterData\SubBatchDelivery\ViewController@scanned_qr_closed')->name('master_data_batch_delivery_scanned_qr_closed');
    //         Route::post('/manufacture/master_data/companies/batch_delivery/activate', 'MasterData\SubBatchDelivery\ActivationBatchController@activate')->name('master_data_batch_delivery_activate');
    //         Route::post('/manufacture/master_data/companies/batch_delivery/close', 'MasterData\SubBatchDelivery\ActivationBatchController@close')->name('master_data_batch_delivery_close');
    //         Route::post('/manufacture/master_data/companies/batch_delivery/reject_qr', 'MasterData\SubBatchDelivery\ActivationBatchController@reject_qr')->name('master_data_sub_batch_reject_qr');
    //         Route::get('/manufacture/master_data/companies/batch_delivery/delivery_note', 'MasterData\SubBatchDelivery\ViewController@delivery_note')->name('master_data_batch_delivery_delivery_note');
        
    //     //Master Data Batch Store
    //         Route::get('/manufacture/master_data/companies/batch_store/view', 'MasterData\BatchStore\ViewController@index')->name('master_data_batch_store_view');
    //         // Route::get('/manufacture/master_data/companies/batch_store/add', 'MasterData\BatchStore\AddController@index')->name('master_data_batch_store_add');
    //         Route::get('/manufacture/master_data/companies/batch_store/edit', 'MasterData\BatchStore\EditController@index')->name('master_data_batch_store_edit');
    //         // Route::post('/manufacture/master_data/companies/batch_store/save', 'MasterData\BatchStore\AddController@save')->name('master_data_batch_store_save');
    //         Route::post('/manufacture/master_data/companies/batch_store/update', 'MasterData\BatchStore\EditController@update')->name('master_data_batch_store_update');
    //         Route::post('/manufacture/master_data/companies/batch_store/delete', 'MasterData\BatchStore\DeleteController@index')->name('master_data_batch_store_delete');
    //         Route::get('/manufacture/master_data/companies/batch_store/sub_batch_packaging', 'MasterData\BatchStore\AddController@sub_batch_packaging');
    //         Route::get('/manufacture/master_data/companies/batch_store/detail', 'MasterData\BatchStore\ViewController@detail')->name('master_data_batch_store_detail');
    //         Route::post('/manufacture/master_data/companies/batch_store/activate', 'MasterData\BatchStore\ActivationBatchController@activate')->name('master_data_batch_store_activate');
    //         Route::post('/manufacture/master_data/companies/batch_store/ready_sale', 'MasterData\BatchStore\ActivationBatchController@ready_sale')->name('master_data_batch_store_ready_sale');
    //         Route::post('/manufacture/master_data/companies/batch_store/close', 'MasterData\BatchStore\ActivationBatchController@close')->name('master_data_batch_store_close');
        
    //     //Product Attribute
    //         Route::get('/manufacture/master_data/product/product_attribute/view', 'MasterData\ProductAttribute\EditController@index')->name('product_attribute_view');
    //         Route::get('/manufacture/master_data/product/product_attribute_custom/view', 'MasterData\ProductAttribute\EditController@custom_attribute_view')->name('product_custom_attribute_view');
    //         Route::post('/manufacture/master_data/product/product_attribute/update_general', 'MasterData\ProductAttribute\EditController@update_general')->name('product_attribute_update_general');
    //         Route::post('/manufacture/master_data/product/product_attribute/update_custom', 'MasterData\ProductAttribute\EditController@update_custom')->name('product_attribute_update_custom');
    //         Route::post('/manufacture/master_data/product/product_attribute/delete', 'MasterData\ProductAttribute\DeleteController@delete')->name('product_attribute_delete');
    // // 

    // // Test Lab
    // //Master Data Categories
    //         Route::get('/test_lab', 'TestLabResultController@test')->name('test_lab');
    //         Route::get('/test_lab/master_data/product/categories/view', 'MasterDataLab\Categories\ViewController@index')->name('master_data_lab_category_view');
    //         Route::get('/test_lab/master_data/product/categories/view', 'MasterDataLab\Categories\ViewController@index')->name('master_data_lab_category_view');
    //         Route::get('/test_lab/master_data/product/categories/add', 'MasterDataLab\Categories\AddController@index')->name('master_data_lab_category_add');
    //         Route::get('/test_lab/master_data/product/categories/edit', 'MasterDataLab\Categories\EditController@index')->name('master_data_lab_category_edit');
    //         Route::post('/test_lab/master_data/product/categories/save', 'MasterDataLab\Categories\AddController@save')->name('master_data_lab_category_save');
    //         Route::post('/test_lab/master_data/product/categories/update', 'MasterDataLab\Categories\EditController@update')->name('master_data_lab_category_update');
    //         Route::post('/test_lab/master_data/product/categories/delete', 'MasterDataLab\Categories\DeleteController@index')->name('master_data_lab_category_delete');
    //     //Master Data Product
    //         Route::get('/test_lab/master_data/product/product/view', 'MasterDataLab\Product\ViewController@index')->name('master_data_lab_product_view');
    //     //Master Data Product Model
    //         Route::get('/test_lab/master_data/product/product_model/view', 'MasterDataLab\ProductModel\ViewController@index')->name('master_data_lab_product_model_view');
    //     //Master Data Product Version
    //         Route::get('/test_lab/master_data/product/product_version/view', 'MasterDataLab\ProductVersion\ViewController@index')->name('master_data_lab_product_version_view');
    //     //Master Data Plant
    //         Route::get('/test_lab/master_data/companies/plant/view', 'MasterDataLab\Plant\ViewController@index')->name('master_data_lab_plant_view');
    //         Route::get('/test_lab/master_data/companies/plant/add', 'MasterDataLab\Plant\AddController@index')->name('master_data_lab_plant_add');
    //         Route::get('/test_lab/master_data/companies/plant/edit', 'MasterDataLab\Plant\EditController@index')->name('master_data_lab_plant_edit');
    //         Route::post('/test_lab/master_data/companies/plant/save', 'MasterDataLab\Plant\AddController@save')->name('master_data_lab_plant_save');
    //         Route::post('/test_lab/master_data/companies/plant/update', 'MasterDataLab\Plant\EditController@update')->name('master_data_lab_plant_update');
    //         Route::post('/test_lab/master_data/companies/plant/delete', 'MasterDataLab\Plant\DeleteController@index')->name('master_data_lab_plant_delete');
    //     //Master Data Batch Store
    //         Route::get('/test_lab/master_data/companies/batch_store/view', 'MasterDataLab\BatchStore\ViewController@index')->name('master_data_lab_batch_store_view');
    //         Route::get('/test_lab/master_data/companies/batch_store/edit', 'MasterDataLab\BatchStore\EditController@index')->name('master_data_lab_batch_store_edit');
    //         Route::get('/test_lab/master_data/companies/batch_store/detail', 'MasterDataLab\BatchStore\ViewController@detail')->name('master_data_lab_batch_store_detail');
    //         Route::get('/test_lab/master_data/companies/batch_store/print', 'MasterDataLab\BatchStore\ViewController@print_lab_report')->name('master_data_lab_print_lab_report');
    //     //Product Attribute
    //         Route::get('/test_lab/master_data/product/product_attribute/view', 'MasterDataLab\ProductAttribute\EditController@index')->name('product_attribute_lab_view');
    //         Route::post('/test_lab/master_data/product/product_attribute/update_general', 'MasterDataLab\ProductAttribute\EditController@update_general')->name('product_attribute_lab_update_general');
    //         Route::post('/test_lab/master_data/product/product_attribute/update_custom', 'MasterDataLab\ProductAttribute\EditController@update_custom')->name('product_attribute_lab_update_custom');
    //         Route::post('/test_lab/master_data/product/product_attribute/delete', 'MasterDataLab\ProductAttribute\DeleteController@delete')->name('product_attribute_lab_delete');
    // 
// });
