<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Todo extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'descriptions', 'is_done', 'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Accessor untuk label is_done
    public function getIsDoneLabelAttribute()
    {
        return $this->is_done ? 'Yes' : 'No';
    }
}
