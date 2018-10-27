<?php
/**
 * Created by PhpStorm.
 * User: Giannis
 * Date: 2018-10-28
 * Time: 12:15 πμ
 */

namespace Igaster\ModelEvents\Controllers;


class ModelEventController
{

    public function userEvents()
    {
        $user = auth()->user();

        return view('model-events::modelEvents',[

        ]);
    }
}