<?php

namespace julio101290\boilerplate\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\Router\Router;
use CodeIgniter\Exceptions\PageNotFoundException;

class ErrorController extends BaseController
{
    public function index()
    {
        helper('auth');
        helper('menu');
        // 1. CASO: Falta de permisos (detectado por el PermissionFilter)
        if ($permError = session()->getFlashdata('permission_error')) {
            return view('julio101290\boilerplate\Views\error404', [
                'tipo_error' => 'permisos',
                'mensaje'    => $permError['message'],
                'permiso'    => $permError['permission'],
            ]);
        }

        // Obtener la URI que falló
        $uri = uri_string();
        if ($uri === 'admin/error404' || empty($uri)) {
            $prev = previous_url();
            $uri = $prev ? trim(str_replace(base_url(), '', $prev), '/') : '';
        }

        $routesCollection  = service('routes');
        $request           = service('request');
        $router            = new Router($routesCollection, $request);

        $rutaExiste        = false;
        $controladorExiste = false;
        $metodoExiste      = false;
        $controladorNombre = '';
        $metodoNombre      = '';

        try {
            $router->handle($uri);
            $rutaExiste        = true;
            $controladorNombre = $router->controllerName();
            $metodoNombre      = $router->methodName();

            if (class_exists($controladorNombre)) {
                $controladorExiste = true;
                if (method_exists($controladorNombre, $metodoNombre)) {
                    $metodoExiste = true;
                }
            }
        } catch (PageNotFoundException $e) {
            $rutaExiste = false;
        } catch (\Throwable $t) {
            $rutaExiste = false;
        }

        // Clasificación de los errores restantes
        if (!$rutaExiste) {
            // 2. CASO: La ruta no existe en Routes.php
            $tipoError = 'ruta_no_encontrada';
        } elseif (!$controladorExiste) {
            // 3A. CASO: La ruta existe, pero la clase del controlador no existe
            $tipoError = 'controlador_faltante';
        } elseif (!$metodoExiste) {
            // 3B. CASO: El controlador existe, pero la función/método no existe
            $tipoError = 'metodo_faltante';
        } else {
            $tipoError = 'recurso_no_encontrado';
        }

        return view('julio101290\boilerplate\Views\error404', [
            'tipo_error'  => $tipoError,
            'uri'         => $uri,
            'controlador' => $controladorNombre,
            'metodo'      => $metodoNombre,
        ]);
    }
}