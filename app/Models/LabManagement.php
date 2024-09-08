<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class LabManagement extends Model
{
    use LogsActivity;
    use SoftDeletes;

    protected static $logAttributes = ['*'];

    protected static $logOnlyDirty = true;

    protected $fillable = [
        'computer_lab_id',
        'lab_checklist_id',
        'software_id',
        'start_time',
        'end_time',
        'computer_no',
        'pc_maintenance_no',
        'pc_unmaintenance_no',
        'pc_damage_no',
        'remarks_submitter',
        'remarks_checker',
        'status',
        'checked_by',
        'checked_at',
        'submitted_by',
        'submitted_at'
    ];

    protected $casts = [
        'lab_checklist_id' => 'array',
        'software_id' => 'array',
    ];

    public function computerLab()
    {
        return $this->belongsTo(ComputerLab::class);
    }

    public function software()
    {
        return $this->belongsTo(Software::class);
    }

    public function labChecklist()
    {
        return $this->belongsTo(LabChecklist::class);
    }

    public function maintenanceRecords()
    {
        return $this->hasMany(MaintenanceRecord::class);
    }

    public function checkedBy()
    {
        return $this->belongsTo(User::class, 'checked_by');
    }

    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }
}
