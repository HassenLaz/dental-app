<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class PatientFile extends Model
{
    protected $fillable = [
        'patient_id',
        'name',
        'file_path',
        'file_name',
        'mime_type',
        'file_size',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    // Helper: is this file an image?
    public function isImage(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    // Helper: is this file a PDF?
    public function isPdf(): bool
    {
        return $this->mime_type === 'application/pdf';
    }

    // Human-readable file size
    public function formattedSize(): string
    {
        $bytes = $this->file_size;
        if ($bytes < 1024)        return $bytes . ' B';
        if ($bytes < 1048576)     return round($bytes / 1024, 1) . ' KB';
        return round($bytes / 1048576, 1) . ' MB';
    }

    // Full URL to access the file
    public function url(): string
    {
        return Storage::url($this->file_path);
    }
}
