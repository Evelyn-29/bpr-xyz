<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_pengajuan',
        'user_id',
        'credit_facility_id',
        'tujuan_pinjaman',
        'jumlah_pinjaman',
        'jangka_waktu',
        'sumber_pendapatan',
        'status',
        'submitted_at',
        'slik_path',
        'slik_status',
        'slik_notes',
        'manager_id',
        'managed_at',
        'recommendation_status',
        'manager_recommended_amount',
        'manager_recommended_tenor',
        'recommended_amount',
        'recommended_tenor',
        'manager_note',
        'direktur_note',
        'requires_npwp',
        'no_perjanjian_kredit',
        'tgl_akad',
        'jam_akad',
        'approved_by',
        'approved_at'
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'managed_at' => 'datetime',
        'approved_at' => 'datetime',
        'tgl_akad' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function nasabahProfile()
    {
        return $this->belongsTo(NasabahProfile::class, 'user_id', 'user_id');
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function creditFacility()
    {
        return $this->belongsTo(CreditFacility::class);
    }

    public function detail()
    {
        return $this->hasOne(CreditApplicationDetail::class);
    }

    public function collateral()
    {
        return $this->hasOne(CreditCollateral::class);
    }

    public function documents()
    {
        return $this->hasMany(CreditDocument::class);
    }

    public function payments()
    {
        return $this->hasMany(CreditPayment::class);
    }
}
