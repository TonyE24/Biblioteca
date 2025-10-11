<?php
require_once './Biblioteca.php';
require_once './Libro.php';
require_once './Prestamo.php';

session_start();

if(!isset($_SESSION['biblioteca'])){
    $biblioteca = new Biblioteca();
    $_SESSION['biblioteca'] = $biblioteca->toArray();
} else {
    $biblioteca = Biblioteca::fromArray($_SESSION['biblioteca']);
}

// Helper para generar ID único de libro
function generarId(){
    return uniqid('lib_', true);
}

// Create
if(isset($_POST['createForm'])){
    if(isset($_POST['titulo'], $_POST['autor'], $_POST['categoria'])){
        $titulo = trim($_POST['titulo']);
        $autor = trim($_POST['autor']);
        $categoria = trim($_POST['categoria']);

        if($titulo !== '' && $autor !== ''){
            $id = generarId();
            $libro = new Libro($id, $titulo, $autor, $categoria, true);
            $biblioteca->addLibro($libro);
            $_SESSION['biblioteca'] = $biblioteca->toArray();
        }
    }
}

// Update
if(isset($_POST['updateForm'])){
    if(isset($_GET['editar']) && isset($_POST['titulo'], $_POST['autor'], $_POST['categoria'])){
        $id = $_GET['editar'];
        $titulo = trim($_POST['titulo']);
        $autor = trim($_POST['autor']);
        $categoria = trim($_POST['categoria']);
        $biblioteca->updateLibro($id, $titulo, $autor, $categoria);
        $_SESSION['biblioteca'] = $biblioteca->toArray();
        header('Location: ' . $_SERVER['PHP_SELF']); exit;
    }
}

// Delete (manteniendo la misma estructura que el proyecto aerolinea)
if(isset($_GET['eliminar'])){
    $id = $_GET['eliminar'];
    $biblioteca->deleteLibro($id);
    $_SESSION['biblioteca'] = $biblioteca->toArray();
    header('Location: ' . $_SERVER['PHP_SELF']); exit;
}

// Solicitar prestamo
if(isset($_POST['solicitarPrestamo'])){
    if(isset($_POST['libroId'], $_POST['usuario'])){
        $libroId = $_POST['libroId'];
        $usuario = trim($_POST['usuario']);
        $prestamo = $biblioteca->solicitarPrestamo($libroId, $usuario);
        if($prestamo){
            $_SESSION['biblioteca'] = $biblioteca->toArray();
        }
    }
}

// Devolver prestamo
if(isset($_GET['devolver'])){
    $prestamoId = $_GET['devolver'];
    $biblioteca->devolverPrestamo($prestamoId);
    $_SESSION['biblioteca'] = $biblioteca->toArray();
    header('Location: ' . $_SERVER['PHP_SELF']); exit;
}

