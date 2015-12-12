<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

abstract class ApiController extends Controller
{
    const STATUS_OK = 200;
    const STATUS_INTERNAL_SERVER_ERROR = 500;
    const STATUS_BAD_REQUEST = 401;

    public function respondOk($data = [])
    {
        return $this->response->json($data, self::STATUS_OK);
    }

    public function respondServerError($data = 'Internal server error.')
    {
        return $this->response->json($data, self::STATUS_INTERNAL_SERVER_ERROR);
    }

    public function respondBadRequest($data = 'Bad request.')
    {
        return $this->response->json($data, self::STATUS_BAD_REQUEST);
    }
}