<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Office extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'abbreviation',
        'priority_counter',
    ];

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
