<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Validator;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    /**
     * Validate request
     *
     * @param array $data
     * @param array $rules
     * @return array|boolean Array of validated inputs or false if failed
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validate(array $data, array $rules) {
        $validator = Validator::make($data, $rules);
        return $validator->validated();
    }

}
