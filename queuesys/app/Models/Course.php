<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function staff()
    {
        return $this->belongsToMany(User::class, 'staff_course');
    }

    public function visitors()
    {
        return $this->hasMany(Visitor::class);
    }
}
