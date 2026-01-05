<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitsRecord extends Model
{
    protected $table = 'visits_record';
    protected $fillable = [];
    protected $guarded = [];

    public function visit() {
        return $this->belongsTo(Visit::class);
    }

    public function finishVisit() {
        return $this->update([
            'status' => 'finished',
        ]);
    }
}
