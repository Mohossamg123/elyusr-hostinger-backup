<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Donor extends Model
{
    protected $fillable=[
        'name',
        'email',
        'phone',
        'address'
    ];


    public function donations()
{
    return $this->hasMany(Donation::class);
}
}
