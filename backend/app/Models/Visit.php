<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\VisitsRecord;
use Illuminate\Support\Facades\Auth;

class Visit extends Model
{
    protected $fillable = [

    ];

    protected $guarded = [

    ];

    public function visitRecord() {
        return $this->hasMany(VisitsRecord::class);
    }
    public function createVisitRecord(User $user) {
        $visitRecord = VisitsRecord::create([
            'user_id'  => $user->id,
            'visit_id' => $this->id,
            'reward'   => $this->reward,
            'cost'     => $this->cost,
            'status'   => 'started',
            'finished_at' => null,
        ]);
    }

    public function checkIfRecordExists(User $user, Visit $visit)
    {
        return VisitsRecord::where('user_id', $user->id)
            ->where('visit_id', $visit->id)
            ->exists();
    }
}
