<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use App\Models\Admin;
use App\Models\ActivitiesLog;
use App\Models\ClientMatter;
use App\Models\CostAssignmentForm;
use Auth;
use Config;

/**
 * ClientActivitiesController
 * 
 * Handles activity logs, application management, and matter-related
 * activities for clients.
 * 
 * Maps to: Activity section in client detail page
 */
class ClientActivitiesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Methods to be moved from ClientsController:
     * 
     * - activities() - Get activities log
     * - saveapplication() - Save application
     * - getapplicationlists() - Get application lists
     * - deleteactivitylog() - Delete activity log
     * - pinactivitylog() - Pin/unpin activity log
     * - notpickedcall() - Log not picked call
     * - deletecostagreement() - Delete cost agreement
     * - listAllMattersWRTSelClient() - List all matters for selected client
     */
}

