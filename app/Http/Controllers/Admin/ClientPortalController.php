<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use App\Models\Admin;
use App\Models\ActivitiesLog;
use Auth;
use Config;

/**
 * ClientPortalController
 * 
 * Handles client portal user management including creating portal users,
 * activating/deactivating access, and sending portal credentials.
 * 
 * Maps to: resources/views/Admin/clients/tabs/client_portal.blade.php
 */
class ClientPortalController extends Controller
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
     * - getClientPortalUsers() - Get list of client portal users
     * - createClientPortalUser() - Create new client portal user
     * - deleteClientPortalUser() - Delete client portal user
     * - toggleClientPortalStatus() - Toggle client portal active status
     * - sendClientPortalActivationEmail() (private) - Send activation email
     * - sendClientPortalDeactivationEmail() (private) - Send deactivation email
     */
}

