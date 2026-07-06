<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penyakit extends Model
{
    protected $table = 'penyakit';
    
    protected $fillable = [
        'id', 'nama_penyakit', 'warna_badge', 'status'
        //, 'deskripsi'
    ];

    protected $primaryKey = 'id';
    
    public function ciriCiri() 
    {
        return $this->hasMany(CiriPenyakit::class, 'id_penyakit');
    }
}
