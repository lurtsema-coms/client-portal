<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MoreInfoValue extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];

    public function moreInfo()
    {
        return $this->belongsTo(MoreInfo::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
