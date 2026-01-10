<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CreditFacility extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'kode',
        'nama',
        'deskripsi',
        'max_jangka_waktu',
        'aktif'
    ];

    /**
     * Relasi ke tier (plafond dan bunga)
     */
    public function tiers()
    {
        return $this->hasMany(CreditFacilityTier::class);
    }

    /**
     * Relasi ke pengajuan kredit
     */
    public function creditApplications()
    {
        return $this->hasMany(CreditApplication::class);
    }
}
