<?php

namespace App\Config;

use Illuminate\Database\Capsule\Manager as Capsule;

class Eloquent
{
    public static function init()
    {
        $capsule = new Capsule;

        $capsule->addConnection([
            'driver'    => 'mysql',
            'host'      => $_ENV['DB_HOST'],
            'database'  => $_ENV['DB_NAME'],
            'username'  => $_ENV['DB_USER'],
            'password'  => $_ENV['DB_PASS'],
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ]);

        // Make this Capsule instance available globally (optional)
        $capsule->setAsGlobal();
        
        // Setup the Eloquent ORM
        $capsule->bootEloquent();
    }
} 