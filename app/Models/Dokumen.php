<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Models\User; // <-- Diperlukan untuk relasi 'owner'
use App\Models\AccessControl; // <-- DITAMBAHKAN: Diperlukan untuk relasi 'accessControls'

class Dokumen extends Model
{
    use HasFactory;

    protected $table = 'dokumen';
    protected $primaryKey = 'dokumen_id';

    protected $fillable = [
        'judul',
        'nomor_dokumen',
        'tanggal_terbit',
        'kategori_id',
        'file_path',
        'deskripsi',
        'created_by',
        'status',
        'owner_user_id',
    ];

    
    protected $casts = [
        'tanggal_terbit' => 'date',
        // 'owner_user_id' => 'array', // <-- DIHAPUS: Baris ini salah karena di DB tipenya integer
    ];

    // --- Relations ---
    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id', 'kategori_id');
    }

    /**
     * Relasi untuk mendapatkan User yang memiliki dokumen (berdasarkan owner_user_id)
     */
    public function owner()
    {
        // DIPERBAIKI: Menggunakan foreign key 'owner_user_id' sesuai skema SQL
        return $this->belongsTo(User::class, 'owner_user_id', 'id_user');
    }
    
    /**
     * Relasi untuk mendapatkan User yang membuat dokumen (berdasarkan created_by)
     */
    public function creator()
    {
        // DIPERBAIKI: Menggunakan foreign key 'created_by' secara eksplisit
        return $this->belongsTo(\App\Models\User::class, 'created_by', 'id_user');
    }

    /**
     * Relasi 'user' untuk mengatasi error RelationNotFoundException [user].
     * Ini mengarah ke 'creator' (user yang mengupload)
     */
    public function user()
    {
        // Ini adalah relasi yang dicari oleh Controller (with('user'))
        // Kita arahkan ke relasi creator()
        return $this->creator();
    }

    public function komentar()
    {
        return $this->hasMany(Komentar::class, 'dokumen_id', 'dokumen_id');
    }

    public function versi()
    {
        return $this->hasMany(VersiDokumen::class, 'dokumen_id', 'dokumen_id');
    }

    /**
     * DITAMBAHKAN: Relasi untuk mengatasi error RelationNotFoundException [accessControls].
     * Dikonfirmasi dari file AccessControl.php:
     * - Model: App\Models\AccessControl
     * - Foreign Key: 'document_id'
     * - Local Key: 'dokumen_id'
     */
    public function accessControls()
    {
        return $this->hasMany(\App\Models\AccessControl::class, 'document_id', 'dokumen_id');
    }

    // --- Accessor URL publik MinIO ---
    public function getUrlAttribute(): ?string
    {
        if (!$this->file_path) return null;
        return Storage::disk('minio')->url($this->file_path);
    }
}