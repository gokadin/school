<?php

namespace App\Http\Controllers;

use Library\Http\Response;

abstract class ApiController extends Controller
{
    const STATUS_OK = 200;
    const STATUS_UNAUTHORIZED = 401;
    const STATUS_INTERNAL_SERVER_ERROR = 500;
    const STATUS_BAD_REQUEST = 400;

    protected function respond(array $data): Response
    {
        return $data ? $this->respondOk($data) : $this->respondBadRequest();
    }

    protected function respondOk($data = [])
    {
        return $this->response->json($data, self::STATUS_OK);
    }

    protected function respondUnauthorized($data = 'You are not authorized to perform this action.')
    {
        return $this->response->json($data, self::STATUS_UNAUTHORIZED);
    }

    protected function respondServerError($data = 'Internal server error.')
    {
        return $this->response->json($data, self::STATUS_INTERNAL_SERVER_ERROR);
    }

    protected function respondBadRequest($data = 'Bad request.')
    {
        return $this->response->json($data, self::STATUS_BAD_REQUEST);
    }
}