<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessControl extends Model
{
    use HasFactory;

    /**
     * Nama tabel di database
     */
    protected $table = 'access_control';

    /**
     * Primary key dari tabel
     */
    protected $primaryKey = 'id';

    /**
     * Kolom timestamps (created_at, updated_at)
     * tidak digunakan karena di tabel tidak ada updated_at
     */
    public $timestamps = false;

    /**
     * Kolom yang bisa diisi mass-assignment
     */
    protected $fillable = [
        'document_id',
        'grantee_user_id',
        'perm',
        'status',
        'expires_at',
        'created_at',
        'created_by',
    ];
    protected $casts = [
        'created_at' => 'datetime',
        'expires_at' => 'datetime',
    ];


    /**
     * Relasi ke tabel users
     * (user yang memberi hak akses dokumen)
     */
    public function pemberiAkses()
    {
        return $this->belongsTo(User::class, 'created_by', 'id_user');
    }

    /**
     * Relasi ke tabel users
     * (user yang menerima akses dokumen, misal TU)
     */
    public function penerimaAkses()
    {
        return $this->belongsTo(User::class, 'grantee_user_id', 'id_user');
    }

    /**
     * Relasi ke tabel dokumen
     */
    public function dokumen()
    {
        return $this->belongsTo(Dokumen::class, 'document_id', 'dokumen_id');
    }

    /**
     * Scope untuk memfilter notifikasi yang ditujukan ke user tertentu (misal TU)
     */
    public function scopeUntukUser($query, $userId)
    {
        return $query->where('grantee_user_id', $userId);
    }

    /**
     * Scope untuk mengambil notifikasi terbaru
     */
    public function scopeTerbaru($query)
    {
        return $query->orderByDesc('created_at');
    }

    /**
     * Helper untuk menampilkan status akses dalam format teks
     */
    public function getStatusLabelAttribute()
    {
        switch ($this->status) {
            case 'active':
                return 'Aktif';
            case 'pending':
                return 'Pending';
            case 'expired':
                return 'Kadaluarsa';
            case 'revoked':
                return 'Ditarik';
            default:
                return $this->status;
        }
    }
}