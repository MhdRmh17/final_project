<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class ProjectForm extends Model
{
    use HasFactory;
protected $attributes = [
        'status' => 'pending',
    ];

    protected $fillable = [
        'user_id',
        'title',
        'supervisor',
        'submitted_at',
        'description',
        'pdf_path',
        'status',
    ];

    /**
     * حوّل هذا العمود إلى DateTime Carbon تلقائيًا
     */
    protected $casts = [
        'submitted_at' => 'datetime',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
