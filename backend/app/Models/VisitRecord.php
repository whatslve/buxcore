<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitRecord extends Model
{
    protected $fillable = [];
    protected $guarded = [];

    public function visit() {
        return $this->belongsTo(Visit::class);
    }
}
