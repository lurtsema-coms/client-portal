<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PersonInContact extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'user_id',
        'cell_number',
        'email',
    ];

}
