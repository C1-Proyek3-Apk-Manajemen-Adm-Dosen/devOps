<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccessControl extends Model
{
    protected $table = 'access_control';
    
    protected $primaryKey = 'id';
    
    public $timestamps = false;
    
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

    public function dokumen()
    {
        return $this->belongsTo(Dokumen::class, 'document_id', 'dokumen_id');
    }

    public function granteeUser()
    {
        return $this->belongsTo(User::class, 'grantee_user_id', 'id_user');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id_user');
    }
}