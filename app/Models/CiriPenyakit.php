<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CiriPenyakit extends Model
{
    protected $table = 'ciri_penyakit';

    protected $fillable = [
        'id_penyakit', 'ciri'
    ];

    public function penyakit()
    {
        return $this->belongsTo(Penyakit::class, 'id');
    }
}
