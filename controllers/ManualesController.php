<?php

namespace Controllers;

use MVC\Router;

class ManualesController
{
    public static function index(Router $router)
    {
        $router->render('pages/manuales', [
            'titulo' => 'Manuales del Sistema'
        ]);
    }
}
