<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'contact_number',
        'id_number',
        'office_id',
        'queue_number',
        'status',
    ];

    /**
     * A Visitor belongs to an Office.
     */
    public function office()
    {
        return $this->belongsTo(Office::class);
    }
}
