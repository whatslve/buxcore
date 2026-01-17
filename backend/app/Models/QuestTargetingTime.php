<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestTargetingTime extends Model
{
    use HasFactory;

    protected $fillable = [

    ];

    protected $guarded = [

    ];

    public function quest()
    {
        return $this->belongsTo(Quest::class);
    }

}
