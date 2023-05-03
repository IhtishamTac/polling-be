<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Choice extends Model
{
    use HasFactory;
    protected $guarded=['id'];

    public function poll()
    {
        return $this->belongsTo(Poll::class);
    }
    public function votes()
    {
        return $this->hasMany(Vote::class);
    }
}
