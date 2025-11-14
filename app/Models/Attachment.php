<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'filename', 'original_name', 'mime_type', 'path', 'size',
        'asset_id', 'uploaded_by', 'description'
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}