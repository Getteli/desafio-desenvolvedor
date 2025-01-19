<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Upload extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'uploads';
    protected $fillable = ['RptDt', 'TckrSymb', 'MktNm', 'SctyCtgyNm', 'ISIN', 'CrpnNm', 'created_at', 'updated_at'];
    protected $guarded = ['_id'];
}
