<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportDetail extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'sub_total', 'quantity','product_id','report_id'
    ];
    public function product() {
        return $this->belongsTo('App\Models\Product');
    }
    public function report() {
        return $this->belongsTo('App\Models\Report');
    }
}
