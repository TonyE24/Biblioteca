<?php

class Categoria {
    private $id;
    private $nombre;

    public function __construct($id, $nombre)
    {
        $this->id = $id;
        $this->nombre = $nombre;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getNombre()
    {
        return $this->nombre;
    }

    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    public function toArray()
    {
        return ['id' => $this->id, 'nombre' => $this->nombre];
    }

    public static function fromArray(array $data)
    {
        return new self($data['id'], $data['nombre']);
    }
}

?>