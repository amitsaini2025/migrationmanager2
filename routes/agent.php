<?php Route::prefix('agent')->group(function() {
        Route::get('/', [App\Http\Controllers\Auth\AgentLoginController::class, 'showLoginForm'])->name('agent.login');
        Route::get('/login', [App\Http\Controllers\Auth\AgentLoginController::class, 'showLoginForm'])->name('agent.login');
        
        Route::post('/login', [App\Http\Controllers\Auth\AgentLoginController::class, 'login'])->name('agent.login');
        
        Route::post('/logout', [App\Http\Controllers\Auth\AgentLoginController::class, 'logout'])->name('agent.logout');
        
        Route::get('/dashboard', [App\Http\Controllers\Agent\DashboardController::class, 'dashboard'])->name('agent.dashboard');
        
        
        Route::get('/clients', [App\Http\Controllers\Agent\ClientsController::class, 'index'])->name('agent.clients.index');
        Route::get('/clients/create', [App\Http\Controllers\Agent\ClientsController::class, 'create'])->name('agent.clients.create');
        Route::post('/clients/store', [App\Http\Controllers\Agent\ClientsController::class, 'store'])->name('agent.clients.store');
        Route::get('/clients/edit/{id}', [App\Http\Controllers\Agent\ClientsController::class, 'edit'])->name('agent.clients.edit');
        Route::post('/clients/edit', [App\Http\Controllers\Agent\ClientsController::class, 'edit'])->name('agent.clients.edit');
        
        Route::get('/clients/detail/{id}', [App\Http\Controllers\Agent\ClientsController::class, 'detail'])->name('agent.clients.detail');	
        Route::get('/clients/get-recipients', [App\Http\Controllers\Agent\ClientsController::class, 'getrecipients'])->name('agent.clients.getrecipients');
        Route::get('/clients/get-allclients', [App\Http\Controllers\Agent\ClientsController::class, 'getallclients'])->name('agent.clients.getallclients');
        // Route::get('/get-templates', 'Agent\AdminController@gettemplates')->name('agent.clients.gettemplates');
        // Route::post('/sendmail', 'Agent\AdminController@sendmail')->name('agent.clients.sendmail');
        Route::post('/create-note', [App\Http\Controllers\Agent\ClientsController::class, 'createnote'])->name('agent.clients.createnote');
        Route::get('/getnotedetail', [App\Http\Controllers\Agent\ClientsController::class, 'getnotedetail'])->name('agent.clients.getnotedetail');
        Route::get('/deletenote', [App\Http\Controllers\Agent\ClientsController::class, 'deletenote'])->name('agent.clients.deletenote');
        //prospects Start  
        Route::get('/prospects', [App\Http\Controllers\Agent\ClientsController::class, 'prospects'])->name('agent.clients.prospects');
        Route::get('/viewnotedetail', [App\Http\Controllers\Agent\ClientsController::class, 'viewnotedetail']);
        Route::get('/viewapplicationnote', [App\Http\Controllers\Agent\ClientsController::class, 'viewapplicationnote']);
        
        //archived Start  
        Route::get('/archived', [App\Http\Controllers\Agent\ClientsController::class, 'archived'])->name('agent.clients.archived');
        Route::get('/change-client-status', [App\Http\Controllers\Agent\ClientsController::class, 'updateclientstatus'])->name('agent.clients.updateclientstatus');
        Route::get('/get-activities', [App\Http\Controllers\Agent\ClientsController::class, 'activities'])->name('agent.clients.activities');
        Route::get('/get-application-lists', [App\Http\Controllers\Agent\ClientsController::class, 'getapplicationlists'])->name('agent.clients.getapplicationlists');
        Route::post('/saveapplication', [App\Http\Controllers\Agent\ClientsController::class, 'saveapplication'])->name('agent.clients.saveapplication');
        Route::get('/get-notes', [App\Http\Controllers\Agent\ClientsController::class, 'getnotes'])->name('agent.clients.getnotes');
        Route::get('/convertapplication', [App\Http\Controllers\Agent\ClientsController::class, 'convertapplication'])->name('agent.clients.convertapplication');
        Route::get('/deleteservices', [App\Http\Controllers\Agent\ClientsController::class, 'deleteservices'])->name('agent.clients.deleteservices');
        Route::post('/upload-document', [App\Http\Controllers\Agent\ClientsController::class, 'uploaddocument'])->name('agent.clients.uploaddocument');
        Route::get('/deletedocs', [App\Http\Controllers\Agent\ClientsController::class, 'deletedocs'])->name('agent.clients.deletedocs');
        Route::post('/renamedoc', [App\Http\Controllers\Agent\ClientsController::class, 'renamedoc'])->name('agent.clients.renamedoc');
        
        Route::post('/savetoapplication', [App\Http\Controllers\Agent\ClientsController::class, 'savetoapplication']);
        
        Route::post('/interested-service', [App\Http\Controllers\Agent\ClientsController::class, 'interestedService']); 	 
        Route::post('/edit-interested-service', [App\Http\Controllers\Agent\ClientsController::class, 'editinterestedService']); 
        
        Route::get('/showproductfeeserv', [App\Http\Controllers\Agent\ClientsController::class, 'showproductfeeserv']);
        Route::post('/servicesavefee', [App\Http\Controllers\Agent\ClientsController::class, 'servicesavefee']);
        
        Route::get('/pinnote', [App\Http\Controllers\Agent\ClientsController::class, 'pinnote']); 
        Route::get('/getpartner', [App\Http\Controllers\Agent\DashboardController::class, 'getpartner']);
        Route::get('getproduct', [App\Http\Controllers\Agent\DashboardController::class, 'getproduct'])->name('agent.quotations.getproduct');
        Route::get('getbranch', [App\Http\Controllers\Agent\DashboardController::class, 'getbranch'])->name('agent.quotations.getbranch');
        Route::get('/get-services', [App\Http\Controllers\Agent\ClientsController::class, 'getServices']); 
        Route::get('/getintrestedservice', [App\Http\Controllers\Agent\ClientsController::class, 'getintrestedservice']);
        Route::get('/getintrestedserviceedit', [App\Http\Controllers\Agent\ClientsController::class, 'getintrestedserviceedit']); 
        
        
        Route::get('/getapplicationdetail', [App\Http\Controllers\Agent\ApplicationsController::class, 'getapplicationdetail']);
        Route::get('/application/export/pdf/{id}', [App\Http\Controllers\Agent\ApplicationsController::class, 'exportapplicationpdf']); 
        Route::get('/getapplicationnotes', [App\Http\Controllers\Agent\ApplicationsController::class, 'getapplicationnotes']);
});

?>