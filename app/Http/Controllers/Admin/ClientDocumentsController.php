<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use App\Models\Admin;
use App\Models\ActivitiesLog;
use App\Models\PersonalDocumentType;
use Auth;
use Config;

/**
 * ClientDocumentsController
 * 
 * Handles personal document management including education documents,
 * document categories, tagging, and organization.
 * 
 * Maps to: resources/views/Admin/clients/tabs/personal_documents.blade.php
 *          resources/views/Admin/clients/tabs/not_used_documents.blade.php
 */
class ClientDocumentsController extends Controller
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
     * - addedudocchecklist() - Add education document checklist
     * - uploadedudocument() - Upload education document
     * - renamedoc() - Rename document
     * - save_tag() - Save document tags
     * - deletedocs() - Delete documents
     * - savetoapplication() - Save document to application
     * - download_document() - Download document
     * - addPersonalDocCategory() - Add personal document category
     * - updatePersonalDocCategory() - Update personal document category
     * - deletePersonalDocCategory() - Delete personal document category
     * - getPersonalDocTypes() - Get personal document types
     * - updatePersonalDocType() - Update personal document type
     * - deletePersonalDocType() - Delete personal document type
     * - notuseddoc() - Move document to not used tab
     * - backtodoc() - Move document back from not used tab
     */
}

