<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Poll extends Model
{
    use HasFactory;
    protected $guarded=['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function choices()
    {
        return $this->hasMany(Choice::class);
    }
    public function votes()
    {
        return $this->hasMany(Vote::class);
    }
}
