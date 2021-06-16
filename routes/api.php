<?php

// URL::forceScheme('https');
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::post('transaction/upload_file', 'Api\V2\Transaction\ReceiveImageController@index');
//Chat
Route::get('V1/chat/get', 'Api\V1\Master\ChatApiController@get_chat');
Route::post('V1/chat/add', 'Api\V1\Master\ChatApiController@send_chat');

// // Transaction Old QR
// Route::get('V2/transaction/old_qr/get', 'Api\V2\Transaction\OldQRTransactionController@get');

// // Transaction New QR
// Route::post('V2/transaction/new_qr/post', 'Api\V2\Transaction\NewQRTransactionController@post');

// // Pairing QR zeta or Alpha
// Route::post('V2/transaction/qr_pairing_production/save', 'Api\V2\Transaction\PairQrProduction@index');
// Route::post('V2/transaction/qr_pairing_packaging/save', 'Api\V2\Transaction\PairQrPackaging@index');
// Route::post('V2/transaction/report_batch', 'Api\V2\Transaction\ReportBatch@index');

// // Pairing test lab
// Route::post('V2_Test_Lab/transaction/qr_pairing_production/save', 'Api\V2_Test_Lab\PairQrProduction@index');
// Route::post('V2_Test_Lab/transaction/qr_pairing_packaging/save', 'Api\V2_Test_Lab\PairQrPackaging@index');
// Route::post('V2_Test_Lab/transaction/pairing_bridge/save', 'Api\V2_Test_Lab\PairBridge@index');

// // Status Batch and Product Data
// Route::post('V2/transaction/qr_pairing/get', 'Api\V2\Transaction\QRPairing@get');

// // Read Transaction Order
// Route::get('V2/transaction/transaction_order/get', 'Api\V2\Transaction\ReadTransactionOrder@get');

// // Staff Authentication
// Route::get('V2/master/staff/check_token', 'Api\V2\Master\StaffAuthenticationController@check_token');
// Route::get('V2/master/staff/logout', 'Api\V2\Master\StaffAuthenticationController@logout');
// Route::get('V2/master/staff/get', 'Api\V2\Master\StaffAuthenticationController@login');
// Route::post('V2/master/staff/send_otp', 'Api\V2\Master\StaffAuthenticationController@send_otp');
// Route::post('V2/master/staff/resend_otp', 'Api\V2\Master\StaffAuthenticationController@resend_otp');
// Route::post('V2/master/staff/check_activity', 'Api\V2\Master\StaffAuthenticationController@check_activity');
// //auth customer
// Route::post('V2/master/customer/send_otp_code', 'Api\V2\Master\CustomerAuthenticationController@send_otp_code');
// Route::post('V2/master/customer/send_otp_email', 'Api\V2\Master\CustomerAuthenticationController@send_otp_email');
// Route::post('V2/master/customer/account_change_otp_email', 'Api\V2\Master\CustomerAuthenticationController@send_otp_account_change_email');

// // List Plant
// Route::get('V2/master/plant', 'Api\V2\Master\PlantController@index');

// // Paired Batch QR & Paired Employee QR
// Route::get('V2/master/batch_information', 'Api\V2\Master\CheckBatchInformation@index');

// // Version Priority
// Route::get('V2/version/update_priority/get', 'Api\V2\Version\UpdatePriority@get');
// Route::get('V2/version/legal_version/get', 'Api\V2\Version\LegalVersion@index');

// // Auth Scan Qr Zeta
// Route::get('V2/master/check_qr', 'Api\V2\Master\CheckQrController@index');
// Route::get('V2_Test_Lab/master/check_qr', 'Api\V2_Test_Lab\CheckQrController@index');

// // Partial QR Scan
// Route::post('V2/master/partial_qr', 'Api\V2\Master\PartialQrController@index');

// // Reject or Report
// Route::post('V2/transaction/qr/reject', 'Api\V2\Transaction\QRPackagingReport@report');
// Route::post('V2/transaction/qr/reject_production', 'Api\V2\Transaction\QRProductionReport@report');
// Route::post('V2/transaction/qr/customer_report', 'Api\V2\Transaction\QRCustomerReport@report');


// // Checking Version
// Route::get('V2/transaction/new_qr/get', 'Api\V2\Transaction\NewQRTransactionController@get');
// // Forgot Account Name
// Route::get('V2/transaction/forgot_account_name/send_email', 'Api\V2\Transaction\ForgotAccountNameController@send_email');
// // Forgot Password
// Route::get('V2/transaction/forgot_password/send_email', 'Api\V2\Transaction\ForgotPasswordController@send_email');

// // Lab Result
// Route::post('V2_Test_Lab/lab_result', 'Api\V2_Test_Lab\LabResultController@index');

// // Scan History Report
// Route::get('V2/report/scan_history_header', 'Api\V2\Report\ScanHistoryHeader@index');
// Route::get('V2/report/scan_history_detail', 'Api\V2\Report\ScanHistoryDetail@index');