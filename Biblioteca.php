<?php

require_once __DIR__ . '/Libro.php';
require_once __DIR__ . '/Prestamo.php';

class Biblioteca {
    private $libros = []; // array of Libro
    private $prestamos = []; // array of Prestamo

    public function __construct(array $libros = [], array $prestamos = [])
    {
        $this->libros = $libros;
        $this->prestamos = $prestamos;
    }

    public function addLibro(Libro $libro)
    {
        $this->libros[] = $libro;
    }

    public function updateLibro($id, $titulo, $autor, $categoria)
    {
        foreach($this->libros as $libro){
            if($libro->getId() == $id){
                $libro->setTitulo($titulo);
                $libro->setAutor($autor);
                $libro->setCategoria($categoria);
                return true;
            }
        }
        return false;
    }

    public function deleteLibro($id)
    {
        foreach($this->libros as $idx => $libro){
            if($libro->getId() == $id){
                unset($this->libros[$idx]);
                $this->libros = array_values($this->libros);
                return true;
            }
        }
        return false;
    }

    public function buscarPorTitulo($termino)
    {
        $res = [];
        foreach($this->libros as $libro){
            if(stripos($libro->getTitulo(), $termino) !== false){
                $res[] = $libro;
            }
        }
        return $res;
    }

    public function buscarPorAutor($termino)
    {
        $res = [];
        foreach($this->libros as $libro){
            if(stripos($libro->getAutor(), $termino) !== false){
                $res[] = $libro;
            }
        }
        return $res;
    }

    public function buscarPorCategoria($termino)
    {
        $res = [];
        foreach($this->libros as $libro){
            if(stripos($libro->getCategoria(), $termino) !== false){
                $res[] = $libro;
            }
        }
        return $res;
    }

    public function obtenerLibroPorId($id)
    {
        foreach($this->libros as $libro){
            if($libro->getId() == $id) return $libro;
        }
        return null;
    }

    public function solicitarPrestamo($libroId, $usuario)
    {
        $libro = $this->obtenerLibroPorId($libroId);
        if(!$libro) return false;
        if(!$libro->isDisponible()) return false;

        $libro->setDisponible(false);
        $id = uniqid('prest_', true);
        $fecha = date('Y-m-d H:i:s');
        $prestamo = new Prestamo($id, $libroId, $usuario, $fecha);
        $this->prestamos[] = $prestamo;
        return $prestamo;
    }

    public function devolverPrestamo($prestamoId)
    {
        foreach($this->prestamos as $prestamo){
            if($prestamo->getId() == $prestamoId && $prestamo->getEstado() === 'activo'){
                $prestamo->setEstado('devuelto');
                $fecha = date('Y-m-d H:i:s');
                $prestamo->setFechaDevolucion($fecha);
                // marcar libro disponible
                $libro = $this->obtenerLibroPorId($prestamo->getLibroId());
                if($libro) $libro->setDisponible(true);
                return true;
            }
        }
        return false;
    }

    public function toArray()
    {
        $librosArr = [];
        foreach($this->libros as $libro){
            $librosArr[] = $libro->toArray();
        }
        $prestamosArr = [];
        foreach($this->prestamos as $p){
            $prestamosArr[] = $p->toArray();
        }
        return ['libros' => $librosArr, 'prestamos' => $prestamosArr];
    }

    public static function fromArray(array $data)
    {
        $libros = [];
        foreach($data['libros'] as $l){
            $libros[] = Libro::fromArray($l);
        }
        $prestamos = [];
        if(isset($data['prestamos'])){
            foreach($data['prestamos'] as $p){
                $prestamos[] = Prestamo::fromArray($p);
            }
        }
        return new self($libros, $prestamos);
    }

    public function getLibros()
    {
        return $this->libros;
    }

    public function getPrestamos()
    {
        return $this->prestamos;
    }
}

?>