// Busqueda
$resultados = $biblioteca->getLibros();
if(isset($_GET['q']) && isset($_GET['by'])){
    $q = trim($_GET['q']);
    $by = $_GET['by'];
    if($q !== ''){
        if($by === 'titulo') $resultados = $biblioteca->buscarPorTitulo($q);
        if($by === 'autor') $resultados = $biblioteca->buscarPorAutor($q);
        if($by === 'categoria') $resultados = $biblioteca->buscarPorCategoria($q);
    }
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biblioteca - CRUD OOP</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
    <header class="header">
        <div class="brand">
            <div class="logo">B</div>
            <div>
                <h1>Gestión de Biblioteca</h1>
            </div>
        </div>
        <form class="search" method="GET">
            <input type="text" name="q" placeholder="Buscar..." value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q'], ENT_QUOTES, 'UTF-8') : '' ?>">
            <select name="by">
                <option value="titulo">Título</option>
                <option value="autor">Autor</option>
                <option value="categoria">Categoría</option>
            </select>
            <button type="submit" class="btn primary">Buscar</button>
        </form>
    </header>

    <!-- espacio reservado para búsqueda (migrado al header) -->

    <?php
    // Edit / Create form
    if(isset($_GET['editar'])){
        $libroEditable = $biblioteca->obtenerLibroPorId($_GET['editar']);
        if($libroEditable){
    ?>
    <div class="grid">
      <div class="card">
        <h3>Editar libro</h3>
        <form action="?editar=<?php echo htmlspecialchars($libroEditable->getId(), ENT_QUOTES, 'UTF-8'); ?>" method="POST">
            <input type="hidden" name="updateForm" value="1">
            <div class="form-row">
                <label>Título:</label>
                <input type="text" name="titulo" value="<?php echo htmlspecialchars($libroEditable->getTitulo(), ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>
            <div class="form-row">
                <label>Autor:</label>
                <input type="text" name="autor" value="<?php echo htmlspecialchars($libroEditable->getAutor(), ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>
            <div class="form-row">
                <label>Categoría:</label>
                <input type="text" name="categoria" value="<?php echo htmlspecialchars($libroEditable->getCategoria(), ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="form-actions">
                <button type="submit" class="btn primary">Guardar</button>
                <a class="btn ghost" href="<?php echo $_SERVER['PHP_SELF']; ?>">Cancelar</a>
            </div>
        </form>
      </div>
    <?php
        } else {
            echo "<p>Libro no encontrado</p>";
        }
    } else {
    ?>
    <div class="grid">
      <div class="card">
        <h3>Agregar libro</h3>
        <form action="" method="POST">
            <input type="hidden" name="createForm" value="1">
            <div class="form-row">
                <label>Título:</label>
                <input type="text" name="titulo" required>
            </div>
            <div class="form-row">
                <label>Autor:</label>
                <input type="text" name="autor" required>
            </div>
            <div class="form-row">
                <label>Categoría:</label>
                <input type="text" name="categoria">
            </div>
            <div class="form-actions">
                <button type="submit" class="btn primary">Agregar</button>
            </div>
        </form>
      </div>
    <?php } ?>

      <div class="card">
        <main>
            <h3>Libros</h3>
            <div class="table-responsive">
            <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Autor</th>
                    <th>Categoría</th>
                    <th>Disponible</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($resultados as $libro): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($libro->getId(), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($libro->getTitulo(), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($libro->getAutor(), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($libro->getCategoria(), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td>
                            <?php if($libro->isDisponible()): ?>
                                <span class="tag available">Sí</span>
                            <?php else: ?>
                                <span class="tag unavailable">No</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a class="btn ghost" href="?editar=<?php echo urlencode($libro->getId()); ?>">Editar</a>
                            <a class="btn ghost" href="?eliminar=<?php echo urlencode($libro->getId()); ?>" onclick="return confirm('¿Eliminar libro?');">Eliminar</a>
                            <?php if($libro->isDisponible()): ?>
                                <form class="inline" method="POST" style="display:inline-block;margin-left:6px">
                                    <input type="hidden" name="solicitarPrestamo" value="1">
                                    <input type="hidden" name="libroId" value="<?php echo htmlspecialchars($libro->getId(), ENT_QUOTES, 'UTF-8'); ?>">
                                    <input type="text" name="usuario" placeholder="Usuario" required>
                                    <button type="submit" class="btn primary">Solicitar</button>
                                </form>
                            <?php else: ?>
                                <em class="muted">Prestado</em>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        
    <h3>Préstamos</h3>
    <div class="table-responsive">
    <table>
            <thead>
                <tr><th>ID</th><th>Libro ID</th><th>Usuario</th><th>Fecha préstamo</th><th>Estado</th><th>Acción</th></tr>
            </thead>
            <tbody>
                <?php foreach($biblioteca->getPrestamos() as $p): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($p->getId(), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($p->getLibroId(), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($p->getUsuario(), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($p->getFechaPrestamo(), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($p->getEstado(), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td>
                            <?php if($p->getEstado() === 'activo'): ?>
                                <a class="btn ghost" href="?devolver=<?php echo urlencode($p->getId()); ?>">Marcar devolución</a>
                            <?php else: ?>
                                <span>Devuelto</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>

        </main>
      </div>
    </div> <!-- fin grid -->
    </div> <!-- fin container -->
</body>
</html>