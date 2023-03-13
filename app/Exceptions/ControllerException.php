<?php

namespace App\Exceptions;

use Exception;
use http\Env\Response;

class ControllerException extends Exception
{
    public function render()
    {
        return redirect()->back()->with([
            'alertType' => 'error',
            'message'   => "Something went wrong!"
        ]);
    }

    public static function error()
    {
        return redirect()->back()->with([
            'alertType' => 'error',
            'message'   => "Something went wrong!"
        ]);
    }

    public static function success($route = null, $msg = null)
    {
        if (is_null($route)) {
            return redirect()->back()->with([
                'alertType' => 'success',
                'message'   => $msg ?? "Successfully created!"
            ]);
        } else {
            return redirect()->route($route)->with([
                'alertType' => 'success',
                'message'   => $msg ?? "Successfully created!"
            ]);
        }
    }
}
