<?php

use Igaster\ModelEvents\Tests\TestModel;
use Igaster\ModelEvents\Tests\User;
use Igaster\ModelEvents\LogModelEvent;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class TestCase extends abstractTest
{

    // -----------------------------------------------
    //   Global Setup(Run Once)
    // -----------------------------------------------

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        // Your Code here...
    }

    public static function tearDownAfterClass()
    {
        // Your Code here...
        parent::tearDownAfterClass();
    }

    // -----------------------------------------------
    //  Setup Database (Run before each Test)
    // -----------------------------------------------

    public function setUp()
    {
        parent::setUp();

        // Run package migrations
        Artisan::call('migrate', [
            "--force"=> true,
        ]);

        // -- Set  migrations
        Schema::create('test_table', function (Blueprint $table) {
            $table->increments('id');
            $table->string('key')->nullable();
            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email')->unique();
            $table->rememberToken();
        });

        User::create([
            'id' => 1,
            'email' => 'admin@myapp.com'
        ]);

        User::create([
            'id' => 2,
            'email' => 'user@myapp.com'
        ]);
    }

    public function _tearDown()
    {
        Schema::drop('test_table');
        parent::teadDown();
    }

    // -----------------------------------------------
    //  Tests
    // -----------------------------------------------

    public function testDummy()
    {
        $model = TestModel::create([
            'key' => 'value',
        ]);
        $model->fresh();
        $this->assertEquals("value", $model->key);
    }

    public function testDatabaseTable()
    {
        $model_event = LogModelEvent::create([
            'model_type' => TestModel::class,
            'model_id' => 234,
            'description' => 'xxx',
        ]);

        $model_event = $model_event->fresh();
        $this->assertEquals('xxx', $model_event->description);
    }

    public function testManualCreateEvent()
    {
        $model = TestModel::create();

        $model->logModelEvent('123');

        $this->seeInDatabase('log_model_events',[
            'description' => '123',
            'model_type' => TestModel::class,
            'model_id' => $model->id,
            'user_id' => null,
        ]);
    }


    public function testAuthUser()
    {
        $user = User::find(1);
        Auth::login($user);

        $model = TestModel::create();

        $modelEvent = $model->logModelEvent('auth');

        $modelEvent = $modelEvent->fresh();

        $this->assertInstanceOf(User::class, $modelEvent->user);

        $this->assertEquals($user->id, $modelEvent->user_id);
        $this->assertEquals('auth', $modelEvent->description);
    }


    public function testGuest()
    {
        $model = TestModel::create();

        $modelEvent = $model->logModelEvent('guest');

        $this->assertNull($modelEvent->user);
    }

    public function testRetrieveModelFromEvent()
    {
        $model = TestModel::create([
            'key' => 'value',
        ]);

        $modelEvent = $model->logModelEvent('guest');

        $this->assertInstanceOf(TestModel::class, $modelEvent->model);
        $this->assertEquals('value', $modelEvent->model->key);
    }

    public function testRetrieveUserFromEvent()
    {
        $model = TestModel::create([
            'key' => 'value',
        ]);

        $modelEvent = $model->logModelEvent('guest');

        $this->assertNull($modelEvent->user);

        Auth::login(User::find(1));

        $modelEvent = $model->logModelEvent('user');


        $this->assertInstanceOf(User::class, $modelEvent->user);
        $this->assertEquals(1, $modelEvent->user->id);
    }

    public function testGetEventsForUser()
    {
        $model = TestModel::create();

        $user_1 = User::find(1);
        $user_2 = User::find(2);

        Auth::login($user_1);

        $model->logModelEvent('one');
        $model->logModelEvent('two');

        Auth::login($user_2);

        $model->logModelEvent('three');

        Auth::logout();

        $model->logModelEvent('four');

        $this->assertEquals(2, LogModelEvent::whereUser($user_1)->count());
        $this->assertEquals(1, LogModelEvent::whereUser($user_2)->count());
    }

    public function testGetEventsForUserFromUserTrait()
    {
        $model = TestModel::create();

        $user_1 = User::find(1);
        $user_2 = User::find(2);

        Auth::login($user_1);

        $model->logModelEvent('one');
        $model->logModelEvent('two');

        Auth::login($user_2);

        $model->logModelEvent('three');

        Auth::logout();

        $model->logModelEvent('four');

        $this->assertEquals(2, $user_1->modelEvents->count());
        $this->assertEquals(1, $user_2->modelEvents->count());

        $this->assertEquals('three', $user_2->modelEvents->first()->description);
    }

    public function testLastNEventsForUser()
    {
        $model = TestModel::create();
        $user = User::find(1);
        Auth::login($user);

        $model->logModelEvent('one');
        $model->logModelEvent('two');
        $model->logModelEvent('three');

        $events = $user->getUserModelEvents(2);

        $this->assertEquals([
            "three",
            "two",
        ], $events->pluck('description')->toArray());
    }

    public function testGetEventsForModel()
    {
        $model_1 = TestModel::create();
        $model_2 = TestModel::create();
        $model_3 = TestModel::create();

        $model_1->logModelEvent('one');
        $model_1->logModelEvent('two');

        $model_2->logModelEvent('three');

        $this->assertEquals(2, LogModelEvent::whereModel($model_1)->count());
        $this->assertEquals(1, LogModelEvent::whereModel($model_2)->count());
        $this->assertEquals(0, LogModelEvent::whereModel($model_3)->count());
    }

    public function testLastEventForModel()
    {
        $model = TestModel::create();

        $model->logModelEvent('one');
        $model->logModelEvent('two');

        $this->assertEquals("two", $model->getLastModelEvent()->description);
    }

    public function testLastNEventsForModel()
    {
        $model = TestModel::create();

        $model->logModelEvent('one');
        $model->logModelEvent('two');
        $model->logModelEvent('three');

        $events = $model->getModelEvents(2);

        $this->assertEquals([
            "three",
            "two",
        ], $events->pluck('description')->toArray());
    }

    public function testQueryEventsForUserAndModel()
    {
        $model_1 = TestModel::create();
        $model_2 = TestModel::create();
        $user_1 = User::find(1);
        $user_2 = User::find(2);

        Auth::login($user_1);

        $model_1->logModelEvent('one');
        $model_2->logModelEvent('two');

        Auth::login($user_2);

        $model_1->logModelEvent('three');

        $this->assertEquals('two', LogModelEvent::whereUser($user_1)->whereModel($model_2)->first()->description);
    }

    public function testAutoLogLaravelCreateModel()
    {
        TestModel::$logModelEvents = [
            'creating',
            'created',
            'updating',
            'updated',
            'saving',
            'saved',
        ];
        $model = TestModel::create();

        $this->assertEquals([
            'created',
            "saved",
        ], $model->modelEvents->pluck('description')->toArray());
    }

    public function testAutoLogLaravelUpdateModel()
    {
        TestModel::$logModelEvents = [
            'creating',
            'created',
            'updating',
            'updated',
            'saving',
            'saved',
        ];
        $model = TestModel::create([
            'key' => 'value1'
        ]);
        $model->clearModelEvents();

        $model->update([
            'key' => 'value2'
        ]);

        $this->assertEquals([
            "saving",
            "updating - Updated values:'key': [value1]=>[value2]",
            "updated - Updated values:'key': [value1]=>[value2]",
            "saved",
        ], $model->modelEvents->pluck('description')->toArray());
    }

    public function testAutoLogOnlyLaravelModelEventsDeclaredOnLogmodeleventsArray()
    {
        TestModel::$logModelEvents = ['created'];
        $model = TestModel::create();
        $model->update([
            'key' => 'value'
        ]);
        $this->assertEquals('created',$model->getLastModelEvent()->description);
    }

}
