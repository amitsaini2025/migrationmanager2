<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use App\Models\Admin;
use App\Models\ActivitiesLog;
use App\Models\VisaDocChecklist;
use App\Models\VisaDocumentType;
use Auth;
use Config;

/**
 * ClientVisaDocumentsController
 * 
 * Handles visa-specific document management including visa document
 * checklists, verification, and document types.
 * 
 * Maps to: resources/views/Admin/clients/tabs/visa_documents.blade.php
 */
class ClientVisaDocumentsController extends Controller
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
     * - addvisadocchecklist() - Add visa document checklist
     * - uploadvisadocument() - Upload visa document
     * - getvisachecklist() - Get visa document checklist
     * - verifydoc() - Verify document
     * - renamechecklistdoc() - Rename checklist document
     * - addVisaDocCategory() - Add visa document category
     * - updateVisaDocCategory() - Update visa document category
     * - deleteVisaDocCategory() - Delete visa document category
     * - getVisaDocTypes() - Get visa document types
     * - updateVisaDocType() - Update visa document type
     * - deleteVisaDocType() - Delete visa document type
     */
}

