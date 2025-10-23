<?php

use App\Http\Controllers\CRM\ClientsController;
use App\Http\Controllers\CRM\ClientEoiRoiController;
use App\Http\Controllers\AdminConsole\AnzscoOccupationController;
use App\Http\Controllers\CRM\Clients\ClientNotesController;
use App\Http\Controllers\CRM\ClientPersonalDetailsController;
use App\Http\Controllers\CRM\PhoneVerificationController;
use App\Http\Controllers\CRM\EmailVerificationController;
use App\Http\Controllers\CRM\Leads\LeadController;
use App\Http\Controllers\CRM\Leads\LeadAssignmentController;
use App\Http\Controllers\CRM\Leads\LeadConversionController;
use App\Http\Controllers\CRM\Leads\LeadFollowupController;
use App\Http\Controllers\CRM\Leads\LeadAnalyticsController;
use App\Http\Controllers\CRM\DashboardController;
use App\Http\Controllers\CRM\AdminController;
use App\Http\Controllers\CRM\AssigneeController;
use App\Http\Controllers\CRM\EmailTemplateController;

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

/*--------------------------------------------------
| SECTION: Root & General Routes
|--------------------------------------------------*/

// Root route - redirect to admin login
Route::get('/', function() {
    return redirect()->route('admin.login');
});

// Cache clearing route - protected with authentication
Route::get('/clear-cache', function() {
    Artisan::call('config:clear');
    Artisan::call('view:clear');
    Artisan::call('route:clear');
    Artisan::call('route:cache');
    return response()->json([
        'success' => true,
        'message' => 'Cache cleared successfully'
    ]);
})->middleware('auth');

/*--------------------------------------------------
| SECTION: Exception Handling
|--------------------------------------------------*/
Route::get('/exception', 'ExceptionController@index')->name('exception.index');
Route::post('/exception', 'ExceptionController@index')->name('exception.store');

/*--------------------------------------------------
| SECTION: Authentication Routes
|--------------------------------------------------*/
// Auth::routes(); // Disabled - Using custom admin login at /admin and API login at /api/login instead

/*--------------------------------------------------
| SECTION: Email Manager Routes
|--------------------------------------------------*/
include_once 'emailUser.php';

/*--------------------------------------------------
| SECTION: Admin Console Routes
|--------------------------------------------------*/
require __DIR__ . '/adminconsole.php';

/*--------------------------------------------------
| SECTION: Authentication Routes
|--------------------------------------------------*/
// Login routes stay at /admin for clarity
Route::prefix('admin')->group(function() {
    Route::get('/', 'Auth\AdminLoginController@showLoginForm')->name('admin.login');
    Route::get('/login', 'Auth\AdminLoginController@showLoginForm');
    Route::post('/login', 'Auth\AdminLoginController@login')->name('admin.login.post');
    Route::post('/logout', 'Auth\AdminLoginController@logout')->name('admin.logout');
});

