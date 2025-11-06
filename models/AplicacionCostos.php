<?php

namespace Model;

class AplicacionCostos extends ActiveRecord
{
    protected static $tabla = 'aplicacion_costos';
    public static $idTabla = 'id_aplicacion_costos';
    protected static $columnasDB = [
        'aplicacion_id_aplicacion',
        'complejidad_id',
        'seguridad_id',
        'costos_horas_estimadas',
        'costos_tarifa_hora',
        'costos_total',
        'costos_moneda',
        'costos_notas',
        'costos_situacion',
        'fecha_creacion',
        'fecha_modificacion',
        'creado_por',
        'modificado_por'
    ];

    public $id_aplicacion_costos;
    public $aplicacion_id_aplicacion;
    public $complejidad_id;
    public $seguridad_id;
    public $costos_horas_estimadas;
    public $costos_tarifa_hora;
    public $costos_total;
    public $costos_moneda;
    public $costos_notas;
    public $costos_situacion;
    public $fecha_creacion;
    public $fecha_modificacion;
    public $creado_por;
    public $modificado_por;

    public function __construct($args = [])
    {
        $this->id_aplicacion_costos = $args['id_aplicacion_costos'] ?? null;
        $this->aplicacion_id_aplicacion = $args['aplicacion_id_aplicacion'] ?? null;
        $this->complejidad_id = $args['complejidad_id'] ?? null;
        $this->seguridad_id = $args['seguridad_id'] ?? null;
        $this->costos_horas_estimadas = $args['costos_horas_estimadas'] ?? 0;
        $this->costos_tarifa_hora = $args['costos_tarifa_hora'] ?? 0;
        $this->costos_total = $args['costos_total'] ?? 0;
        $this->costos_moneda = $args['costos_moneda'] ?? 'USD';
        $this->costos_notas = $args['costos_notas'] ?? '';
        $this->costos_situacion = $args['costos_situacion'] ?? 1;
        $this->fecha_creacion = $args['fecha_creacion'] ?? date('Y-m-d H:i:s');
        $this->fecha_modificacion = $args['fecha_modificacion'] ?? null;
        $this->creado_por = $args['creado_por'] ?? $_SESSION['usuario_id'] ?? null;
        $this->modificado_por = $args['modificado_por'] ?? null;
    }

    public function validar()
    {
        parent::validar();

        if (!$this->aplicacion_id_aplicacion) {
            self::setAlerta('error', 'La aplicación es obligatoria');
        }

        if (!$this->costos_horas_estimadas || $this->costos_horas_estimadas <= 0) {
            self::setAlerta('error', 'Las horas estimadas deben ser mayor a 0');
        }

        if (!$this->costos_tarifa_hora || $this->costos_tarifa_hora <= 0) {
            self::setAlerta('error', 'La tarifa por hora debe ser mayor a 0');
        }

        return self::getAlertas();
    }
}
