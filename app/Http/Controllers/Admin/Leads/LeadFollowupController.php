<?php

namespace App\Http\Controllers\Admin\Leads;

use App\Http\Controllers\Controller;
use App\Services\LeadFollowupService;
use App\Models\LeadFollowup;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeadFollowupController extends Controller
{
    protected $followupService;
    
    public function __construct(LeadFollowupService $followupService)
    {
        $this->middleware('auth:admin');
        $this->followupService = $followupService;
    }
    
    /**
     * Display follow-ups list (with filters)
     * Only lead owner or super admin can access
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = LeadFollowup::with(['lead', 'assignedAgent', 'creator']);
        
        // Role-based filtering: super admin sees all, others see only their leads
        if ($user->role != 1) {
            // Get lead IDs where user is the owner
            $ownedLeadIds = \App\Models\Lead::where('user_id', $user->id)->pluck('id')->toArray();
            $query->whereIn('lead_id', $ownedLeadIds);
        }
        
        // Additional role-specific filtering
        if ($user->role_type === 'agent') {
            $query->where('assigned_to', $user->id);
        } elseif ($user->role_type === 'team_lead') {
            $teamMembers = Admin::where('team_lead_id', $user->id)->pluck('id')->toArray();
            $teamMembers[] = $user->id; // Include team lead's own followups
            $query->whereIn('assigned_to', $teamMembers);
        }
        
        // Status filter
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }
        
        // Date range filter
        if ($request->has('date_from')) {
            $query->whereDate('scheduled_date', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->whereDate('scheduled_date', '<=', $request->date_to);
        }
        
        // Type filter
        if ($request->has('type') && $request->type != 'all') {
            $query->where('followup_type', $request->type);
        }
        
        $followups = $query->orderBy('scheduled_date', 'desc')->paginate(20);
        
        return view('Admin.leads.followups.index', compact('followups'));
    }
    
    /**
     * Store a new follow-up
     * Only lead owner or super admin can create followups
     */
    public function store(Request $request)
    {
        // Check if user is super admin or lead owner
        $user = Auth::user();
        $lead = \App\Models\Lead::find($request->lead_id);
        
        if (!$lead) {
            return redirect()->back()->with('error', 'Lead not found');
        }
        
        if ($user->role != 1 && $lead->user_id != $user->id) {
            return redirect()->back()->with('error', 'Unauthorized: You can only create followups for your own leads');
        }
        
        $this->validate($request, [
            'lead_id' => 'required|exists:admins,id',
            'type' => 'required|in:call,email,meeting,sms,whatsapp,other',
            'scheduled_date' => 'required|date|after_or_equal:now',
            'assigned_to' => 'required|exists:admins,id',
            'priority' => 'nullable|in:low,medium,high,urgent',
        ]);
        
        $followup = $this->followupService->scheduleFollowup(
            $request->lead_id,
            [
                'type' => $request->type,
                'assigned_to' => $request->assigned_to,
                'scheduled_date' => $request->scheduled_date,
                'priority' => $request->priority ?? 'medium',
                'notes' => $request->notes,
            ]
        );
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Follow-up scheduled successfully',
                'followup' => $followup
            ]);
        }
        
        return redirect()->back()->with('success', 'Follow-up scheduled successfully');
    }
    
    /**
     * Complete a follow-up
     * Only lead owner or super admin can complete followups
     */
    public function complete(Request $request, $id)
    {
        // Check if user is super admin or lead owner
        $user = Auth::user();
        $followup = LeadFollowup::find($id);
        
        if (!$followup) {
            return redirect()->back()->with('error', 'Followup not found');
        }
        
        $lead = \App\Models\Lead::find($followup->lead_id);
        
        if ($user->role != 1 && (!$lead || $lead->user_id != $user->id)) {
            return redirect()->back()->with('error', 'Unauthorized: You can only complete followups for your own leads');
        }
        
        $this->validate($request, [
            'outcome' => 'required|in:interested,not_interested,callback_later,converted,no_response',
            'notes' => 'nullable|string',
            'schedule_next' => 'nullable|boolean',
        ]);
        
        $followup = $this->followupService->completeFollowup($id, $request->all());
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Follow-up completed successfully',
                'followup' => $followup
            ]);
        }
        
        return redirect()->back()->with('success', 'Follow-up completed successfully');
    }
    
    /**
     * Reschedule a follow-up
     * Only lead owner or super admin can reschedule followups
     */
    public function reschedule(Request $request, $id)
    {
        // Check if user is super admin or lead owner
        $user = Auth::user();
        $followup = LeadFollowup::find($id);
        
        if (!$followup) {
            return redirect()->back()->with('error', 'Followup not found');
        }
        
        $lead = \App\Models\Lead::find($followup->lead_id);
        
        if ($user->role != 1 && (!$lead || $lead->user_id != $user->id)) {
            return redirect()->back()->with('error', 'Unauthorized: You can only reschedule followups for your own leads');
        }
        
        $this->validate($request, [
            'new_date' => 'required|date|after_or_equal:now',
            'reason' => 'nullable|string',
        ]);
        
        $newFollowup = $this->followupService->rescheduleFollowup(
            $id,
            $request->new_date,
            $request->reason
        );
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Follow-up rescheduled successfully',
                'followup' => $newFollowup
            ]);
        }
        
        return redirect()->back()->with('success', 'Follow-up rescheduled successfully');
    }
    
    /**
     * Cancel a follow-up
     * Only lead owner or super admin can cancel followups
     */
    public function cancel(Request $request, $id)
    {
        // Check if user is super admin or lead owner
        $user = Auth::user();
        $followup = LeadFollowup::find($id);
        
        if (!$followup) {
            return redirect()->back()->with('error', 'Followup not found');
        }
        
        $lead = \App\Models\Lead::find($followup->lead_id);
        
        if ($user->role != 1 && (!$lead || $lead->user_id != $user->id)) {
            return redirect()->back()->with('error', 'Unauthorized: You can only cancel followups for your own leads');
        }
        
        $this->validate($request, [
            'reason' => 'nullable|string',
        ]);
        
        $followup = $this->followupService->cancelFollowup($id, $request->reason);
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Follow-up cancelled successfully'
            ]);
        }
        
        return redirect()->back()->with('success', 'Follow-up cancelled successfully');
    }
    
    /**
     * My follow-ups dashboard
     * Only lead owner or super admin can access
     */
    public function myFollowups(Request $request)
    {
        $agentId = Auth::id();
        $user = Auth::user();
        
        // Build base query
        $query = LeadFollowup::with(['lead', 'assignedAgent']);
        
        // Role-based filtering: super admin sees all, others see only their leads
        if ($user->role != 1) {
            // Get lead IDs where user is the owner
            $ownedLeadIds = \App\Models\Lead::where('user_id', $user->id)->pluck('id')->toArray();
            $query->whereIn('lead_id', $ownedLeadIds);
        }
        
        // Additional role-specific filtering
        if ($user->role_type === 'agent') {
            $query->where('assigned_to', $agentId);
        } elseif ($user->role_type === 'team_lead') {
            $teamMembers = Admin::where('team_lead_id', $agentId)->pluck('id')->toArray();
            $teamMembers[] = $agentId;
            $query->whereIn('assigned_to', $teamMembers);
        }
        // Super admins see all (no additional filter)
        
        $today = (clone $query)->dueToday()->get();
        $overdue = (clone $query)->overdue()->get();
        $upcoming = (clone $query)
            ->where('status', 'pending')
            ->where('scheduled_date', '>', today()->endOfDay())
            ->orderBy('scheduled_date')
            ->take(10)
            ->get();
        
        // Get agent stats
        $stats = $this->followupService->getAgentStats($agentId);
        
        return view('Admin.leads.followups.dashboard', compact('today', 'overdue', 'upcoming', 'stats'));
    }
    
    /**
     * Get follow-ups for a specific lead (AJAX)
     * Only lead owner or super admin can view followups
     */
    public function getLeadFollowups($leadId)
    {
        // Check if user is super admin or lead owner
        $user = Auth::user();
        $lead = \App\Models\Lead::find($leadId);
        
        if (!$lead) {
            return response()->json(['error' => 'Lead not found'], 404);
        }
        
        if ($user->role != 1 && $lead->user_id != $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $followups = LeadFollowup::where('lead_id', $leadId)
            ->with(['assignedAgent', 'creator'])
            ->orderBy('scheduled_date', 'desc')
            ->get();
        
        $stats = $this->followupService->getLeadStats($leadId);
        
        return response()->json([
            'followups' => $followups,
            'stats' => $stats
        ]);
    }
}

