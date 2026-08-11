<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;


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

    protected function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Carbon::parse($value)->timezone('Asia/Jakarta')
        );
    }


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

    public function granteeUser()
    {
        return $this->belongsTo(User::class, 'grantee_user_id', 'id_user');
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

    public static function getStatusBadge($status)
    {
        return match($status) {
            'ACC' => [
                'class' => 'bg-green-100 text-green-700 border-2 border-green-300',
                'text' => 'Acc',
                'dot' => 'bg-green-700'
            ],
            'PENDING' => [
                'class' => 'bg-yellow-100 text-yellow-700 border-2 border-yellow-300',
                'text' => 'Pending',
                'dot' => 'bg-yellow-700'
            ],
            'REVISI' => [
                'class' => 'bg-orange-100 text-orange-700 border-2 border-orange-300',
                'text' => 'Revisi',
                'dot' => 'bg-orange-700'
            ],
            'TOLAK' => [
                'class' => 'bg-red-100 text-red-700 border-2 border-red-300',
                'text' => 'Ditolak',
                'dot' => 'bg-red-700'
            ],
            default => [
                'class' => 'bg-gray-100 text-gray-700 border-2 border-gray-300',
                'text' => '-',
                'dot' => 'bg-gray-700'
            ]
        };
    }
}