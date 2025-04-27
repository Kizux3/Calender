<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class UserController extends BaseController
{
    private $jwtSecret;
    private $jwtExpiration;

    public function __construct()
    {
        $this->jwtSecret = $_ENV['JWT_SECRET'];
        $this->jwtExpiration = (int)($_ENV['JWT_EXPIRATION'] ?? 3600);

        if (empty($this->jwtSecret)) {
            throw new \RuntimeException('JWT_SECRET environment variable is not set');
        }
    }

    public function register(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        // Validate required fields
        if (!isset($data['email']) || !isset($data['password']) || !isset($data['name'])) {
            return $this->errorResponse($response, 'Email, password, and name are required');
        }

        // Check if user already exists
        $existingUser = User::where('email', $data['email'])->first();
        if ($existingUser) {
            return $this->errorResponse($response, 'User with this email already exists', 409);
        }

        // Create new user
        $user = new User();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = password_hash($data['password'], PASSWORD_DEFAULT);
        $user->save();

        // Generate JWT token
        $token = JWT::encode([
            'user_id' => $user->id,
            'email' => $user->email,
            'exp' => time() + $this->jwtExpiration
        ], $this->jwtSecret, 'HS256');

        return $this->successResponse($response, [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email
            ],
            'token' => $token
        ], 'User registered successfully');
    }

    public function login(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        // Validate required fields
        if (!isset($data['email']) || !isset($data['password'])) {
            return $this->errorResponse($response, 'Email and password are required');
        }

        // Find user by email
        $user = User::where('email', $data['email'])->first();
        if (!$user) {
            return $this->errorResponse($response, 'Invalid credentials', 401);
        }

        // Verify password
        if (!password_verify($data['password'], $user->password)) {
            return $this->errorResponse($response, 'Invalid credentials', 401);
        }

        // Generate JWT token
        $token = JWT::encode([
            'user_id' => $user->id,
            'email' => $user->email,
            'exp' => time() + $this->jwtExpiration
        ], $this->jwtSecret, 'HS256');

        return $this->successResponse($response, [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email
            ],
            'token' => $token
        ], 'Login successful');
    }
} 