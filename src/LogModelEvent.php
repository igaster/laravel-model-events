<?php

namespace Igaster\ModelEvents;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class LogModelEvent extends Model
{
    protected $table = 'log_model_events';
    protected $guarded = [];

    // ----------------------------------------------
    //  Relationships
    // ----------------------------------------------

    public function user()
    {
        $userClass = config('auth.providers.users.model');

        return $this->belongsTo($userClass, 'user_id');
    }

    public function model()
    {
        return $this->morphTo();
    }

    // ----------------------------------------------
    //  Scopes
    // ----------------------------------------------

    public function scopeWhereUser(Builder $query, $user)
    {
        return $query->where('user_id', $user->getKey());
    }

    public function scopeWhereModel(Builder $query, $model)
    {
        return $query->where([
            'model_type' => $model->getMorphClass(),
            'model_id' => $model->getKey(),
        ]);
    }

    // ----------------------------------------------
    //  Methods
    // ----------------------------------------------
}
