<?php
require_once '../config/database.php';
require_once '../src/controller/createUser.php';
require_once '../src/controller/updateUser.php';
require_once '../src/controller/deleteUser.php';


$message = '';

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {

    $id_a_eliminar = $_GET['id'];

    if (eliminarUsuario($conn, $id_a_eliminar)) {
        $message = "Usuario eliminado exitosamente";
    } else {
        $message = "Error al eliminar el usuario";
    }

    // Limpiamos la URL para que no se re-elimine al recargar
    header("Location: index.php");
    exit;
}


if (isset($_POST['save'])) {

    // Recoge todos los datos
    $id = $_POST['id'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $name= $_POST['name'] ?? '';
    $lastName= $_POST['lastname'] ?? '';
    $birthday= $_POST['birthday'] ?? '';
    $city= $_POST['city'] ?? '';
    $typeUser= $_POST['typeUser'] ?? '';

    // Decide si crear o actualizar
    if (!empty($id)) {
        // Si hay un ID, actualizamos
        $message = actualizarUsuario($conn, $id, $username, $password, $name, $lastName, $birthday, $city, $typeUser);
    } else {
        // Si no hay ID, creamos
        $message = crearUsuario($conn, $username, $password, $name, $lastName, $birthday, $city, $typeUser);
    }

    // Resetea el formulario POST si la operación fue exitosa
    if ($message === "Usuario creado exitosamente" || $message === "Usuario actualizado exitosamente") {
        $_POST = [];
    }
}

elseif (isset($_POST['delete'])) {
  $id_delete = $_POST['DELETE'] ?? null;
  if ($id_delete) {
    $stmt = $pdo ->prepare(("DELETE FROM specialty WHERE id=?"));
    $success = $stmt->execute([$id_delete]);
    $message = $success ? "Especialidad Eliminada" : "Error al Eliminar";
  }
}

// Eliminar usuario



$citie = $conn->query("SELECT id, name FROM cities")->fetchAll();
$typeUsers = $conn->query("SELECT id, name FROM typeusers")->fetchAll();
$allUsers = $conn->query("SELECT u.id, u.username, u.name, u.lastname, u.birthdate,
       c.name AS city_name,
       tu.name AS type_user_name,
       u.idCity,
       u.idTypeUser
FROM users u
LEFT JOIN cities c ON u.idCity = c.id
LEFT JOIN typeusers tu ON u.idTypeUser = tu.id")->fetchAll();



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
</script>
</head>

<!-- [<nav class="navbar d-flex justify-content-center" style="background-color: #6063ffff">
      <ul class="nav">
    <li class="nav-item">
      <a href="../public/index.php" class="nav-link active text-light" aria-current="page">Usuarios</a>
    </li>
    <li class="nav-item">
      <a href="../public/crudSpecialty.php" class="nav-link active text-light" aria-current="page">Especialidades</a>
    </li>
  </ul> 
</nav>] -->



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
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="index.php" class="brand-link">
      <i class="fas fa-cogs"></i>
      <span class="brand-text font-weight-light">Gestión Empleados</span>
    </a>
    <div class="sidebar">
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
          <li class="nav-item">
            <a href="index.php" class="nav-link active">
              <i class="nav-icon fas fa-users"></i>
              <p>Usuarios</p>
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
        <h1>Gestión de Usuarios</h1>
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
        <form method="POST" id="formEmpleado" action="">
          <input type="hidden" name="id" id="id" />
          <div class="form-group mb-2">
            <input type="text" name="username" id="username" placeholder="Usuario" required class="form-control" />
          </div>
          <div class="form-group mb-2">
            <input type="password" name="password" id="password" placeholder="Contraseña" required class="form-control" />
          </div>
          <div class="form-group mb-2">
            <input type="text" name="name" id="name" placeholder="Nombre" class="form-control" />
          </div>
          </div>
          <div class="form-group mb-2">
            <input type="text" name="lastname" id="lastname" placeholder="Apellido" class="form-control" />
          </div>
          <div class="form-group mb-2">
            <select name="typeUser" id="typeUser" class="form-control" required>
              <option value="">Seleccione el tipo de usuario</option>
              <?php foreach ($typeUsers as $tuser): ?>
                <option value="<?= $tuser['id'] ?>"><?= htmlspecialchars($tuser['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group mb-2">
            <select name="city" id="city" class="form-control" required>
              <option value="">Seleccione la ciudad</option>
              <?php foreach ($citie as $c): ?>
                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <button type="submit" name="save" class="btn btn-primary w-100">Guardar</button>
          <button type="button" onclick="limpiarFormulario()" class="btn btn-secondary w-100 mt-2">Limpiar</button>
        </form>


        <!-- Tabla -->
        <table class="table table-bordered table-hover mt-4">
          <thead class="thead-light">
            <tr>
              <th>usuario</th><th>nombre</th><th>Apellido</th><th>Ciudad</th><th>Tipo de usuario</th><th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($allUsers as $aUsers): ?>
              <tr>
                <td><?= htmlspecialchars($aUsers['username']) ?></td>
                <td><?= htmlspecialchars($aUsers['name']) ?></td>
                <td><?= htmlspecialchars($aUsers['lastname']) ?></td>
                <td><?= htmlspecialchars($aUsers['city_name']) ?></td>
                <td><?= htmlspecialchars($aUsers['type_user_name']) ?></td>
                <td>
                  <button class="btn btn-warning btn-sm" onclick="editarUsuario(
              '<?= $aUsers['id']?>',
              '<?= htmlspecialchars(addslashes($aUsers['username'])) ?>',
              '<?= htmlspecialchars(addslashes($aUsers['name'])) ?>',
              '<?= htmlspecialchars(addslashes($aUsers['lastname'])) ?>',
              '<?= htmlspecialchars(addslashes($aUsers['birthdate'])) ?>',
              '<?= $aUsers['idCity'] ?>',
              '<?= $aUsers['idTypeUser'] ?>'
              )">
              Editar
              </button>
                  <form method="POST" action="" style="display:inline-block;" onsubmit="return confirm('¿Seguro que deseas eliminar este empleado?');">
                    <input type="hidden" name="id_delete" value="<?= $aUsers['id'] ?>" />
                    <button type="submit" name="delete" class="btn btn-danger btn-sm">Eliminar</button>
                  </form>
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