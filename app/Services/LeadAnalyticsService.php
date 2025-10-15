<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\LeadFollowup;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LeadAnalyticsService
{
    /**
     * Get conversion funnel statistics
     */
    public function getConversionFunnel($startDate = null, $endDate = null)
    {
        $query = Admin::where('type', 'lead');
        
        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }
        
        $totalLeads = $query->count();
        $qualified = (clone $query)->where('lead_quality', '!=', 'cold')->count();
        $contacted = (clone $query)->whereHas('followups', function($q) {
            $q->where('status', 'completed');
        })->count();
        $interested = (clone $query)->whereHas('followups', function($q) {
            $q->where('outcome', 'interested');
        })->count();
        $converted = (clone $query)->where('type', 'client')->count();
        
        return [
            'total_leads' => $totalLeads,
            'qualified' => [
                'count' => $qualified,
                'percentage' => $totalLeads > 0 ? round(($qualified / $totalLeads) * 100, 2) : 0
            ],
            'contacted' => [
                'count' => $contacted,
                'percentage' => $totalLeads > 0 ? round(($contacted / $totalLeads) * 100, 2) : 0
            ],
            'interested' => [
                'count' => $interested,
                'percentage' => $totalLeads > 0 ? round(($interested / $totalLeads) * 100, 2) : 0
            ],
            'converted' => [
                'count' => $converted,
                'percentage' => $totalLeads > 0 ? round(($converted / $totalLeads) * 100, 2) : 0
            ],
        ];
    }
    
    /**
     * Get lead source performance
     */
    public function getSourcePerformance($startDate = null, $endDate = null)
    {
        $query = Admin::where('type', 'lead')
            ->select('source', DB::raw('COUNT(*) as total'))
            ->groupBy('source');
        
        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }
        
        $sources = $query->get();
        
        $performance = [];
        foreach ($sources as $source) {
            $converted = Admin::where('type', 'lead')
                ->where('source', $source->source)
                ->where('lead_status', 'converted')
                ->when($startDate, fn($q) => $q->where('created_at', '>=', $startDate))
                ->when($endDate, fn($q) => $q->where('created_at', '<=', $endDate))
                ->count();
            
            $performance[] = [
                'source' => $source->source ?: 'Unknown',
                'total_leads' => $source->total,
                'converted' => $converted,
                'conversion_rate' => $source->total > 0 ? round(($converted / $source->total) * 100, 2) : 0,
            ];
        }
        
        // Sort by total leads descending
        usort($performance, function($a, $b) {
            return $b['total_leads'] <=> $a['total_leads'];
        });
        
        return $performance;
    }
    
    /**
     * Get agent performance metrics
     */
    public function getAgentPerformance($startDate = null, $endDate = null)
    {
        $agents = Admin::where('type', 'admin')
            ->where(function($q) {
                $q->where('role_type', 'agent')
                  ->orWhere('role_type', 'team_lead');
            })
            ->get();
        
        $performance = [];
        
        foreach ($agents as $agent) {
            $assignedLeads = Admin::where('type', 'lead')
                ->where('assignee', $agent->id)
                ->when($startDate, fn($q) => $q->where('created_at', '>=', $startDate))
                ->when($endDate, fn($q) => $q->where('created_at', '<=', $endDate))
                ->count();
            
            $convertedLeads = Admin::where('type', 'lead')
                ->where('assignee', $agent->id)
                ->where('lead_status', 'converted')
                ->when($startDate, fn($q) => $q->where('created_at', '>=', $startDate))
                ->when($endDate, fn($q) => $q->where('created_at', '<=', $endDate))
                ->count();
            
            $completedFollowups = LeadFollowup::where('assigned_to', $agent->id)
                ->where('status', 'completed')
                ->when($startDate, fn($q) => $q->where('completed_at', '>=', $startDate))
                ->when($endDate, fn($q) => $q->where('completed_at', '<=', $endDate))
                ->count();
            
            $overdueFollowups = LeadFollowup::where('assigned_to', $agent->id)
                ->where('status', 'overdue')
                ->count();
            
            $avgResponseTime = $this->calculateAvgResponseTime($agent->id, $startDate, $endDate);
            
            $performance[] = [
                'agent_id' => $agent->id,
                'agent_name' => $agent->first_name . ' ' . $agent->last_name,
                'assigned_leads' => $assignedLeads,
                'converted_leads' => $convertedLeads,
                'conversion_rate' => $assignedLeads > 0 ? round(($convertedLeads / $assignedLeads) * 100, 2) : 0,
                'completed_followups' => $completedFollowups,
                'overdue_followups' => $overdueFollowups,
                'avg_response_time_hours' => $avgResponseTime,
            ];
        }
        
        // Sort by conversion rate descending
        usort($performance, function($a, $b) {
            return $b['conversion_rate'] <=> $a['conversion_rate'];
        });
        
        return $performance;
    }
    
    /**
     * Calculate average response time for an agent
     */
    protected function calculateAvgResponseTime($agentId, $startDate = null, $endDate = null)
    {
        $followups = LeadFollowup::where('assigned_to', $agentId)
            ->where('status', 'completed')
            ->when($startDate, fn($q) => $q->where('completed_at', '>=', $startDate))
            ->when($endDate, fn($q) => $q->where('completed_at', '<=', $endDate))
            ->whereNotNull('completed_at')
            ->get();
        
        if ($followups->isEmpty()) {
            return 0;
        }
        
        $totalHours = 0;
        $count = 0;
        
        foreach ($followups as $followup) {
            $diff = $followup->completed_at->diffInHours($followup->scheduled_date);
            $totalHours += abs($diff);
            $count++;
        }
        
        return $count > 0 ? round($totalHours / $count, 2) : 0;
    }
    
    /**
     * Get time-based lead trends
     */
    public function getLeadTrends($period = 'month', $count = 12)
    {
        $trends = [];
        
        for ($i = $count - 1; $i >= 0; $i--) {
            $date = match($period) {
                'week' => now()->subWeeks($i),
                'month' => now()->subMonths($i),
                'year' => now()->subYears($i),
                default => now()->subMonths($i),
            };
            
            $startDate = match($period) {
                'week' => $date->copy()->startOfWeek(),
                'month' => $date->copy()->startOfMonth(),
                'year' => $date->copy()->startOfYear(),
            };
            
            $endDate = match($period) {
                'week' => $date->copy()->endOfWeek(),
                'month' => $date->copy()->endOfMonth(),
                'year' => $date->copy()->endOfYear(),
            };
            
            $newLeads = Admin::where('type', 'lead')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();
            
            $converted = Admin::where('type', 'lead')
                ->where('lead_status', 'converted')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();
            
            $trends[] = [
                'period' => $startDate->format($period == 'year' ? 'Y' : ($period == 'week' ? 'M d' : 'M Y')),
                'new_leads' => $newLeads,
                'converted' => $converted,
                'conversion_rate' => $newLeads > 0 ? round(($converted / $newLeads) * 100, 2) : 0,
            ];
        }
        
        return $trends;
    }
    
    /**
     * Get lead quality distribution
     */
    public function getLeadQualityDistribution($startDate = null, $endDate = null)
    {
        $query = Admin::where('type', 'lead')
            ->select('lead_quality', DB::raw('COUNT(*) as count'))
            ->groupBy('lead_quality');
        
        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }
        
        $distribution = $query->get();
        
        $total = $distribution->sum('count');
        
        return $distribution->map(function($item) use ($total) {
            return [
                'quality' => $item->lead_quality ?: 'Unqualified',
                'count' => $item->count,
                'percentage' => $total > 0 ? round(($item->count / $total) * 100, 2) : 0,
            ];
        })->toArray();
    }
    
    /**
     * Get comprehensive dashboard statistics
     */
    public function getDashboardStats($startDate = null, $endDate = null)
    {
        $query = Admin::where('type', 'lead');
        
        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }
        
        return [
            'total_leads' => (clone $query)->count(),
            'new_this_month' => Admin::where('type', 'lead')
                ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->count(),
            'converted' => (clone $query)->where('lead_status', 'converted')->count(),
            'active' => (clone $query)->where('lead_status', 'active')->count(),
            'cold' => (clone $query)->where('lead_quality', 'cold')->count(),
            'hot' => (clone $query)->where('lead_quality', 'hot')->count(),
            'avg_conversion_time' => $this->getAvgConversionTime($startDate, $endDate),
            'pending_followups' => LeadFollowup::where('status', 'pending')->count(),
            'overdue_followups' => LeadFollowup::where('status', 'overdue')->count(),
        ];
    }
    
    /**
     * Calculate average time to convert a lead
     */
    protected function getAvgConversionTime($startDate = null, $endDate = null)
    {
        $convertedLeads = Admin::where('type', 'lead')
            ->where('lead_status', 'converted')
            ->when($startDate, fn($q) => $q->where('created_at', '>=', $startDate))
            ->when($endDate, fn($q) => $q->where('created_at', '<=', $endDate))
            ->whereNotNull('updated_at')
            ->get();
        
        if ($convertedLeads->isEmpty()) {
            return 0;
        }
        
        $totalDays = 0;
        foreach ($convertedLeads as $lead) {
            $totalDays += $lead->created_at->diffInDays($lead->updated_at);
        }
        
        return round($totalDays / $convertedLeads->count(), 1);
    }
}

