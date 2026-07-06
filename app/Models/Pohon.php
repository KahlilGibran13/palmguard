<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pohon extends Model
{
    protected $table = 'pohon';
    
    protected $fillable = [
        'nama_pohon', 'latitude', 'longitude'
    ];

    protected $casts = [
        'latitude'       => 'float',
        'longitude'      => 'float',
    ];

    public function detections(): HasMany
    {
        return $this->hasMany(Detection::class, 'id_pohon');
    }

   public function kebun()
    {
        return $this->hasOne(KondisiKebun::class, 'id_pohon');
    }
}
