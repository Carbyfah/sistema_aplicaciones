<?php

namespace Controllers;

use Exception;

class UploadController
{
    public static function uploadAPI()
    {
        getHeadersApi();

        try {
            // DEBUG: Ver qué llegó
            error_log("FILES recibidos: " . print_r($_FILES, true));
            error_log("POST size: " . $_SERVER['CONTENT_LENGTH']);

            if (!isset($_FILES['archivo'])) {
                http_response_code(400);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'No se recibió el archivo'
                ]);
                return;
            }

            $archivo = $_FILES['archivo'];

            // Mostrar el error específico
            if ($archivo['error'] !== UPLOAD_ERR_OK) {
                $errores = [
                    UPLOAD_ERR_INI_SIZE => 'El archivo excede upload_max_filesize en php.ini',
                    UPLOAD_ERR_FORM_SIZE => 'El archivo excede MAX_FILE_SIZE',
                    UPLOAD_ERR_PARTIAL => 'El archivo se subió parcialmente',
                    UPLOAD_ERR_NO_FILE => 'No se subió ningún archivo',
                    UPLOAD_ERR_NO_TMP_DIR => 'Falta carpeta temporal',
                    UPLOAD_ERR_CANT_WRITE => 'Error al escribir en disco',
                    UPLOAD_ERR_EXTENSION => 'Extensión de PHP detuvo la subida'
                ];

                http_response_code(400);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Error al subir: ' . ($errores[$archivo['error']] ?? 'Error desconocido'),
                    'codigo_error' => $archivo['error']
                ]);
                return;
            }

            $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));

            $extensiones_permitidas = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'zip'];

            if (!in_array($extension, $extensiones_permitidas)) {
                http_response_code(400);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Tipo de archivo no permitido'
                ]);
                return;
            }

            $anio = date('Y');
            $mes = date('m');
            $carpeta = __DIR__ . "/../public/uploads/documentos/{$anio}/{$mes}";

            if (!is_dir($carpeta)) {
                mkdir($carpeta, 0777, true);
            }

            $nombre_unico = time() . '_' . uniqid() . '.' . $extension;
            $ruta_completa = $carpeta . '/' . $nombre_unico;

            if (move_uploaded_file($archivo['tmp_name'], $ruta_completa)) {
                $ruta_relativa = "/uploads/documentos/{$anio}/{$mes}/{$nombre_unico}";

                http_response_code(200);
                echo json_encode([
                    'exito' => true,
                    'mensaje' => 'Archivo subido correctamente',
                    'ruta' => $ruta_relativa,
                    'nombre_original' => $archivo['name'],
                    'tamano' => $archivo['size'],
                    'extension' => $extension
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Error al guardar el archivo'
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error al subir archivo',
                'detalle' => $e->getMessage()
            ]);
        }
    }
}
