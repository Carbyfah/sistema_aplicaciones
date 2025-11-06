<?php

namespace MVC;

class Router
{
    public $getRoutes = [];
    public $postRoutes = [];
    protected $base = '';

    public function get($url, $fn)
    {
        $this->getRoutes[$this->base . $url] = $fn;
    }

    public function post($url, $fn)
    {
        $this->postRoutes[$this->base . $url] = $fn;
    }

    public function setBaseURL($base)
    {
        $this->base = $base;
    }

    public function comprobarRutas()
    {
        $currentUrl = $_SERVER['REQUEST_URI'] ? str_replace("?" . $_SERVER['QUERY_STRING'], '', $_SERVER['REQUEST_URI']) : $this->base . '/';
        $method = $_SERVER['REQUEST_METHOD'];

        if ($method === 'GET') {
            $fn = $this->getRoutes[$currentUrl] ?? null;
        } else {
            $fn = $this->postRoutes[$currentUrl] ?? null;
        }

        if ($fn) {
            call_user_func($fn, $this);
        } else {
            if (empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                $this->render('pages/notfound');
            } else {
                getHeadersApi();
                echo json_encode(["ERROR" => "PÁGINA NO ENCONTRADA"]);
            }
        }
    }

    public function render($view, $datos = [], $layout = true)
    {
        foreach ($datos as $key => $value) {
            $$key = $value;
        }

        ob_start();

        // CORREGIDO: views está en la misma carpeta que Router.php
        $viewPath = __DIR__ . "/views/$view.php";

        if (!file_exists($viewPath)) {
            die("Vista no encontrada: $viewPath");
        }

        include_once $viewPath;
        $contenido = ob_get_clean();

        if ($layout) {
            $layoutPath = __DIR__ . "/views/layout.php";

            if (!file_exists($layoutPath)) {
                die("Layout no encontrado: $layoutPath");
            }

            include_once $layoutPath;
        } else {
            echo $contenido;
        }
    }

    public function load($view, $datos = [])
    {
        foreach ($datos as $key => $value) {
            $$key = $value;
        }

        ob_start();
        include_once __DIR__ . "/views/$view.php";
        $contenido = ob_get_clean();
        return $contenido;
    }

    public function printPDF($ruta)
    {
        header("Content-type: application/pdf");
        header("Content-Disposition: inline; filename=filename.pdf");
        @readfile(__DIR__ . '/storage/' . $ruta);
    }
}
