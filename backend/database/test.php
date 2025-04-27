<?php

require __DIR__ . '/../vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Database configuration
$dbConfig = [
    'host' => $_ENV['DB_HOST'] ?? 'localhost',
    'database' => $_ENV['DB_NAME'] ?? 'calendar_app',
    'username' => $_ENV['DB_USER'] ?? 'root',
    'password' => $_ENV['DB_PASS'] ?? '',
];

try {
    // Connect to database
    $pdo = new PDO(
        "mysql:host={$dbConfig['host']};dbname={$dbConfig['database']}", 
        $dbConfig['username'], 
        $dbConfig['password']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Test database connection
    echo "Database connection successful\n";
    
    // Create test user
    $email = 'test@example.com';
    $password = password_hash('test123', PASSWORD_DEFAULT);
    $name = 'Test User';
    
    // Check if test user already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        echo "Test user already exists\n";
    } else {
        // Create test user
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, $password]);
        echo "Test user created successfully\n";
    }
    
    // Test users table
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    $userCount = $stmt->fetchColumn();
    echo "Users table has {$userCount} records\n";
    
    // Test events table
    $stmt = $pdo->query("SELECT COUNT(*) FROM events");
    $eventCount = $stmt->fetchColumn();
    echo "Events table has {$eventCount} records\n";
    
    echo "Database test completed successfully\n";
    
} catch(PDOException $e) {
    die("Database test failed: " . $e->getMessage() . "\n");
} 