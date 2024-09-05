<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComputerLabHistory extends Model
{
    protected $fillable = [
        'computer_lab_id', 'code', 'name', 'pc_no', 'owner', 'month_year', 'action', 'publish_status'
    ];

    protected $casts = [
        'month_year' => 'datetime',
    ];

    public function computerLab()
    {
        return $this->belongsTo(ComputerLab::class);
    }

    public function pemilik()
    {
        return $this->belongsTo(User::class, 'owner');
    }  

}
