<?php

class Libro {
    private $id;
    private $titulo;
    private $autor; // nombre del autor
    private $categoria; // nombre de la categoria
    private $disponible;

    public function __construct($id, $titulo, $autor, $categoria, $disponible = true)
    {
        $this->id = $id;
        $this->titulo = $titulo;
        $this->autor = $autor;
        $this->categoria = $categoria;
        $this->disponible = $disponible;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTitulo()
    {
        return $this->titulo;
    }

    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;
    }

    public function getAutor()
    {
        return $this->autor;
    }

    public function setAutor($autor)
    {
        $this->autor = $autor;
    }

    public function getCategoria()
    {
        return $this->categoria;
    }

    public function setCategoria($categoria)
    {
        $this->categoria = $categoria;
    }

    public function isDisponible()
    {
        return (bool) $this->disponible;
    }

    public function setDisponible($disponible)
    {
        $this->disponible = (bool) $disponible;
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'titulo' => $this->titulo,
            'autor' => $this->autor,
            'categoria' => $this->categoria,
            'disponible' => $this->disponible
        ];
    }

    public static function fromArray(array $data)
    {
        return new self($data['id'], $data['titulo'], $data['autor'], $data['categoria'], $data['disponible']);
    }
}

?>