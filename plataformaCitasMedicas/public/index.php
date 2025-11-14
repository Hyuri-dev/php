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
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
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

<nav class="navbar d-flex justify-content-center" style="background-color: #6063ffff">
      <ul class="nav">
    <li class="nav-item">
      <a href="../public/index.php" class="nav-link active text-light" aria-current="page">Usuarios</a>
    </li>
    <li class="nav-item">
      <a href="../public/crudSpecialty.php" class="nav-link active text-light" aria-current="page">Especialidades</a>
    </li>
  </ul> 
</nav>



<body>
  <div class="container mt-4">
    <h2>Gestión de Usuarios</h2>
    
    <?php if (!empty($message)): ?>
        <p class="alert alert-info"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    
    <form method= "POST" id="formUsers" action="" class="mb-4">
      <input type="hidden" name="id" id="id"> 
      <input type="text" name="username" id="username" placeholder="Usuario" required class="form-control mb-2"> <br><br>
      <input type="password" name="password" id="password" placeholder="Contraseña" required class="form-control mb-2"><br><br>
      <input type="text" name="name" id="name" placeholder="Nombre" class="form-control mb-2"><br><br>
      <input type="text" name="lastname" id="lastname" placeholder="Apellido" class="form-control mb-2"><br><br>
      <input type="date" name="birthday" id="date" placeholder="Fecha" class="form-select mb-2"><br><br>
      <select name="city" id="city" class="form-select mb-2" required>

            <option value="">Seleccione una ciudad</option>
            <?php foreach ($citie as $cit): ?>
                <option value="<?= $cit['id'] ?>">
                  <?= htmlspecialchars($cit['name']) ?></option>
            <?php endforeach; ?>
      </select> <br><br>
       <select name="typeUser" id="typeUser" class="form-select mb-2" required>

            <option value="">Seleccione un tipo de usuario</option>
            <?php foreach ($typeUsers as $tuser): ?>
                <option value="<?= $tuser['id'] ?>">
                  <?= htmlspecialchars($tuser['name']) ?></option>
            <?php endforeach; ?>
      </select>
      <br><br>
      <button type="submit" name="save" class="btn btn-primary w-100">Guardar</button>
      <button type="button" onclick="limpiarFormulario()" class="btn btn-secondary w-100 mt-2">Limpiar</button>


  </form>

  <table class="table table-bordered table-striped"">
    <thead>
      <tr>
        <th>Usuario</th>
        <th>Nombre</th>
        <th>Apellido</th>
        <th>Ciudad</th>
        <th>Tipo de Usuario</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($allUsers as $aUsers): ?>
      <tr>
        <td> <?= htmlspecialchars($aUsers['username']) ?></td>
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
                                  <a href="index.php?action=delete&id=<?= $aUsers['id'] ?>" 
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
        
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>