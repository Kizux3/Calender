<?php

use Slim\Factory\AppFactory;
use DI\Container;
use App\Middleware\AuthMiddleware;
use App\Controllers\EventController;
use App\Config\Eloquent;

require __DIR__ . '/../vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Initialize Eloquent
Eloquent::init();

// Create Container
$container = new Container();
AppFactory::setContainer($container);

// Create App
$app = AppFactory::create();

// Add Error Middleware
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

// Add CORS Middleware
$app->add(function ($request, $handler) {
    $response = $handler->handle($request);
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});

// Handle OPTIONS requests
$app->options('/{routes:.+}', function ($request, $response) {
    return $response;
});

// Add Body Parsing Middleware
$app->addBodyParsingMiddleware();

// Add welcome route
$app->get('/', function ($request, $response) {
    $response->getBody()->write(json_encode([
        'status' => 'success',
        'message' => 'Calendar API is running'
    ]));
    return $response->withHeader('Content-Type', 'application/json');
});

// Load Auth Routes
$authRoutes = require __DIR__ . '/../src/routes/auth.php';
$authRoutes($app);

// Protected Event Routes
$app->group('/api/events', function ($group) {
    $group->get('', [EventController::class, 'index']);
    $group->post('', [EventController::class, 'create']);
    $group->get('/{id}', [EventController::class, 'show']);
    $group->put('/{id}', [EventController::class, 'update']);
    $group->delete('/{id}', [EventController::class, 'delete']);
})->add(new AuthMiddleware());

// Run App
$app->run();
