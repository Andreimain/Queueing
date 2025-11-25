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
        'previous_office_id',
        'queue_number',
        'ticket_number',
        'status',
        'priority',
        'cashier_id',
    ];

    protected $casts = [
        'priority' => 'boolean',
    ];

    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    public function cashier() {
        return $this->belongsTo(User::class, 'cashier_id');
    }

}
