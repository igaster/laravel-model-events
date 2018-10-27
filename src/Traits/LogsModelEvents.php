<?php

namespace Igaster\ModelEvents\Traits;


/**
 * Add this trait to a Model to enable Log custom events.
 * It will log current auth user
 *
 * We must manually call the logModelEvent('Some Event') method to log an event
 */

use Igaster\ModelEvents\LogModelEvent;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Auth;

trait LogsModelEvents
{


    public static function boot() {

        parent::boot();


        if(property_exists(self::class,'logModelEvents')){

            foreach(self::$logModelEvents as $eventName){

                static::$eventName(function($model) use ($eventName){
                    $description = $eventName;

                    if($eventName == 'updating' || $eventName == 'updated'){
                        if($dirty = $model->getDirty()){

                            $changed=[];
                            foreach($dirty as $key => $value){
                                $changed[] = "'$key': [" . ($model->original[$key] ?? '-') . "]=>[$value]";
                            }

                            if ($changed) {
                                $description .= ' - Updated values:'. implode(', ', $changed);
                            }
                        }
                    }

                    $model->logModelEvent($description);
                });

            }
        }

    }

    // ----------------------------------------------
    //  Relationships
    // ----------------------------------------------

    /**
     * @return MorphMany
     */
    public function modelEvents()
    {
        return $this->morphMany(LogModelEvent::class, 'model');
    }


    // ----------------------------------------------
    //  Methods
    // ----------------------------------------------

    public function logModelEvent($description = '')
    {
        if (!$this->id){
            return null;
        }

        return $this->modelEvents()->create([
            'description' => $description,
            'user_id' => Auth::check() ? Auth::user()->id : null,
        ]);
    }

    public function getModelEvents()
    {
        return $this->modelEvents()->orderBy('created_at', 'desc')->get();
    }

    public function getLastModelEvent()
    {
        return $this->modelEvents()->orderBy('id', 'desc')->first();
    }

    public function clearModelEvents()
    {
        return $this->modelEvents()->delete();
    }

}
