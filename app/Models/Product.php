<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = [
        'name', 'price'
    ];

    public function solds() {
        return $this->hasMany('App\Models\ReportDetail', 'product_id');
    }
    public function reports() {
        return $this->belongsToMany('App\Models\Report', 'report_details','product_id','report_id');
    }
}
