<?php
require_once '../config/database.php';
require_once '../src/controller/createUser.php';
require_once '../src/controller/updateUser.php';
require_once '../src/controller/deleteUser.php';
require_once '../src/controller/rol_check.php';


// Verificacion de sesión
if (!isset($_SESSION['logueado']) || $_SESSION['logueado'] !== true) {
    header("Location: login.php");
    exit;
}

verificarRol([1]);
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

$allEspecialty = $conn->query("SELECT id , nombre FROM specialty ")->fetchAll();

//  Datos de las citas medicas

$sql = "SELECT 
    c.id AS id_appointment,
    c.dateAppointment,
    c.idStatus,
    c.idUser,       -- <-- AGREGAR ESTO (ID del paciente)
    c.idDoctor,     -- <-- AGREGAR ESTO (ID del doctor)
    c.idSpecialty,  -- <-- AGREGAR ESTO (ID especialidad)
    s.name AS status_name,
    p.name AS patient,
    p.lastname AS patient_lastname,
    m.name AS doctor,
    e.nombre AS specialty
  FROM appointment c
  INNER JOIN users p ON c.idUser = p.id
  INNER JOIN users m ON c.idDoctor = m.id
  LEFT JOIN specialty e ON c.idSpecialty = e.id
  LEFT JOIN status s ON c.idStatus = s.id
  ORDER BY c.dateAppointment ASC";

