<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Sms\SmsController;
use App\Http\Controllers\Admin\Sms\SmsSendController;
use App\Http\Controllers\Admin\Sms\SmsTemplateController;
use App\Http\Controllers\Admin\Sms\SmsWebhookController;

/*
|--------------------------------------------------------------------------
| SMS Routes
|--------------------------------------------------------------------------
|
| All SMS-related routes are defined here
| Organized by feature area for better maintainability
|
*/

// ============================================================================
// ADMIN SMS ROUTES (Authenticated)
// ============================================================================
Route::prefix('admin/sms')->middleware(['auth:admin'])->name('admin.sms.')->group(function () {
    
    // SMS Dashboard & History
    Route::get('/dashboard', [SmsController::class, 'dashboard'])->name('dashboard');
    Route::get('/history', [SmsController::class, 'history'])->name('history');
    Route::get('/history/{id}', [SmsController::class, 'show'])->name('history.show');
    
    // SMS Statistics & Status (API endpoints)
    Route::get('/statistics', [SmsController::class, 'statistics'])->name('statistics');
    Route::get('/status/{smsLogId}', [SmsController::class, 'checkStatus'])->name('status.check');
    
    // Manual SMS Sending
    Route::get('/send', [SmsSendController::class, 'create'])->name('send.create');
    Route::post('/send', [SmsSendController::class, 'send'])->name('send');
    Route::post('/send/template', [SmsSendController::class, 'sendFromTemplate'])->name('send.template');
    Route::post('/send/bulk', [SmsSendController::class, 'sendBulk'])->name('send.bulk');
    
    // SMS Templates
    Route::resource('templates', SmsTemplateController::class);
    Route::get('/templates-active', [SmsTemplateController::class, 'active'])->name('templates.active');
});

// ============================================================================
// WEBHOOK ROUTES (Public - No Authentication)
// ============================================================================
Route::prefix('webhooks/sms')->name('webhooks.sms.')->group(function () {
    
    // Twilio Webhooks
    Route::post('/twilio/status', [SmsWebhookController::class, 'twilioStatus'])->name('twilio.status');
    Route::post('/twilio/incoming', [SmsWebhookController::class, 'twilioIncoming'])->name('twilio.incoming');
    
    // Cellcast Webhooks
    Route::post('/cellcast/status', [SmsWebhookController::class, 'cellcastStatus'])->name('cellcast.status');
    Route::post('/cellcast/incoming', [SmsWebhookController::class, 'cellcastIncoming'])->name('cellcast.incoming');
});

