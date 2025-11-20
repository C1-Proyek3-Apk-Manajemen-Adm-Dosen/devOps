<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfilDosen extends Model
{
    protected $table = 'profil_dosen';
    protected $primaryKey = 'profil_id';

    protected $fillable = [
        'id_user',
        'nidn',
        'nip',
        'nama_lengkap',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'jabatan_fungsional',
        'pangkat_golongan',
        'status_dosen',
        'perguruan_tinggi',
        'fakultas',
        'program_studi',
        'riwayat_pendidikan',
        'jumlah_penelitian',
        'jumlah_publikasi',
        'jumlah_pengabdian',
        'sertifikat_pendidik',
        'tahun_sertifikasi',
        'email_institusi',
        'no_telepon',
        'bio',
        'bidang_keahlian',
        'foto_profil',
        'pddikti_url',
        'last_scraped_at',
        'is_verified',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'riwayat_pendidikan' => 'array',
        'last_scraped_at' => 'datetime',
        'is_verified' => 'boolean',
        'jumlah_penelitian' => 'integer',
        'jumlah_publikasi' => 'integer',
        'jumlah_pengabdian' => 'integer',
    ];

    /**
     * Relasi ke User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    /**
     * Accessor: Pendidikan terakhir
     */
    public function getPendidikanTerakhirAttribute(): ?string
    {
        $pendidikan = $this->riwayat_pendidikan;
        return !empty($pendidikan) ? $pendidikan[0] : null;
    }

    /**
     * Cek apakah profil perlu di-refresh (sudah 7 hari)
     */
    public function needsRefresh(): bool
    {
        if (!$this->last_scraped_at) {
            return true;
        }
        return $this->last_scraped_at->diffInDays(now()) >= 7;
    }

    /**
     * Scope: hanya yang verified
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }
}
