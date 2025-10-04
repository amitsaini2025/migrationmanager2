<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use App\Models\Admin;
use App\Models\ClientAddress;
use App\Models\ClientContact;
use App\Models\ClientEmail;
use App\Models\ClientQualification;
use App\Models\ClientExperience;
use App\Models\ClientTestScore;
use App\Models\ClientVisaCountry;
use App\Models\ClientOccupation;
use App\Models\ClientSpouseDetail;
use App\Models\ClientPoint;
use App\Models\ClientPassportInformation;
use App\Models\ClientTravelInformation;
use App\Models\ClientCharacter;
use App\Models\ClientRelationship;
use Auth;
use Config;

/**
 * ClientPersonalDetailsController
 * 
 * Handles personal information, family details, qualifications, occupations,
 * test scores, and points calculation for clients.
 * 
 * Maps to: resources/views/Admin/clients/tabs/personal_details.blade.php
 */
class ClientPersonalDetailsController extends Controller
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
     * - clientdetailsinfo() - Get client details
     * - getVisaTypes() - Get list of visa types
     * - getCountries() - Get list of countries
     * - saveRelationship() - Save family relationships
     * - updateAddress() - Update client address
     * - updateOccupation() - Update occupation details
     * - get_client_au_pr_point_details() - Get AU PR points details
     * - CalculatePoints() - Calculate immigration points
     * - calculateAgePoint() (private) - Calculate age points
     * - calculateEnglishPoint() (private) - Calculate English test points
     * - prpoints_add_to_notes() - Add points calculation to notes
     * - fetchClientContactNo() - Fetch client contact numbers
     * - fetchClientMatterAssignee() - Fetch matter assignee
     * - updateClientMatterAssignee() - Update matter assignee
     */
}

