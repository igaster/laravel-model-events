{{--
    Display a list of all events that have been logged by [$user] for some [$model]

    Parameters:
        - $model : any model that uses the LogsModelEvents trait
        - $user : a user model

    Example:

    <div class="row">
        <div class="col-md-12">
            <h4>Actions History:</h4>
            @include('model-events::modelEvents', [
                'model' => $model
            ])
        </div>
    </div>

 --}}

<?php
    $query = Igaster\ModelEvents\LogModelEvent::query();

    if(isset($user)) {
        $query->whereUser($user);
    }

    if(isset($model)) {
        $query->whereModel($model);
    }

    if(isset($count_events)) {
        $query->limit($count);
    }

    $modelEvents = $query->orderBy('created_at', 'desc')->get();
?>

<ul class="list-group">
    @foreach($modelEvents as $modelEvent)
        <li class="list-group-item">
            {{$modelEvent->description}}
            <div class="pull-right">
                @if($modelEvent->user)
                    <span class="label label-pill label-default">{{$modelEvent->user->email}}</span>
                @endif
                <span class="label label-pill label-info">{{$modelEvent->created_at}}</span>
            </div>
        </li>
    @endforeach
</ul>
