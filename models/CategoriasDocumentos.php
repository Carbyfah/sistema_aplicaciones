<?php

namespace Model;

class CategoriasDocumentos extends ActiveRecord
{
    protected static $tabla = 'categorias_documentos';
    public static $idTabla = 'id_categorias_documentos';
    protected static $columnasDB = [
        'categorias_documentos_nombre',
        'categorias_documentos_descripcion',
        'categorias_documentos_situacion'
    ];

    public $id_categorias_documentos;
    public $categorias_documentos_nombre;
    public $categorias_documentos_descripcion;
    public $categorias_documentos_situacion;

    public function __construct($args = [])
    {
        $this->id_categorias_documentos = $args['id_categorias_documentos'] ?? null;
        $this->categorias_documentos_nombre = $args['categorias_documentos_nombre'] ?? '';
        $this->categorias_documentos_descripcion = $args['categorias_documentos_descripcion'] ?? '';
        $this->categorias_documentos_situacion = $args['categorias_documentos_situacion'] ?? 1;
    }

    public function validar()
    {
        parent::validar();

        if (!$this->categorias_documentos_nombre) {
            self::setAlerta('error', 'El nombre de la categoría es obligatorio');
        }

        return self::getAlertas();
    }
}
