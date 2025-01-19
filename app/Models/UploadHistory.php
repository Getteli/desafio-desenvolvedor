<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class UploadHistory extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'upload_histories';
    protected $fillable = ['file_path', 'file_name', 'uploaded_at', 'uploaded_by', 'created_at', 'updated_at'];
    protected $guarded = ['_id'];
}
