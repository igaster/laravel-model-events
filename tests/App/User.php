<?php

namespace Igaster\ModelEvents\Tests;

use Igaster\ModelEvents\Traits\UserLogsModelEvents;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User  extends Authenticatable
{
    use UserLogsModelEvents;

    protected $table = 'users';
    protected $guarded = [];
    public $timestamps = false;

}