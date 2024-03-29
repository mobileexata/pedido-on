<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    protected function trataFloat($num)
    {
        return str_replace(['.', ','], ['', '.'], $num);
    }

    protected function returnResponseError(Exception $e, string $message = null)
    {
        $response = ['error' => $e->getMessage()];

        if ($message) {
            $response['message'] = $message;
        }

        return response()->json($response, 500);
    }

}
