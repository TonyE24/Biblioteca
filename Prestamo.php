<?php

class Prestamo {
    private $id;
    private $libroId;
    private $usuario;
    private $fecha_prestamo; // timestamp o string
    private $fecha_devolucion; // nullable
    private $estado; // 'activo' | 'devuelto'

    public function __construct($id, $libroId, $usuario, $fecha_prestamo, $fecha_devolucion = null, $estado = 'activo')
    {
        $this->id = $id;
        $this->libroId = $libroId;
        $this->usuario = $usuario;
        $this->fecha_prestamo = $fecha_prestamo;
        $this->fecha_devolucion = $fecha_devolucion;
        $this->estado = $estado;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getLibroId()
    {
        return $this->libroId;
    }

    public function getUsuario()
    {
        return $this->usuario;
    }

    public function getFechaPrestamo()
    {
        return $this->fecha_prestamo;
    }

    public function getFechaDevolucion()
    {
        return $this->fecha_devolucion;
    }

    public function getEstado()
    {
        return $this->estado;
    }

    public function setEstado($estado)
    {
        $this->estado = $estado;
    }

    public function setFechaDevolucion($fecha)
    {
        $this->fecha_devolucion = $fecha;
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'libroId' => $this->libroId,
            'usuario' => $this->usuario,
            'fecha_prestamo' => $this->fecha_prestamo,
            'fecha_devolucion' => $this->fecha_devolucion,
            'estado' => $this->estado
        ];
    }

    public static function fromArray(array $data)
    {
        return new self($data['id'], $data['libroId'], $data['usuario'], $data['fecha_prestamo'], $data['fecha_devolucion'], $data['estado']);
    }
}

?>