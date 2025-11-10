<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MyFatoorahController extends Controller
{
    public function callback(Request $request)
    {
        // Redirect to API V2 controller
        return app('App\Http\Controllers\Api\V2\MyfatoorahController')->callback($request);
    }
}
