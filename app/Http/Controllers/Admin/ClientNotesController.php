<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use App\Models\Admin;
use App\Models\Note;
use App\Models\ActivitiesLog;
use App\Models\OnlineForm;
use Auth;
use Config;
use Carbon\Carbon;

/**
 * ClientNotesController
 * 
 * Handles all note-related operations including creating, updating,
 * viewing, deleting, and pinning notes.
 * 
 * Maps to: resources/views/Admin/clients/tabs/notes.blade.php
 */
class ClientNotesController extends Controller
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
     * - createnote() - Create new note
     * - updateNoteDatetime() - Update note date/time
     * - getnotedetail() - Get note details for editing
     * - viewnotedetail() - View note details
     * - viewapplicationnote() - View application note
     * - getnotes() - Get list of notes
     * - deletenote() - Delete a note
     * - pinnote() - Pin/unpin a note
     * - saveprevvisa() - Save previous visa information
     * - saveonlineform() - Save online form data
     */
}

