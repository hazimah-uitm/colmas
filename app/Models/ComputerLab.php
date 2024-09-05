<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class ComputerLab extends Model
{
    use LogsActivity;
    use SoftDeletes;

    protected static $logAttributes = ['*'];

    protected static $logOnlyDirty = true;

    protected $fillable = [
        'code',
        'name',
        'campus_id',
        'pemilik_id',
        'username',
        'password',
        'no_of_computer',
        'publish_status'
    ];

    public function getPublishStatusAttribute()
    {
        return $this->attributes['publish_status'] ? 'Aktif' : 'Tidak Aktif';
    }

    public function pemilik()
    {
        return $this->belongsTo(User::class, 'pemilik_id');
    }

    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    // public function maintenanceRecord()
    // {
    //     return $this->hasMany(MaintenanceRecord::class);
    // }

    // public function labManagement()
    // {
    //     return $this->hasMany(LabManagement::class);
    // }

    public function histories()
    {
        return $this->hasMany(ComputerLabHistory::class);
    }
}
