<?php

namespace App\Exceptions;

use Exception;

class Exceptions extends Exception
{
    public function render()
    {
        return response()->json([
            'status' => false,
            'msg'    => ["Something went wrong!"],
        ], 500);
    }

    public static function error($error = null, $code = 500)
    {
        return response()->json([
            'status' => false,
            'msg'    => [$error ?? "Something went wrong!"],
        ], $code);
    }

    public static function forbidden()
    {
        return response()->json([
            'status' => false,
            'msg'    => ["Forbidden!"],
        ], 403);
    }

    public static function success($msg = null)
    {
        return response()->json([
            'status' => true,
            'msg'    => [$msg ?? "Success!"],
        ]);
    }

    public static function validationError($error, $code = 422)
    {
        return response()->json([
            'status' => false,
            'msg'    => is_array($error) ? $error : [$error],
        ], $code);
    }

}
