## Description
[![Laravel](https://img.shields.io/badge/Laravel-orange.svg)](http://laravel.com)
[![License](http://img.shields.io/badge/license-MIT-brightgreen.svg)](https://tldrlegal.com/license/mit-license)
[![Downloads](https://img.shields.io/packagist/dt/igaster/laravel-model-events.svg)](https://packagist.org/packages/igaster/laravel-model-events)
[![Build Status](https://img.shields.io/travis/igaster/laravel-model-events.svg)](https://travis-ci.org/igaster/laravel-model-events)
[![Codecov](https://img.shields.io/codecov/c/github/igaster/laravel-model-events.svg)](https://codecov.io/github/igaster/laravel-model-events)

This is as simple as keeping a diary for your models!

You can record a short message for any model with current timestamp and authenticated user.


## Installation:

A) Execute `composer require igaster/laravel-model-events`

This package includes a Service Provider that will be automatically discovered by Laravel.

B) Run migrations.

This will create a table `log_model_events` that will be used to store events.

## Usage

### Step 1: Add a Trait to your model:

```php
use Igaster\ModelEvents\Traits\LogsModelEvents;

class MyModel extends Eloquent
{
    use LogsModelEvents;

```

### Step 2: Log yout events:

#### a) Manually

Use the `logModelEvent("Description")` method to log any event


```php
class MyModel extends Eloquent
{
    public function myMethod()
    {
        // ...
        $modelEvent = $this->logModelEvent("Something Happened!");
    }
```

- The `logModelEvent()` method will also log a) the current authenticated user and b) the related model instance c) current timestamp
- This is a public method. You may also call it from your `$model` instance from anywhere

#### b) Automatically capture laravel model events:

Eloquent models fire [several events ](https://laravel.com/docs/5.7/eloquent#events) during updating, creating etc. These events can be automatically logged. Just define these events inside the `$logModelEvents` static array in your model:

```php

class MyModel extends Eloquent
{
    public static $logModelEvents = [
        'created',
        'updated',
    ];

```

- Now every time this model instance is changed, the event will be logged and attributed to the authenticated user.
- As a bonus a report of all the updated attributes will be added in the description!

### Step 3: Fetch a list of events:

#### a) From a `$model` instance:

```php
// This will retrieve the last 10 events logged for $model instance.
$modelEvents = $model->getModelEvents(10);
```

#### b) From a `$user` instance:

In order to query events from a $user model you must first include this trait with the User class:
Note: This trait is optional for the rest functions of this package!

```php
use Igaster\ModelEvents\Traits\UserLogsModelEvents;

class User extends Authenticatable
{
    use UserLogsModelEvents;
```

```php
// This will retrieve the last 10 events logged by this $user.
$modelEvents = $user->getUserModelEvents(10);
```

#### c) Build your own queries:

All relationships with the `LogModelEvent` model have been implemented. These are some valid queries:

```php
$user->modelEvents; // Get all model events for $user
$model->modelEvents; // Get all model events for $model
$model->modelEvents()->where(`created_at`, '>', $yesterday)->get(); // Custom Query

// Or you can build queries with the LogModelEvent model:
LogModelEvent::whereUser($user)->whereModel($model)->get();
```

### Step 4: Display Events:

#### a) Manually

Through a `LogModelEvents` model you can retrieve the `$user` and the `$model` instances:

```php
foreach($model->modelEvents as $modelEvent){
    $modelEvent->user; // User model
    $modelEvent->model; // Model related with the event (though polymorphic relathinships)
    $modelEvent->description; // String
    $modelEvent->created_at;  // Timestamp
}
```

Note the the `$modelEvent->model` is a polymorphic relationship and it will retrieve a `$model` instance on its respective class.

#### b) Use package sample view:

![image](https://user-images.githubusercontent.com/4586319/47613088-cf211e00-da90-11e8-8e32-76e23976adc6.JPG)


You may include the `model-events::modelEvents` partial in your views to render a list of events:

```php
    <div class="row">
        <div class="col-md-12">
            <h4>Actions History:</h4>

            @include('model-events::modelEvents', [
                'model' => $order
            ])

        </div>
    </div>
```

Available parameters are: `model`, `user`, `count_events`. All are optional