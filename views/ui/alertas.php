<?php

/**
 * Sistema de alertas usando SweetAlert2
 * Este archivo maneja la visualización de mensajes al usuario
 */

if (!function_exists('mostrarAlertas')) {
    function mostrarAlertas($alertas)
    {
        if (empty($alertas)) {
            return '';
        }

        $script = '<script>';

        foreach ($alertas as $tipo => $mensajes) {
            foreach ($mensajes as $mensaje) {
                $icono = match ($tipo) {
                    'exito' => 'success',
                    'error' => 'error',
                    'advertencia' => 'warning',
                    'info' => 'info',
                    default => 'info'
                };

                $titulo = match ($tipo) {
                    'exito' => 'Éxito',
                    'error' => 'Error',
                    'advertencia' => 'Advertencia',
                    'info' => 'Información',
                    default => 'Notificación'
                };

                $script .= "
                    Swal.fire({
                        icon: '{$icono}',
                        title: '{$titulo}',
                        text: '{$mensaje}',
                        showConfirmButton: true,
                        timer: 3000,
                        timerProgressBar: true,
                        toast: true,
                        position: 'top-end'
                    });
                ";
            }
        }

        $script .= '</script>';

        return $script;
    }
}

// Si hay alertas en la sesión, mostrarlas
if (isset($_SESSION['alertas']) && !empty($_SESSION['alertas'])) {
    echo mostrarAlertas($_SESSION['alertas']);
    unset($_SESSION['alertas']);
}
