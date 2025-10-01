<?php
use App\Http\Controllers\EmailUser\ProfileController;
use App\Http\Controllers\EmailUser\AuthController; // For zoho 
use App\Http\Controllers\EmailUser\EmailController;
use App\Http\Controllers\EmailUser\AttachmentController;
use App\Http\Controllers\EmailUser\LabelController;
use App\Http\Controllers\EmailUser\EmailAccountController;
use App\Http\Controllers\EmailUser\SignatureController;
use App\Http\Controllers\Auth\AdminEmailController;
use Illuminate\Support\Facades\Route;   

Route::prefix('email_users')->group(function() {
    //Email manager authentication routes
    Route::get('/', [AdminEmailController::class, 'showLoginForm'])->name('email_users.login');
    Route::get('/login', [AdminEmailController::class, 'showLoginForm'])->name('email_users.login');
    Route::post('/login', [AdminEmailController::class, 'login'])->name('email_users.login.post');
    Route::post('/logout', [AdminEmailController::class, 'logout'])->name('email_users.logout');
    //Route::get('/logout', 'Auth\AdminEmailController@logout')->name('email_users.logout.get');

    
    /*
    Route::get('/dashboard', 'EmailUser\DashboardController@dashboard')->name('email_users.dashboard');
    Route::get('/loadinbox/{email_user_id}', 'EmailUser\EmailListController@loadinbox')->name('email_users.loadinbox');
    Route::get('/loadsent/{email_user_id}', 'EmailUser\EmailListController@loadsent')->name('email_users.loadsent');
    Route::get('/inbox/{email_user_id}', 'EmailUser\EmailListController@inbox')->name('email_users.inbox');
    Route::get('/sent/{email_user_id}', 'EmailUser\EmailListController@sent')->name('email_users.sent');
    Route::post('/assiginboxemail', 'EmailUser\EmailListController@assiginboxemail')->name('email_users.assiginboxemail');
    Route::post('/assigsentemail', 'EmailUser\EmailListController@assigsentemail')->name('email_users.assigsentemail');

    //Fetch selected client all matters at assign email to user popup
    Route::post('/listAllMattersWRTSelClient', 'EmailUser\EmailListController@listAllMattersWRTSelClient')->name('email_users.listAllMattersWRTSelClient');
    */


    Route::get('/dashboard', function () {
        $emailAccounts = \App\Models\EmailAccount::where('user_id', auth('email_users')->id())
            ->get()
            ->map(function ($account) {
                return [
                    'id' => $account->id,
                    'label' => ucfirst($account->provider) . ' - ' . $account->email
                ];
            });
        
        return view('EmailUser.dashboard', compact('emailAccounts'));
    })->middleware('auth:email_users')->name('email_users.dashboard');


    Route::middleware('auth:email_users')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('email_users.profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('email_users.profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('email_users.profile.destroy');
    
        // Email account management routes
        Route::resource('accounts', EmailAccountController::class)->names([
            'index' => 'email_users.accounts.index',
            'create' => 'email_users.accounts.create',
            'store' => 'email_users.accounts.store',
            'show' => 'email_users.accounts.show',
            'edit' => 'email_users.accounts.edit',
            'update' => 'email_users.accounts.update',
            'destroy' => 'email_users.accounts.destroy'
        ]);
        Route::post('/accounts/{account}/test-connection', [EmailAccountController::class, 'testConnection'])->name('email_users.accounts.test-connection');
        Route::post('/accounts/{account}/test-authentication', [EmailAccountController::class, 'testAuthentication'])->name('email_users.accounts.test-authentication');
    
        Route::post('/emails/send', [EmailController::class, 'send'])->name('email_users.emails.send');
        Route::post('/emails/save-draft', [EmailController::class, 'saveDraft'])->name('email_users.emails.save-draft');
        Route::get('/emails/drafts', [EmailController::class, 'drafts'])->name('email_users.emails.drafts');
        Route::get('/emails/draft/{id}', [EmailController::class, 'getDraft'])->name('email_users.emails.draft.get');
        Route::delete('/emails/draft/{id}', [EmailController::class, 'deleteDraft'])->name('email_users.emails.draft.delete');
        Route::get('/emails/reply/{id}', [EmailController::class, 'getReplyData'])->name('email_users.emails.reply.get');
        Route::get('/emails/content/{id}', [EmailController::class, 'getEmailContent'])->name('email_users.emails.content.get');
        Route::get('/emails/compose', [EmailController::class, 'compose'])->name('email_users.emails.compose');
        Route::get('/emails/sync/{accountId}', [EmailController::class, 'sync'])->name('email_users.emails.sync.get');
        Route::post('/emails/sync/{accountId}', [EmailController::class, 'sync'])->name('email_users.emails.sync.post');
        Route::post('/emails/bulk-action', [EmailController::class, 'bulkAction'])->name('email_users.emails.bulk-action');
        Route::get('/emails/debug-bulk-action', [EmailController::class, 'debugBulkAction'])->name('email_users.emails.debug-bulk-action');
        Route::get('/clients', [EmailController::class, 'getClients'])->name('email_users.clients');
        Route::get('/client-matters/{clientId}', [EmailController::class, 'getClientMatters'])->name('email_users.client-matters');
        Route::post('/emails/allocate', [EmailController::class, 'allocateEmails'])->name('email_users.emails.allocate');
        Route::post('/auth/zoho/add', [AuthController::class, 'addZohoAccount'])->name('email_users.auth.zoho.add');
    
        // Attachment download and view
        Route::get('/attachments/{id}/download', [AttachmentController::class, 'download'])->name('email_users.attachments.download');
        Route::get('/attachments/{id}/view', [AttachmentController::class, 'view'])->name('email_users.attachments.view');
    
        // Labels minimal API for UI
        Route::get('/labels', [LabelController::class, 'index'])->name('email_users.labels.index');
        Route::post('/labels/apply', [LabelController::class, 'apply'])->name('email_users.labels.apply');
        Route::post('/labels/remove', [LabelController::class, 'remove'])->name('email_users.labels.remove');
    
        // Email signatures management
        Route::resource('signatures', SignatureController::class)->names([
            'index' => 'email_users.signatures.index',
            'create' => 'email_users.signatures.create',
            'store' => 'email_users.signatures.store',
            'show' => 'email_users.signatures.show',
            'edit' => 'email_users.signatures.edit',
            'update' => 'email_users.signatures.update',
            'destroy' => 'email_users.signatures.destroy'
        ]);
        Route::post('/signatures/{signature}/toggle', [SignatureController::class, 'toggle'])->name('email_users.signatures.toggle');
        Route::post('/signatures/{signature}/set-default', [SignatureController::class, 'setDefault'])->name('email_users.signatures.set-default');
        Route::get('/signatures/account/{account_id}', [SignatureController::class, 'getForAccount'])->name('email_users.signatures.get-for-account');
        Route::post('/signatures/{signature}/upload-image', [SignatureController::class, 'uploadImage'])->name('email_users.signatures.upload-image');
        Route::delete('/signatures/{signature}/remove-image', [SignatureController::class, 'removeImage'])->name('email_users.signatures.remove-image');
        Route::get('/signatures/{signature}/preview', [SignatureController::class, 'preview'])->name('email_users.signatures.preview');
        Route::get('/signatures/template/preview', [SignatureController::class, 'getTemplatePreview'])->name('email_users.signatures.template-preview');
    });


});

//require __DIR__.'/auth.php';

// OAuth routes
//Route::get('/auth/{provider}', [AuthController::class, 'redirect'])->name('oauth.redirect');
//Route::get('/auth/{provider}/callback', [AuthController::class, 'callback'])->name('oauth.callback');
?>









