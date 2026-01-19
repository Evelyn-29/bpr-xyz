<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditCollateral extends Model
{
    use HasFactory;

    protected $fillable = [
        'credit_application_id',
        'jenis_agunan',
        'nomor_sertifikat',
        'atas_nama',
        'masa_berlaku',
        'foto_agunan',
        'file_sertifikat',
        'file_pbb'
    ];

    protected $casts = [
        'masa_berlaku' => 'date',
    ];

    public function creditApplication()
    {
        return $this->belongsTo(CreditApplication::class);
    }
}