/*--------------------------------------------------
| SECTION: CRM Application Routes (Protected)
|--------------------------------------------------*/
// Main CRM routes at root level with auth:admin middleware
Route::middleware(['auth:admin'])->group(function() {

	/*---------- Dashboard Routes ----------*/
    Route::get('/dashboard', 'CRM\DashboardController@index')->name('dashboard');
    Route::post('/dashboard/column-preferences', 'CRM\DashboardController@saveColumnPreferences')->name('dashboard.column-preferences');
    Route::post('/dashboard/update-stage', 'CRM\DashboardController@updateStage')->name('dashboard.update-stage');
    Route::post('/dashboard/extend-deadline', 'CRM\DashboardController@extendDeadlineDate')->name('dashboard.extend-deadline');
    Route::post('/dashboard/update-task-completed', 'CRM\DashboardController@updateTaskCompleted')->name('dashboard.update-task-completed');
    Route::get('/dashboard/fetch-notifications', 'CRM\AdminController@fetchnotification')->name('dashboard.fetch-notifications');
    Route::get('/dashboard/fetch-office-visit-notifications', 'CRM\AdminController@fetchOfficeVisitNotifications')->name('dashboard.fetch-office-visit-notifications');
    Route::post('/dashboard/mark-notification-seen', 'CRM\AdminController@markNotificationSeen')->name('dashboard.mark-notification-seen');
    Route::get('/dashboard/fetch-visa-expiry-messages', 'CRM\AdminController@fetchvisaexpirymessages')->name('dashboard.fetch-visa-expiry-messages');
    Route::get('/dashboard/fetch-in-person-waiting-count', 'CRM\AdminController@fetchInPersonWaitingCount')->name('dashboard.fetch-in-person-waiting-count');
    Route::get('/dashboard/fetch-total-activity-count', 'CRM\AdminController@fetchTotalActivityCount')->name('dashboard.fetch-total-activity-count');
    Route::post('/dashboard/check-checkin-status', 'CRM\DashboardController@checkCheckinStatus')->name('dashboard.check-checkin-status');
    Route::post('/dashboard/update-checkin-status', 'CRM\DashboardController@updateCheckinStatus')->name('dashboard.update-checkin-status');

	/*---------- General Admin Routes ----------*/
    Route::get('/my_profile', 'CRM\AdminController@myProfile')->name('my_profile');
    Route::post('/my_profile', 'CRM\AdminController@myProfile')->name('my_profile.update');
    Route::get('/change_password', 'CRM\AdminController@change_password')->name('change_password');
    Route::post('/change_password', 'CRM\AdminController@change_password')->name('change_password.update');
    Route::post('/update_action', 'CRM\AdminController@updateAction');
    Route::post('/approved_action', 'CRM\AdminController@approveAction');
    Route::post('/process_action', 'CRM\AdminController@processAction');
    Route::post('/archive_action', 'CRM\AdminController@archiveAction');
    Route::post('/declined_action', 'CRM\AdminController@declinedAction');
    Route::post('/delete_action', 'CRM\AdminController@deleteAction');
    Route::post('/move_action', 'CRM\AdminController@moveAction');

    Route::get('/appointments-education', 'CRM\AdminController@appointmentsEducation')->name('appointments-education');
    Route::get('/appointments-jrp', 'CRM\AdminController@appointmentsJrp')->name('appointments-jrp');
    Route::get('/appointments-tourist', 'CRM\AdminController@appointmentsTourist')->name('appointments-tourist');
    Route::get('/appointments-others', 'CRM\AdminController@appointmentsOthers')->name('appointments-others');

    Route::post('/add_ckeditior_image', 'CRM\AdminController@addCkeditiorImage')->name('add_ckeditior_image');
    Route::post('/get_chapters', 'CRM\AdminController@getChapters')->name('get_chapters');
    Route::post('/get_states', 'CRM\AdminController@getStates');
    Route::get('/settings/taxes/returnsetting', 'CRM\AdminController@returnsetting')->name('returnsetting');
    Route::post('/settings/taxes/savereturnsetting', 'CRM\AdminController@returnsetting')->name('savereturnsetting');
    Route::get('/getsubcategories', 'CRM\AdminController@getsubcategories');
    Route::get('/getassigneeajax', 'CRM\AdminController@getassigneeajax');
    Route::get('/getpartnerajax', 'CRM\AdminController@getpartnerajax');
    Route::get('/checkclientexist', 'CRM\AdminController@checkclientexist');

	/*---------- CRM & User Management Routes ----------*/
    // All user management routes moved to routes/adminconsole.php
    // - Staff management: Use adminconsole.system.users routes
    // - User types/roles: Use adminconsole.system.roles routes

    /*---------- Leads Management (Modern Laravel Syntax) ----------*/
    // Lead CRUD operations
    Route::prefix('leads')->name('leads.')->group(function () {
        // List & Detail
        Route::get('/', [LeadController::class, 'index'])->name('index');
        Route::get('/detail/{id}', [LeadController::class, 'detail'])->name('detail');
        Route::get('/history/{id}', [LeadController::class, 'history'])->name('history');
        
        // Create
        Route::get('/create', [LeadController::class, 'create'])->name('create');
        Route::post('/store', [LeadController::class, 'store'])->name('store');
        
        // Edit & Update (RESTful pattern)
        Route::get('/{id}/edit', [LeadController::class, 'edit'])->name('edit');
        Route::put('/{id}', [LeadController::class, 'update'])->name('update');
        Route::patch('/{id}', [LeadController::class, 'update'])->name('patch');
        
        // Assignment operations
        Route::post('/assign', [LeadAssignmentController::class, 'assign'])->name('assign');
        Route::post('/bulk-assign', [LeadAssignmentController::class, 'bulkAssign'])->name('bulk_assign');
        Route::get('/assignable-users', [LeadAssignmentController::class, 'getAssignableUsers'])->name('assignable_users');
        
        // Conversion operations
        Route::get('/convert', [LeadConversionController::class, 'convertToClient'])->name('convert');
        Route::post('/convert-single', [LeadConversionController::class, 'convertSingleLead'])->name('convert_single');
        Route::post('/bulk-convert', [LeadConversionController::class, 'bulkConvertToClient'])->name('bulk_convert');
        Route::get('/conversion-stats', [LeadConversionController::class, 'getConversionStats'])->name('conversion_stats');
        
        // Follow-up System
        Route::prefix('followups')->name('followups.')->group(function () {
            Route::get('/', [LeadFollowupController::class, 'index'])->name('index');
            Route::get('/dashboard', [LeadFollowupController::class, 'myFollowups'])->name('dashboard');
            Route::post('/', [LeadFollowupController::class, 'store'])->name('store');
            Route::post('/{id}/complete', [LeadFollowupController::class, 'complete'])->name('complete');
            Route::post('/{id}/reschedule', [LeadFollowupController::class, 'reschedule'])->name('reschedule');
            Route::post('/{id}/cancel', [LeadFollowupController::class, 'cancel'])->name('cancel');
        });
        Route::get('/{leadId}/followups', [LeadFollowupController::class, 'getLeadFollowups'])->name('followups.get');
        
        // Analytics (Admin/Team Lead only)
        Route::prefix('analytics')->name('analytics.')->group(function () {
            Route::get('/', [LeadAnalyticsController::class, 'index'])->name('index');
            Route::get('/trends', [LeadAnalyticsController::class, 'getTrends'])->name('trends');
            Route::get('/export', [LeadAnalyticsController::class, 'export'])->name('export');
            Route::post('/compare-agents', [LeadAnalyticsController::class, 'compareAgents'])->name('compare');
        });
        
        // Legacy routes (deprecated functionality)
        Route::get('/notes/delete/{id}', [LeadController::class, 'leaddeleteNotes'])->name('notes.delete');
        Route::get('/pin/{id}', [LeadController::class, 'leadPin'])->name('pin');
    });
    
    // Global route (outside leads prefix) - kept for backward compatibility
    Route::get('/get-notedetail', [LeadController::class, 'getnotedetail'])->name('get-notedetail');

	/*---------- Email Templates ----------*/
    Route::get('/email_templates', 'CRM\EmailTemplateController@index')->name('email.index');
    Route::get('/email_templates/create', 'CRM\EmailTemplateController@create')->name('email.create');
    Route::post('/email_templates/store', 'CRM\EmailTemplateController@store')->name('email.store');
    Route::get('/edit_email_template/{id}', 'CRM\EmailTemplateController@editEmailTemplate')->name('edit_email_template');
    Route::post('/edit_email_template', 'CRM\EmailTemplateController@editEmailTemplate')->name('edit_email_template.update');

	/*---------- API Settings ----------*/
    Route::get('/api-key', 'CRM\AdminController@editapi')->name('api');
    Route::post('/api-key', 'CRM\AdminController@editapi')->name('api.update');

	/*--------------------------------------------------
	| SECTION: Client Management Routes
	|--------------------------------------------------*/
	// All client routes moved to routes/clients.php
	// Includes: CRUD, documents, verification, invoices, EOI/ROI, notes, agreements
	require __DIR__ . '/clients.php';

	/*--------------------------------------------------
	| SECTION: Applications & Office Visits Routes
	|--------------------------------------------------*/
	// All application, office visit, and appointment routes moved to routes/applications.php
	require __DIR__ . '/applications.php';

	/*---------- Audit Logs ----------*/
	Route::get('/audit-logs', 'CRM\AuditLogController@index')->name('auditlogs.index');

	/*---------- Notifications ----------*/
	Route::get('/fetch-notification', 'CRM\AdminController@fetchnotification');
	Route::get('/fetch-messages', 'CRM\AdminController@fetchmessages');
	Route::get('/fetch-office-visit-notifications', 'CRM\AdminController@fetchOfficeVisitNotifications');
	Route::post('/mark-notification-seen', 'CRM\AdminController@markNotificationSeen');
	Route::get('/check-checkin-status', 'CRM\AdminController@checkCheckinStatus');
	Route::post('/update-checkin-status', 'CRM\AdminController@updateCheckinStatus');
	Route::get('/all-notifications', 'CRM\AdminController@allnotification');
	Route::get('/fetch-InPersonWaitingCount', 'CRM\AdminController@fetchInPersonWaitingCount');
	Route::get('/fetch-TotalActivityCount', 'CRM\AdminController@fetchTotalActivityCount');

	/*---------- Assignee Module ----------*/
	Route::resource('/assignee', AssigneeController::class);
        Route::get('/assignee-completed', 'CRM\AssigneeController@completed'); //completed list only

        Route::post('/update-task-completed', 'CRM\AssigneeController@updatetaskcompleted'); //update task to be completed
        Route::post('/update-task-not-completed', 'CRM\AssigneeController@updatetasknotcompleted'); //update task to be not completed

        Route::get('/assigned_by_me', 'CRM\AssigneeController@assigned_by_me')->name('assignee.assigned_by_me'); //assigned by me
        Route::get('/assigned_to_me', 'CRM\AssigneeController@assigned_to_me')->name('assignee.assigned_to_me'); //assigned to me

        Route::delete('/destroy_by_me/{note_id}', 'CRM\AssigneeController@destroy_by_me')->name('assignee.destroy_by_me'); //assigned by me
        Route::delete('/destroy_to_me/{note_id}', 'CRM\AssigneeController@destroy_to_me')->name('assignee.destroy_to_me'); //assigned to me
        Route::get('/action_completed', 'CRM\AssigneeController@action_completed')->name('assignee.action_completed'); //action completed


        Route::delete('/destroy_activity/{note_id}', 'CRM\AssigneeController@destroy_activity')->name('assignee.destroy_activity'); //delete activity
        Route::delete('/destroy_complete_activity/{note_id}', 'CRM\AssigneeController@destroy_complete_activity')->name('assignee.destroy_complete_activity'); //delete completed activity

	/*---------- Task Management ----------*/
	// Task routes for email and contact uniqueness
        Route::post('/is_email_unique', 'CRM\Leads\LeadController@is_email_unique');
        Route::post('/is_contactno_unique', 'CRM\Leads\LeadController@is_contactno_unique');

	// Activity management
        Route::post('/extenddeadlinedate', 'CRM\AdminController@extenddeadlinedate');
        Route::post('/update-stage', 'CRM\AdminController@updateStage');

	// Get assigne list
	Route::post('/get_assignee_list', 'CRM\AssigneeController@get_assignee_list');

	// Update task
        Route::post('/update-task', 'CRM\AssigneeController@updateTask');
        Route::get('/action/counts','CRM\AssigneeController@getActionCounts' )->name('action.counts');

	// For datatable - Action list routes
	Route::get('/action', 'CRM\AssigneeController@action')->name('assignee.action');
	Route::get('/action/list','CRM\AssigneeController@getAction')->name('action.list');

	/*---------- End of Admin Routes ----------*/


	});

/*--------------------------------------------------
| SECTION: Document Signature Routes (Admin & Public)
|--------------------------------------------------*/
// Admin document management and public client signing
// Loaded outside admin group to allow proper prefix handling
require __DIR__ . '/documents.php';

/*--------------------------------------------------
| SECTION: Public Email Verification
|--------------------------------------------------*/
// Public email verification route loaded from clients.php

