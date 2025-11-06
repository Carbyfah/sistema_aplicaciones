<?php

namespace Model;

use PDO;

class ActiveRecord
{

    protected static $db;
    protected static $tabla = '';
    protected static $columnasDB = [];
    protected static $idTabla = '';

    protected static $alertas = [];

    public static function setDB($database)
    {
        self::$db = $database;
    }

    public static function setAlerta($tipo, $mensaje)
    {
        static::$alertas[$tipo][] = $mensaje;
    }

    public static function getAlertas()
    {
        return static::$alertas;
    }

    public function validar()
    {
        static::$alertas = [];
        return static::$alertas;
    }

    public function guardar()
    {
        $resultado = '';
        $id = static::$idTabla ?? 'id';

        if (!empty($this->$id)) {
            $resultado = $this->actualizar();
        } else {
            $resultado = $this->crear();
        }
        return $resultado;
    }

    public static function all()
    {
        $query = "SELECT * FROM " . static::$tabla;
        $resultado = self::consultarSQL($query);
        return $resultado;
    }

    public static function find($id = [])
    {
        $idQuery = static::$idTabla ?? 'id';
        $query = "SELECT * FROM " . static::$tabla;

        if (is_array(static::$idTabla)) {
            /** @var array $idArray */
            $idArray = static::$idTabla;
            $primer_id = reset($idArray);

            foreach ($idArray as $key => $value) {
                if ($value == $primer_id) {
                    $query .= " WHERE $value = " . self::$db->quote($id[$value]);
                } else {
                    $query .= " AND $value = " . self::$db->quote($id[$value]);
                }
            }
        } else {
            $query .= " WHERE $idQuery = " . self::$db->quote($id);
        }

        $resultado = self::consultarSQL($query);
        return array_shift($resultado);
    }

    public static function get($limite)
    {
        $query = "SELECT * FROM " . static::$tabla . " LIMIT {$limite}";
        $resultado = self::consultarSQL($query);
        return array_shift($resultado);
    }

    public static function where($columna, $valor, $condicion = '=')
    {
        $query = "SELECT * FROM " . static::$tabla . " WHERE {$columna} {$condicion} '{$valor}'";
        $resultado = self::consultarSQL($query);
        return  $resultado;
    }

    public static function quote($value)
    {
        return self::$db->quote($value);
    }

    public static function SQL($consulta)
    {
        $query = trim($consulta);

        if (stripos($query, 'SELECT') === 0) {
            return self::$db->query($query);
        }

        $stmt = self::$db->query($query);
        return $stmt->rowCount();
    }
    public function crear()
    {
        $atributos = $this->sanitizarAtributos();

        $query = " INSERT INTO " . static::$tabla . " ( ";
        $query .= join(', ', array_keys($atributos));
        $query .= " ) VALUES (";
        $query .= join(", ", array_values($atributos));
        $query .= " ) ";

        $stmt = self::$db->query($query);

        return [
            'resultado' =>  $stmt->rowCount(),
            'id' => self::$db->lastInsertId(static::$tabla)
        ];
    }

    public function actualizar()
    {
        $atributos = $this->sanitizarAtributos();

        $valores = [];
        foreach ($atributos as $key => $value) {
            $valores[] = "{$key}={$value}";
        }
        $id = static::$idTabla ?? 'id';

        $query = "UPDATE " . static::$tabla . " SET ";
        $query .=  join(', ', $valores);

        if (is_array(static::$idTabla)) {
            /** @var array $idArray */
            $idArray = static::$idTabla;
            $primer_id = reset($idArray);

            foreach ($idArray as $key => $value) {
                if ($value == $primer_id) {
                    $query .= " WHERE $value = " . self::$db->quote($this->$value);
                } else {
                    $query .= " AND $value = " . self::$db->quote($this->$value);
                }
            }
        } else {
            $query .= " WHERE " . $id . " = " . self::$db->quote($this->$id) . " ";
        }

        $stmt = self::$db->query($query);
        return [
            'resultado' =>  $stmt->rowCount(),
        ];
    }

    public function eliminar()
    {
        $idQuery = static::$idTabla ?? 'id';
        $query = "DELETE FROM "  . static::$tabla . " WHERE $idQuery = " . self::$db->quote($this->$idQuery);
        $stmt = self::$db->query($query);
        return $stmt->rowCount();
    }

    public static function consultarSQL($query)
    {
        $resultado = self::$db->query($query);

        $array = [];
        while ($registro = $resultado->fetch(PDO::FETCH_ASSOC)) {
            $array[] = static::crearObjeto($registro);
        }

        $resultado->closeCursor();

        return $array;
    }

    public static function fetchArray($query)
    {
        $resultado = self::$db->query($query);
        $respuesta = $resultado->fetchAll(PDO::FETCH_ASSOC);
        $data = [];
        foreach ($respuesta as $value) {
            $convertido = array_map(function ($val) {
                if (is_numeric($val)) {
                    return strpos($val, '.') !== false ? floatval($val) : intval($val);
                }
                return $val;
            }, $value);
            $data[] = array_change_key_case($convertido);
        }
        $resultado->closeCursor();
        return $data;
    }

    public static function fetchFirst($query)
    {
        $resultado = self::$db->query($query);
        $respuesta = $resultado->fetchAll(PDO::FETCH_ASSOC);
        $data = [];
        foreach ($respuesta as $value) {
            $convertido = array_map(function ($val) {
                if (is_numeric($val)) {
                    return strpos($val, '.') !== false ? floatval($val) : intval($val);
                }
                return $val;
            }, $value);
            $data[] = array_change_key_case($convertido);
        }
        $resultado->closeCursor();
        return array_shift($data);
    }

    protected static function crearObjeto($registro)
    {
        $objeto = new static;

        foreach ($registro as $key => $value) {
            $key = strtolower($key);
            if (property_exists($objeto, $key)) {
                $objeto->$key = $value;
            }
        }

        return $objeto;
    }

    public function atributos()
    {
        $atributos = [];
        foreach (static::$columnasDB as $columna) {
            $columna = strtolower($columna);
            if ($columna === 'id' || $columna === static::$idTabla) continue;
            $atributos[$columna] = $this->$columna;
        }
        return $atributos;
    }

    public function sanitizarAtributos()
    {
        $atributos = $this->atributos();
        $sanitizado = [];
        foreach ($atributos as $key => $value) {
            if (is_null($value) || $value === '') {
                $sanitizado[$key] = 'NULL';
            } else {
                if (is_string($value)) {
                    $value = str_replace("'", "''", $value);
                    $sanitizado[$key] = "'{$value}'";
                } else {
                    $sanitizado[$key] = self::$db->quote($value);
                }
            }
        }
        return $sanitizado;
    }

    public function sincronizar($args = [])
    {
        foreach ($args as $key => $value) {
            if (property_exists($this, $key) && !is_null($value)) {
                $this->$key = $value;
            }
        }
    }
}
