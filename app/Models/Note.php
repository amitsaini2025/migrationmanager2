<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use Notifiable;

    protected $fillable = [
        'id','user_id','client_id','lead_id','unique_group_id','title','description','note_deadline','mail_id','type','pin','followup_date','folloup','assigned_to','status','task_group','matter_id','mobile_number','created_at', 'updated_at'
    ];

	public $sortable = ['id', 'created_at', 'updated_at','task_group','followup_date'];


    /**
     * Get the client that owns the note.
     */
    public function client()
    {
        return $this->belongsTo(Admin::class, 'client_id');
    }

    /**
     * Get the user who created the note.
     */
    public function user()
    {
        return $this->belongsTo(Admin::class, 'user_id');
    }

    /**
     * Get the user assigned to the note.
     */
    public function assignedUser()
    {
        return $this->belongsTo(Admin::class, 'assigned_to');
    }

    /**
     * Legacy method for backward compatibility
     */
    public function noteClient()
    {
        return $this->client();
    }

    /**
     * Legacy method for backward compatibility
     */
    public function noteUser()
    {
        return $this->user();
    }

    /**
     * Legacy method for backward compatibility
     */
    public function assigned_user()
    {
        return $this->assignedUser();
    }

    /**
     * Legacy relationship - Appointment model has been removed
     * This relationship is kept for backward compatibility but will return null
     * 
     * @deprecated Appointment system has been removed
     */
    public function lead()
    {
        // Appointment model no longer exists - old appointment system removed
        // Returning null to prevent errors
        return null;
    }

}
