<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Type extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'types';
    public $timestamps = false;

    public function resolveRouteBinding($value, $field = null){
        return $this->where($field ?? 'id', $value)->withTrashed()->firstOrFail();
    }

    public function tickets(){
        return $this->hasMany(Ticket::class);
    }

    public function posts(){
        return $this->hasMany(Blog::class);
    }

    public function kb(){
        return $this->hasMany(KnowledgeBase::class);
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
