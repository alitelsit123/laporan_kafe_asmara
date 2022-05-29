<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    public $timestamps = false;
    public $fillable = [
        'name', 'total_income', 'tanggal'
    ];

    public function details() {
        return $this->hasMany('App\Models\ReportDetail', 'report_id');
    }
}
