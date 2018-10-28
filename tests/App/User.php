<?php

namespace Igaster\ModelEvents\Tests\App;

use Igaster\ModelEvents\Traits\UserLogsModelEvents;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User  extends Authenticatable
{
    use UserLogsModelEvents;

    protected $table = 'users';
    protected $guarded = [];
    public $timestamps = false;

}