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
        'abbreviation',
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

    public function staff()
    {
        return $this->hasMany(User::class)->where('role', 'staff');
    }
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function outgoingTransfers()
    {
        return $this->hasMany(VisitorTransfer::class, 'from_office_id');
    }

    public function incomingTransfers()
    {
        return $this->hasMany(VisitorTransfer::class, 'to_office_id');
    }
}
