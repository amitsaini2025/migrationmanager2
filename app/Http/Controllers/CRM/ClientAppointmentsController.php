<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use App\Models\Admin;
use App\Models\ActivitiesLog;
use App\Models\CheckinLog;
use App\Models\Note;
use Auth;
use Config;
use DateTime;
use DateTimeZone;
use Carbon\Carbon;

/**
 * ClientAppointmentsController
 * 
 * Handles appointments, follow-ups, scheduling, and session management.
 * 
 * Maps to: resources/views/Admin/clients/tabs/appointments.blade.php
 */
class ClientAppointmentsController extends Controller
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
     * - addAppointmentBook() - Add appointment to calendar
     * - addAppointment() - Create new appointment
     * - editappointment() - Edit existing appointment
     * - updateappointmentstatus() - Update appointment status
     * - updatefollowupschedule() - Update follow-up schedule
     * - getAppointments() - Get list of appointments
     * - getAppointmentdetail() - Get appointment details
     * - deleteappointment() - Delete appointment
     * - followupstore() - Store follow-up
     * - reassignfollowupstore() - Reassign follow-up
     * - updatefollowup() - Update follow-up
     * - personalfollowup() - Personal follow-up
     * - retagfollowup() - Retag follow-up
     * - removetag() - Remove tag from appointment
     * - updatesessioncompleted() - Update session as completed
     */
}

