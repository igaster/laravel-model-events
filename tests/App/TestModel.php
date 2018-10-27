<?php

namespace Igaster\ModelEvents\Tests;

use Igaster\ModelEvents\Traits\LogsModelEvents;
use Illuminate\Database\Eloquent\Model as Eloquent;

class TestModel extends Eloquent
{
    use LogsModelEvents;

    protected $table = 'test_table';
    protected $guarded = [];
    public $timestamps = false;

    public static $logModelEvents = [];
}
