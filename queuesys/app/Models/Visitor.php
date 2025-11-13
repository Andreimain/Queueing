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
        'priority',
        'cashier_id',
    ];

    protected $casts = [
        'priority' => 'boolean',
    ];

    /**
     * A Visitor belongs to an Office.
     */
    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    public function cashier() {
        return $this->belongsTo(User::class, 'cashier_id');
    }

}
