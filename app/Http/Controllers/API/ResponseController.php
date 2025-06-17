<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ResponseController extends Controller
{
    public function sendSuccess($result, $msg)
    {
        $response = [
            'status' => true,
            'message' => $msg,
            'data' => $result,
        ];
        return response()->json($response, 200);
    }
    public function sendError($error, $msg = [], $code = 404)
    {
        $response = [
            'status' => false,
            'message' => $msg,
            'data' => $error,
        ];
        return response()->json($response, $code);
    }
}
