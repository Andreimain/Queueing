<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Office extends Model
{
    use HasFactory;

    /**
     * Allow mass-assignment of these attributes.
     * Keep this in sync with your offices table columns.
     */
    protected $fillable = [
        'name',
        'priority_counter',
    ];

    /**
     * Relationships
     * An Office has many Visitors.
     */
    public function visitors()
    {
        return $this->hasMany(Visitor::class);
    }

    public function office()
    {
        return $this->belongsTo(Office::class);
    }

}
