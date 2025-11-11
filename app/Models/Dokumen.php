<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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
    ];

    protected $casts = [
        'tanggal_terbit' => 'date',
    ];

    // --- Relations ---
    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id', 'kategori_id');
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function komentar()
    {
        return $this->hasMany(Komentar::class, 'dokumen_id', 'dokumen_id');
    }

    public function versi()
    {
        return $this->hasMany(VersiDokumen::class, 'dokumen_id', 'dokumen_id');
    }

    // --- Accessor URL publik MinIO ---
    public function getUrlAttribute(): ?string
    {
        if (!$this->file_path) return null;
        return Storage::disk('minio')->url($this->file_path);
    }
}
