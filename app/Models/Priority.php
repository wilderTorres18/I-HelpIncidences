<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Priority extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'priorities';

    public $timestamps = false;

    public function resolveRouteBinding($value, $field = null){
        return $this->where($field ?? 'id', $value)->withTrashed()->firstOrFail();
    }

    public function tickets(){
        return $this->hasMany(Ticket::class);
    }

    public function scopeFilter($query, array $filters){
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%'.$search.'%');
            });
        })->when($filters['trashed'] ?? null, function ($query, $trashed) {
            if ($trashed === 'with') {
                $query->withTrashed();
            } elseif ($trashed === 'only') {
                $query->onlyTrashed();
            }
        });
    }
}
