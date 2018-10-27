<?php

namespace Igaster\ModelEvents;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

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

    public function scopeWhereUser(Builder $query, $user){
        return $query->where('user_id',$user->id);
    }

    public function scopeWhereModel(Builder $query, $model){
        return $query->where([
            'model_type' => get_class($model),
            'model_id' => $model->id
        ]);
    }

    // ----------------------------------------------
    //  Methods
    // ----------------------------------------------
}