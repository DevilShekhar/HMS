<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomPack extends Model
{
    use HasFactory;

    protected $fillable = [
        'designer_id',
        'name',
        'cover_render',
        'optional_renders',
        'pdf_2d_drawing',
        'decor_material_chart',
    ];

    protected $casts = [
        'optional_renders' => 'array',
    ];

    public function designer()
    {
        return $this->belongsTo(User::class, 'designer_id');
    }
}
