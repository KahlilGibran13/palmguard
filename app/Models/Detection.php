<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Detection extends Model
{
    protected $table = 'detection';

    protected $fillable = [
        'id_pohon', 'id_penyakit',
        'filename', 'image_path','disease_name', 'status', 'description', 'confidence',
        'bounding_box', 'yolo_raw', 'file_size', 'source', 'latitude', 'longitude',
    ];

    protected $casts = [
        'bounding_box'   => 'array',
        'yolo_raw'       => 'array',
        'confidence'     => 'float',
        'latitude'       => 'float',
        'longitude'      => 'float',
    ];

    public function getFotoLokasiNamaAttribute()
    {
        return $this->pohon ? $this->pohon->nama_pohon : null;
    }

    public function pohon(): BelongsTo
    {
        return $this->belongsTo(Pohon::class, 'id_pohon', 'id');
    }
}
