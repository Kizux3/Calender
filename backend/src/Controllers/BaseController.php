<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;

class BaseController
{
    protected function jsonResponse(Response $response, $data, int $status = 200): Response
    {
        $response->getBody()->write(json_encode($data));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }

    protected function successResponse(Response $response, $data = null, string $message = 'Success'): Response
    {
        return $this->jsonResponse($response, [
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ]);
    }

    protected function errorResponse(Response $response, string $message, int $status = 400): Response
    {
        return $this->jsonResponse($response, [
            'status' => 'error',
            'message' => $message
        ], $status);
    }
} 