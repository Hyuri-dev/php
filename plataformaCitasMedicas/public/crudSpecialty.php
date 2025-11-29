<?php
session_start();
// Verificacion de sesión
if (!isset($_SESSION['logueado']) || $_SESSION['logueado'] !== true) {
    header("Location: login.php");
    exit;
}

require_once '../config/database.php';
require_once '../src/controller/createSpecialty.php';
require_once '../src/controller/updateSpecialty.php';
require_once '../src/controller/deleteSpecialty.php';

$message ='';

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {

    $id_a_eliminar = $_GET['id'];

    if (eliminarEspecialidad($conn , $id_a_eliminar)) {
        $message = "Especialidad eliminada exitosamente";
    } else {
        $message = "Error al eliminar la especialidad";
    }

    // Limpiamos la URL para que no se re-elimine al recargar
    header("Location: crudSpecialty.php");
    exit;
}

if (isset($_POST['save'])) {

    // Recoge todos los datos
    $id = $_POST['id'] ?? '';
    $specialty = $_POST['name'] ?? '';

    // Decide si crear o actualizar
    if (!empty($id)) {
        // Si hay un ID, actualizamos
        $message = actualizarEspecialidad($conn, $id, $specialty);
    } else {
        // Si no hay ID, creamos
        $message = crearEspecialidad($conn,$specialty);
    }

    // Resetea el formulario POST si la operación fue exitosa
    if ($message === "Especialidad creada exitosamente" || $message === "Especialidad actualizado exitosamente") {
        $_POST = [];
    }
}
$allEspecialty = $conn->query("SELECT * FROM specialty")
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestión de usuarios</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css" />
<script>
  function editarUsuario (id, username, name, lastname, date, city, typeUser){
    document.getElementById('id').value = id; 
    document.getElementById('username').value = username;
    document.getElementById('name').value = name;
    document.getElementById('lastname').value = lastname;
    document.getElementById('date').value = date;
    document.getElementById('city').value = city;
    document.getElementById('typeUser').value = typeUser;
  }

  function limpiarFormulario() {
      document.getElementById('formUsers').reset(); 
      document.getElementById('id').value = ""; 
  }
  // Temporizador de notificacion
  setTimeout(() => {
    const alert = document.querySelector('.alert');
    if (alert) {
        alert.style.transition = "opacity 0.5s ease";
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 500);
    }
}, 4500);
</script>
</head>

<body class="hold-transition sidebar-mini">
<div class="wrapper">


  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="#" class="nav-link">Inicio</a>
      </li>
    </ul>
    <ul class="navbar-nav ml-auto">
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
          <i class="far fa-user"></i> <?=htmlspecialchars($_SESSION['username'])?> <i class="fas fa-caret-down ml-1"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-right">
          <a href="logout.php" class="dropdown-item">
            <i class="fas fa-sign-out-alt"></i> Cerrar sesión
          </a>
        </div>
      </li>
    </ul>
  </nav>


  <!-- Sidebar -->
  <aside class="main-sidebar sidebar-#fff elevation-4">
    <a href="index.php" class="brand-link">
      <i class="fas fa-cogs"></i>
      <span class="brand-text font-weight-light">Gestión Citas Medicas</span>
    </a>
    <div class="sidebar">
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
          <li class="nav-item">
            <a href="index.php" class="nav-link active">
              <i class="nav-icon fas fa-users"></i>
              <p>Usuarios</p>
            </a>
            <a href="crudSpecialty.php" class="nav-link active">
              <i class="nav-icon fas fa-users"></i>
              <p>Especialidad</p>
            </a>
            <a href="crudSpecialty.php" class="nav-link active">
              <i class="nav-icon fas fa-users"></i>
              <p>Estados de las citas</p>
            </a>
            <!-- Pendiente por crear el apartado de las citas -->
            <a href="crudSpecialty.php" class="nav-link active">
              <i class="nav-icon fas fa-users"></i>
              <p>Citas</p>
            </a>
            
          </li>
        </ul>
      </nav>
    </div>
  </aside>


  <!-- Content Wrapper -->
  <div class="content-wrapper">
    <section class="content-header">
      <div class="container-fluid">
        <h1>Gestión de Especialidades</h1>
      </div>
    </section>
    <section class="content">
      <div class="container-fluid">


        <?php if ($message): ?>
          <div class="alert alert-info alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($message) ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
        <?php endif; ?>


        <!-- Formulario -->
        <form method= "POST" id="specialtyForm" action="" class="mb-4">
          <input type="hidden" name="id" id="id"> 
          <input type="text" name="name" id="name" placeholder="Especialidad" required class="form-control mb-2"> <br><br>
            <button type="submit" name="save" class="btn btn-primary w-100" onclick="editarEspecialidad">Guardar</button>
            <button type="button" onclick="limpiarFormulario()" class="btn btn-secondary w-100 mt-2">Limpiar</button> 


        <!-- Tabla -->
        <table class="table table-bordered table-hover mt-4">
          <thead class="thead-light">
            <tr>
              <th>Especialidad</th><th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($allEspecialty as $aEspecialty): ?>
              <tr>
              <td> <?= htmlspecialchars($aEspecialty['nombre'] ?? '') ?></td>
              
              <td> 
                  <button 
                    type="button" 
                    class="btn btn-sm btn-warning" 
                    onclick="editarEspecialidad(<?= $aEspecialty['id'] ?>, '<?= htmlspecialchars($aEspecialty['nombre'] ?? '') ?>')">
                      Editar
                  </button>
                                  <a href="crudSpecialty.php?action=delete&id=<?= $aEspecialty['id'] ?>" 
                 class="btn btn-danger btn-sm" 
                 onclick="return confirm('¿Estás seguro de que quieres eliminar a este usuario?');">
                 Eliminar
              </a>
                  </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>


      </div>
    </section>
  </div>


  <!-- Footer -->
  <footer class="main-footer text-center">
    <strong>&copy; 2025 ServiCare</strong> Todos los derechos reservados.
  </footer>


</div>


<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>


</body>
</html>