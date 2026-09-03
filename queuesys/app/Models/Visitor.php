<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'contact_number',
        'id_number',
        'type',
        'course_id',
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

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function previousOffice()
    {
        return $this->belongsTo(Office::class, 'previous_office_id');
    }

    public function transfers()
    {
        return $this->hasMany(VisitorTransfer::class)
            ->with(['fromOffice', 'toOffice', 'transferredBy'])
            ->orderBy('transferred_at');
    }
}
