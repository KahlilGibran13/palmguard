<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KondisiKebun extends Model
{
    protected $table = 'kondisi_kebun';

    protected $fillable = [
        'id_pohon',
        'suhu',
        'kelembapan',
    ];

    public function pohon(): BelongsTo
    {
        return $this->belongsTo(Pohon::class, 'id_pohon');
    }
}