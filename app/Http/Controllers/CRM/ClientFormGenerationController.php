<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Log;
use App\Models\Admin;
use App\Models\ActivitiesLog;
use App\Models\Form956;
use App\Models\CostAssignmentForm;
use App\Models\ClientEoiReference;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use Auth;

/**
 * ClientFormGenerationController
 * 
 * Handles form generation including Form 956, cost assignments,
 * visa agreements, and EOI references.
 * 
 * Maps to: resources/views/Admin/clients/tabs/form_generation_client.blade.php
 *          resources/views/Admin/clients/tabs/form_generation_lead.blade.php
 */
class ClientFormGenerationController extends Controller
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
     * - generateagreement() - Generate visa agreements and forms
     * - savecostassignment() - Save cost assignment form
     * - savereferences() - Save EOI references
     * - getMigrationAgentDetail() - Get migration agent details
     * - getVisaAggreementMigrationAgentDetail() - Get migration agent for visa agreement
     * - getCostAssignmentMigrationAgentDetail() - Get migration agent for cost assignment
     */
}

