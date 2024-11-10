<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Campus extends Model
{
    use LogsActivity;
    use SoftDeletes;

    protected static $logAttributes = ['*'];

    protected static $logOnlyDirty = true;

    protected $fillable = [
        'name',
        'publish_status'
    ];

    public function getPublishStatusAttribute()
    {
        return $this->attributes['publish_status'] ? 'Aktif' : 'Tidak Aktif';
    }
    
    public function users()
    {
        return $this->belongsToMany(User::class, 'campus_user');
    }

    public function computerLab()
    {
        return $this->hasMany(ComputerLab::class);
    }
}
