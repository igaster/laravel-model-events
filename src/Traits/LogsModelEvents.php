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
    // List of Laravel model events that should be recorded
    // public static $logModelEvents = ['created','updated'];

    public static function bootLogsModelEvents()
    {

        if (property_exists(self::class, 'logModelEvents')) {

            foreach (self::$logModelEvents as $eventName) {

                static::$eventName(function ($model) use ($eventName) {
                    $description = $eventName;

                    if ($eventName == 'updating' || $eventName == 'updated') {
                        if ($dirty = $model->getDirty()) {

                            $changed = [];
                            foreach ($dirty as $key => $value) {
                                if (!self::shouldHideKey($key)) {
                                    if (self::shouldSanitizeKey($key)) {
                                        $changed[] = "'$key': ***";
                                    } else {
                                        $changed[] = "'$key': [" . ($model->original[$key] ?? '-') . "]→[$value]";
                                    }
                                }
                            }

                            if ($changed) {
                                $description .= ':' . implode(', ', $changed);
                            }
                        }
                    }

                    $model->logModelEvent($description);
                });

            }
        }
    }

    private static function shouldHideKey($key): bool
    {
        return isset(self::$dontLogUpdatedColumns) && in_array($key, self::$dontLogUpdatedColumns);
    }

    private static function shouldSanitizeKey($key): bool
    {
        return isset(self::$sanitizeUpdatedColumns) && in_array($key, self::$sanitizeUpdatedColumns);
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
        if (!$this->getKey()){
            return null;
        }

        return $this->modelEvents()->create([
            'description' => $description,
            'user_id' => Auth::check() ? Auth::user()->id : null,
        ]);
    }

    public function getModelEvents($count = null)
    {
        $query = $this->modelEvents()->orderBy('id', 'desc');
        if($count) {
            $query->limit($count);
        }
        return $query->get();
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
