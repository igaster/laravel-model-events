<?php

use Illuminate\Support\Facades\DB;

abstract class abstractTest extends Orchestra\Testbench\TestCase
{


    // -----------------------------------------------
    //  Global Setup (Run once)
    // -----------------------------------------------

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        
        if (file_exists(__DIR__.'/../.env')) {
            $dotenv = new Dotenv\Dotenv(__DIR__.'/../');
            $dotenv->load();
        }
    }

    // -----------------------------------------------
    //   Set Laravel App Configuration
    // -----------------------------------------------

    /*
    * Manually set configuration for testing
    * Note: Usually config is loading values from .env file
    * Edit phpunit.xml for default .env values
    */
    protected function getEnvironmentSetUp($app)
    {
        $config = $app['config'];

        $config->set('auth.providers.users.model', Igaster\ModelEvents\Tests\User::class);
    }

    // -----------------------------------------------
    //   add Service Providers & Facades
    // -----------------------------------------------

    protected function getPackageProviders($app)
    {
        return [
            \Igaster\ModelEvents\modelEventsServiceProvider::class
        ];
    }


    protected function getPackageAliases($app)
    {
        return [
            // 'Image' => Intervention\Image\Facades\Photo::class,
        ];
    }

    // -----------------------------------------------
    //  Helpers
    // -----------------------------------------------

    public function reloadModel(&$model)
    {
        $className = get_class($model);
        $model = $className::find($model->id);
        return $model;
    }

    // -----------------------------------------------
    //  Added functionality
    // -----------------------------------------------

    protected function seeInDatabase($table, array $data, $connection = null)
    {
        $count = DB::table($table)->where($data)->count();
        
        $this->assertGreaterThan(0, $count, sprintf(
            'Unable to find row in database table [%s] that matched attributes [%s].',
        
            $table,
        
            json_encode($data)
        ));

        return $this;
    }

    protected function notSeeInDatabase($table, array $data, $connection = null)
    {
        $count = DB::table($table)->where($data)->count();
        
        $this->assertEquals(0, $count, sprintf(
            'Found unexpected records in database table [%s] that matched attributes [%s].',
        
            $table,
        
            json_encode($data)
        ));

        return $this;
    }
}