$resultado = $conn -> query($sql);



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
<link rel="stylesheet" href="../styles/styles.css">
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
  <nav class="main-header navbar navbar-expand navbar-dark navbar-light">
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
      <i class="fa-solid fa-house-medical"></i>
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
              <i class="fa-solid fa-user-tie"></i>
              <p>Especialidad</p>
            </a>
            
            <a href="status.php" class="nav-link active">
              <i class="fa-solid fa-gear"></i>
              <p>Estados de las citas</p>
            </a>
            <!-- Pendiente por crear el apartado de las citas -->
            <a href="crudSpecialty.php" class="nav-link active">
              <i class="fa-solid fa-calendar"></i>
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
            <h1>Gestión de Citas Médicas</h1>
        </div>
    </section>

    <section class="content" style="display: flex;">
        <div class="container-fluid">
            
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title" style="color: #0069D9">Listado de Citas Programadas</h3> 
                    <div class="card-tools">
                        <button class="btn btn-sm btn-primary" data-toggle="modal" data-target='#modalNewAppointment'>
                            <i class="fas fa-plus"></i> Nueva Cita
                          </button>
                    </div>
                </div>

                <div class="card-body table-responsive p-0">
                    <table class="table table-hover table-striped text-nowrap">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Fecha y Hora</th>
                                <th>Paciente</th>
                                <th>Médico / Especialidad</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            // VERIFICAR SI HAY CITAS
                            if ($resultado-> rowCount()) {
                                // ITERAR SOBRE LOS DATOS
                                while($fila = $resultado->fetch(PDO::FETCH_ASSOC)) { 
                                    
                                    // Lógica visual para el estado (Badges de Bootstrap)
                                    $estadoTexto = "desconocido";
                                    $badgeColor = 'secondary';

                                    if($fila['idStatus'] == '4') {
                                      $estadoTexto = "Completada";
                                      $badgeColor = 'success';
                                    }else if ($fila['idStatus'] == '3') {
                                      $estadoTexto = "Pendiente";
                                      $badgeColor = 'warning';
                                    } else if ($fila['idStatus'] == '2') {
                                      $estadoTexto = "Cancelado";
                                      $badgeColor = 'danger';} // // Cancelada
                            ?>
                            
                            <tr>
                                <td><?php echo $fila['id_appointment']; ?></td>
                                
                                <td>
                                    <?php echo date('d/m/Y h:i A', strtotime($fila['dateAppointment'])); ?>
                                </td>
                                
                                <td>
                                    <strong><?php echo $fila['patient'] . " " . $fila['patient_lastname']; ?></strong>
                                    <br>
                                    <!-- <small class="text-muted">Motivo: <?php echo substr($fila['motivo'], 0, 20); ?>...</small> -->
                                </td>
                                
                                <td>
                                    Dr. <?php echo $fila['doctor']; ?>
                                    <br>
                                    <small class="text-info"><?php echo $fila['specialty']; ?></small>
                                </td>
                                
                                <td>
                                    <span class="badge badge-<?php echo $badgeColor; ?>">
                                        <?php echo $fila['status_name']; ?>
                                    </span>
                                </td>
                                
                                <td>
                                    <div class="btn-group">
                                      <button type="button" class="btn btn-info btn-sm btn-editar" 
                                                data-id="<?php echo $fila['id_appointment']; ?>"
                                                data-user="<?php echo $fila['idUser']; ?>"
                                                data-doctor="<?php echo $fila['idDoctor']; ?>"
                                                data-specialty="<?php echo $fila['idSpecialty']; ?>"
                                                data-date="<?php echo date('Y-m-d\TH:i', strtotime($fila['dateAppointment'])); ?>" 
                                                data-status="<?php echo $fila['idStatus']; ?>"
                                                title="Editar">
                                            <i class="fas fa-pencil-alt"></i>
                                        </button>
                                        
                                        
                                        <button type="button" class="btn btn-danger btn-sm btn-eliminar" 
                                                data-id="<?php echo $fila['id_appointment']; ?>" title="Cancelar">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <?php 
                                } // Fin del while
                            } else { 
                            ?>
                                <tr>
                                    <td colspan="6" class="text-center">No hay citas registradas.</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            </div>
    </section>
</div>


        <?php if ($message): ?>
          <div class="alert alert-info alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($message) ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
        <?php endif; ?>





      </div>
    </section>
  </div>


  <!-- Footer -->
  <footer class="main-footer text-center bg-dark">
    <strong>&copy; 2025 ServiCare</strong> Todos los derechos reservados.
  </footer>
</div>

<div class="modal fade" id="modalNewAppointment" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Crear nueva cita</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form id="appointment-form">
        <div class="modal-body">
            <div class="form-group">
                <label for="paciente">Paciente</label>
                <select class="form-control" name="idUser" required>
                    <option value="">Seleccione...</option>
                    <?php foreach ($allUsers as $user): ?>
                         <option value="<?= $user['id'] ?>"><?= $user['name'] . ' ' . $user['lastname'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="doctor">Doctor</label>
                <select class="form-control" name="idDoctor" required>
    <option value="">Seleccione...</option>
    
    <?php foreach ($allUsers as $user): ?>
        
        <?php if ($user['idTypeUser'] == 3): ?> 
            
            <option value="<?= $user['id'] ?>">
                <?= $user['name'] . ' ' . $user['lastname']. ' ' . $user['idTypeUser'] ?>
            </option>
            
        <?php endif; ?>
        
    <?php endforeach; ?>
</select>
            </div>

            <div class="form-group">
                <label>Fecha y Hora</label>
                <input type="datetime-local" class="form-control" name="dateAppointment" required>
            </div>

            <div class="form-group">
                <label for="especialidad">Especialidad</label>
                <select class="form-control" name="idSpecialty" required>
                    <option value="">Seleccione...</option>
                    <?php foreach ($allEspecialty as $specialty): ?>
                         <option value="<?= $specialty['id'] ?>"><?= $specialty['nombre'] ?></option>
                    <?php endforeach; ?>
                    </select>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            <button type="submit" class="btn btn-primary">Crear Cita</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal para editar -->
<div class="modal fade" id="modalEditAppointment" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-info">
        <h5 class="modal-title text-white">Editar Cita</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form id="edit-appointment-form">
        <input type="hidden" name="id" id="edit_id">

        <div class="modal-body">
            <div class="form-group">
                <label>Paciente</label>
                <select class="form-control" name="idUser" id="edit_idUser" required>
                    <option value="">Seleccione...</option>
                    <?php foreach ($allUsers as $user): ?>
                         <option value="<?= $user['id'] ?>"><?= $user['name'] . ' ' . $user['lastname'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Doctor</label>
                <select class="form-control" name="idDoctor" id="edit_idDoctor" required>
                    <option value="">Seleccione...</option>
                    <?php foreach ($allUsers as $user): ?>
                        <?php if ($user['idTypeUser'] == 3): ?> 
                            <option value="<?= $user['id'] ?>"><?= $user['name'] . ' ' . $user['lastname'] ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Fecha y Hora</label>
                <input type="datetime-local" class="form-control" name="dateAppointment" id="edit_dateAppointment" required>
            </div>

            <div class="form-group">
                <label>Especialidad</label>
                <select class="form-control" name="idSpecialty" id="edit_idSpecialty" required>
                    <option value="">Seleccione...</option>
                    <?php foreach ($allEspecialty as $specialty): ?>
                         <option value="<?= $specialty['id'] ?>"><?= $specialty['nombre'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Estado</label>
                <select class="form-control" name="idStatus" id="edit_idStatus" required>
                    <option value="3">Pendiente</option>
                    <option value="4">Completada</option>
                    <option value="2">Cancelada</option>
                </select>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-info">Guardar Cambios</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<script src="../javascript/appointment.js"></script>


</body>
</html>