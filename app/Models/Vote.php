<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    use HasFactory;
    protected $guarded=['id'];

    public function choice()
    {
        return $this->belongsTo(Choice::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function poll()
    {
        return $this->belongsTo(Poll::class);
    }
}
