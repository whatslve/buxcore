<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quest extends Model
{
    use HasFactory;
    protected $fillable = [

    ];

    protected $guarded = [

    ];

    public function links() {
        return $this->hasMany(QuestLink::class)->orderBy('order', 'desc');
    }

    public function targetingTime() {
        return $this->hasMany(QuestTargetingTime::class);
    }

    public function targetingGeo() {
        return $this->hasMany(QuestTargetingGeo::class);
    }

    public function targetingProfile()
    {
        return $this->hasOne(QuestTargetingProfile::class);
    }

    public function limits() {
        return $this->hasMany(QuestLimit::class);
    }

    public function payment()
    {
        return $this->hasOne(QuestPayment::class);
    }
}
