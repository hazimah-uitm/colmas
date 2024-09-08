<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class MaintenanceRecord extends Model
{
    use LogsActivity;
    use SoftDeletes;

    protected static $logAttributes = ['*'];

    protected static $logOnlyDirty = true;

    protected $fillable = [
        'computer_name',
        'ip_address',
        'lab_management_id',
        'work_checklist_id',
        'vms_no',
        'aduan_unit_no',
        'remarks',
        'entry_option',
    ];

    protected $casts = [
        'work_checklist_id' => 'array',
    ];

    public function workChecklist()
    {
        return $this->belongsTo(WorkChecklist::class);
    }

    public function labManagement()
    {
        return $this->belongsTo(LabManagement::class);
    }
}
