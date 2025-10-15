<?php

namespace App\Http\Controllers\Admin\Sms;

use App\Http\Controllers\Controller;
use App\Models\SmsTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * SmsTemplateController
 * 
 * Handles SMS template CRUD operations
 * Sprint 4 will add full UI for template management
 */
class SmsTemplateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * List all SMS templates (Sprint 4)
     */
    public function index(Request $request)
    {
        // TODO: Implement in Sprint 4
        $templates = SmsTemplate::orderBy('name')->paginate(20);
        
        return view('Admin.sms.templates.index', compact('templates'));
    }

    /**
     * Show create template form (Sprint 4)
     */
    public function create()
    {
        // TODO: Implement in Sprint 4
        return view('Admin.sms.templates.create');
    }

    /**
     * Store new template
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:sms_templates,name',
            'message' => 'required|string|max:1600',
            'description' => 'nullable|string',
            'variables' => 'nullable|array',
            'category' => 'nullable|string|max:100',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $template = SmsTemplate::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Template created successfully',
            'data' => $template
        ]);
    }

    /**
     * Show edit template form (Sprint 4)
     */
    public function edit($id)
    {
        // TODO: Implement in Sprint 4
        $template = SmsTemplate::findOrFail($id);
        
        return view('Admin.sms.templates.edit', compact('template'));
    }

    /**
     * Update template
     */
    public function update(Request $request, $id)
    {
        $template = SmsTemplate::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:sms_templates,name,' . $id,
            'message' => 'required|string|max:1600',
            'description' => 'nullable|string',
            'variables' => 'nullable|array',
            'category' => 'nullable|string|max:100',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $template->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Template updated successfully',
            'data' => $template
        ]);
    }

    /**
     * Delete template
     */
    public function destroy($id)
    {
        $template = SmsTemplate::findOrFail($id);
        
        // Check if template is in use
        if ($template->usage_count > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete template that has been used. Consider deactivating it instead.'
            ], 422);
        }

        $template->delete();

        return response()->json([
            'success' => true,
            'message' => 'Template deleted successfully'
        ]);
    }

    /**
     * Get template by ID (API endpoint)
     */
    public function show($id)
    {
        $template = SmsTemplate::findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => $template
        ]);
    }

    /**
     * Get active templates (API endpoint for dropdowns)
     */
    public function active()
    {
        $templates = SmsTemplate::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'message', 'variables', 'category']);

        return response()->json([
            'success' => true,
            'data' => $templates
        ]);
    }
}

