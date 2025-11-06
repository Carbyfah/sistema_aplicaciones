<?php

namespace Model;

class Documentos extends ActiveRecord
{
    protected static $tabla = 'documentos';
    public static $idTabla = 'id_documentos';
    protected static $columnasDB = [
        'documentos_nombre',
        'documentos_ruta',
        'documentos_extension',
        'documentos_tamanio',
        'documentos_version',
        'documentos_fecha_subida',
        'documentos_situacion',
        'documento_original_id',
        'ordenes_aplicaciones_id_ordenes_aplicaciones',
        'categorias_documentos_id_categorias_documentos',
        'usuarios_id_usuarios'
    ];

    // Solo PDF, máximo 50MB
    const EXTENSION_PERMITIDA = 'pdf';
    const TAMANIO_MAXIMO = 52428800; // 50MB

    public $id_documentos;
    public $documentos_nombre;
    public $documentos_ruta;
    public $documentos_extension;
    public $documentos_tamanio;
    public $documentos_version;
    public $documentos_fecha_subida;
    public $documentos_situacion;
    public $documento_original_id;
    public $ordenes_aplicaciones_id_ordenes_aplicaciones;
    public $categorias_documentos_id_categorias_documentos;
    public $usuarios_id_usuarios;

    public function __construct($args = [])
    {
        $this->id_documentos = $args['id_documentos'] ?? null;
        $this->documentos_nombre = $args['documentos_nombre'] ?? '';
        $this->documentos_ruta = $args['documentos_ruta'] ?? '';
        $this->documentos_extension = $args['documentos_extension'] ?? '';
        $this->documentos_tamanio = $args['documentos_tamanio'] ?? 0;
        $this->documentos_version = $args['documentos_version'] ?? 1;
        $this->documentos_fecha_subida = $args['documentos_fecha_subida'] ?? date('Y-m-d H:i:s');
        $this->documentos_situacion = $args['documentos_situacion'] ?? 1;
        $this->documento_original_id = $args['documento_original_id'] ?? null;
        $this->ordenes_aplicaciones_id_ordenes_aplicaciones = $args['ordenes_aplicaciones_id_ordenes_aplicaciones'] ?? null;
        $this->categorias_documentos_id_categorias_documentos = $args['categorias_documentos_id_categorias_documentos'] ?? null;
        $this->usuarios_id_usuarios = $args['usuarios_id_usuarios'] ?? null;
    }

    public function validar()
    {
        parent::validar();

        if (!$this->documentos_nombre) {
            self::setAlerta('error', 'El nombre del documento es obligatorio');
        }

        if (!$this->documentos_ruta) {
            self::setAlerta('error', 'La ruta del documento es obligatoria');
        }

        if (!$this->ordenes_aplicaciones_id_ordenes_aplicaciones) {
            self::setAlerta('error', 'El documento debe estar asociado a un proyecto');
        }

        if (!$this->categorias_documentos_id_categorias_documentos) {
            self::setAlerta('error', 'Debe seleccionar una categoría');
        }

        if (!$this->usuarios_id_usuarios) {
            self::setAlerta('error', 'Debe especificar el usuario');
        }

        // Validar extensión si se proporciona
        if ($this->documentos_extension && strtolower($this->documentos_extension) !== self::EXTENSION_PERMITIDA) {
            self::setAlerta('error', 'Solo se permiten archivos PDF');
        }

        // Validar tamaño si se proporciona
        if ($this->documentos_tamanio > self::TAMANIO_MAXIMO) {
            self::setAlerta('error', 'El archivo no puede ser mayor a 50MB');
        }

        return self::getAlertas();
    }
}
