<?php

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;

class AuthMiddleware implements MiddlewareInterface
{
    private $jwtSecret;

    public function __construct()
    {
        $this->jwtSecret = $_ENV['JWT_SECRET'];
        if (empty($this->jwtSecret)) {
            throw new \RuntimeException('JWT_SECRET environment variable is not set');
        }
    }

    public function process(Request $request, RequestHandler $handler): Response
    {
        $authorization = $request->getHeaderLine('Authorization');
        
        if (empty($authorization)) {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'message' => 'Authorization header is required'
            ]));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }

        // Extract token from Bearer scheme
        if (preg_match('/Bearer\s+(.*)$/i', $authorization, $matches)) {
            $token = $matches[1];
        } else {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'message' => 'Invalid authorization header format'
            ]));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }

        try {
            // Verify and decode the token
            $decoded = JWT::decode($token, new Key($this->jwtSecret, 'HS256'));
            
            // Add user data to request attributes
            $request = $request->withAttribute('user_id', $decoded->user_id);
            $request = $request->withAttribute('email', $decoded->email);
            
            return $handler->handle($request);
        } catch (ExpiredException $e) {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'message' => 'Token has expired'
            ]));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        } catch (SignatureInvalidException $e) {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'message' => 'Invalid token signature'
            ]));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'message' => 'Invalid token'
            ]));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }
    }
} 