<?php

namespace Model;

class ContenidoDocumentos extends ActiveRecord
{
    protected static $tabla = 'contenido_documentos';
    public static $idTabla = 'id_contenido_documentos';
    protected static $columnasDB = [
        'contenido_documentos_texto',
        'documentos_id_documentos'
    ];

    public $id_contenido_documentos;
    public $contenido_documentos_texto;
    public $documentos_id_documentos;

    public function __construct($args = [])
    {
        $this->id_contenido_documentos = $args['id_contenido_documentos'] ?? null;
        $this->contenido_documentos_texto = $args['contenido_documentos_texto'] ?? '';
        $this->documentos_id_documentos = $args['documentos_id_documentos'] ?? null;
    }

    public function validar()
    {
        parent::validar();

        if (!$this->documentos_id_documentos) {
            self::setAlerta('error', 'El contenido debe estar asociado a un documento');
        }

        return self::getAlertas();
    }

    public static function getContenidoDocumento($documento_id)
    {
        return static::where('documentos_id_documentos', $documento_id);
    }

    public static function buscarTexto($texto)
    {
        $query = "SELECT * FROM " . static::$tabla .
            " WHERE MATCH(contenido_documentos_texto) AGAINST(" . self::$db->quote($texto) . " IN NATURAL LANGUAGE MODE)";
        return self::consultarSQL($query);
    }
}
