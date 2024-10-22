<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $table = 'tasks'; 

    protected $fillable = [
        'user_id',
        'titulo',
        'descripcion',
        'category_id',
        'estado',
        'prioridad',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
