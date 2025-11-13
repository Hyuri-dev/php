<?php
// Importa la configuración y conexión a base de datos PDO
require_once '../config/database.php';

// Variable para mensajes informativos al usuario
$message = "";

// Proceso al recibir datos POST (formulario o eliminación)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Se extraen los datos del formulario o se asignan valores vacíos si no existen
    $id = $_POST['id'] ?? null;
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $department = $_POST['department'] ?? '';
    $country = $_POST['country'] ?? '';
    $state = $_POST['state'] ?? '';
    $city = $_POST['city'] ?? '';

    // Si se envió el formulario para guardar (insertar o actualizar)
    if (isset($_POST['save'])) {
        // Validar que campos obligatorios no estén vacíos
        if ($name && $email && $department && $country) {
            if ($id) {
                // Actualizar registro existente con ID
                $stmt = $pdo->prepare("UPDATE employees SET name=?, email=?, phone=?, department_id=?, country_id=?, state_id=?, city_id=? WHERE id=?");
                $success = $stmt->execute([$name, $email, $phone, $department, $country, $state, $city, $id]);
                $message = $success ? "Empleado actualizado" : "Error al actualizar";
            } else {
                // Insertar nuevo registro
                $stmt = $pdo->prepare(query: "INSERT INTO employees (name,email,phone,department_id,country_id,state_id,city_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $success = $stmt->execute([$name, $email, $phone, $department, $country, $state, $city]);
                $message = $success ? "Empleado agregado" : "Error al agregar";
            }
            // Limpiar $_POST para evitar reinserciones accidentales
            $_POST = [];
        } else {
            $message = "Campos obligatorios incompletos";
        }
    }
    // Si se envió formulario para borrar un registro
    elseif (isset($_POST['delete'])) {
        $id_delete = $_POST['id_delete'] ?? null;
        if ($id_delete) {
            $stmt = $pdo->prepare("DELETE FROM employees WHERE id=?");
            $success = $stmt->execute([$id_delete]);
            $message = $success ? "Empleado eliminado" : "Error al eliminar";
        }
    }
}

// Consultas para llenar selects del formulario
$departments = $pdo->query("SELECT id, name FROM departments")->fetchAll();
$countries = $pdo->query("SELECT id, name FROM countries")->fetchAll();
$states = $pdo->query("SELECT id, name FROM states")->fetchAll();
$cities = $pdo->query("SELECT id, name FROM cities")->fetchAll();

// Consulta principal que obtiene empleados con nombres de relaciones y sus IDs
$employees = $pdo->query(
    "SELECT e.id, e.name, e.email, e.phone,
            d.name as department, c.name as country, s.name as state, ci.name as city,
            e.department_id, e.country_id, e.state_id, e.city_id
    FROM employees e
    LEFT JOIN departments d ON e.department_id = d.id
    LEFT JOIN countries c ON e.country_id = c.id
    LEFT JOIN states s ON e.state_id = s.id
    LEFT JOIN cities ci ON e.city_id = ci.id"
)->fetchAll();

?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Gestión de Empleados</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
<script>
// Función para cargar datos en formulario para editar empleado
function editarEmpleado(id, name, email, phone, department, country, state, city) {
    document.getElementById('id').value = id;
    document.getElementById('name').value = name;
    document.getElementById('email').value = email;
    document.getElementById('phone').value = phone;
    document.getElementById('department').value = department;
    document.getElementById('country').value = country;
    document.getElementById('state').value = state;
    document.getElementById('city').value = city;
    // Lleva la vista hacia el formulario arriba
    window.scrollTo(0, 0);
}
// Limpia el formulario y los campos ocultos
function limpiarFormulario() {
    document.getElementById('formEmpleado').reset();
    document.getElementById('id').value = '';
}
// Hace desaparecer el mensaje automáticamente después de 4.5 segundos
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
<body>
<div class="container mt-4">
    <h2>Gestión de Empleados</h2>
    <!-- Mostrar mensaje de estado si existe -->
    <?php if ($message): ?>
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
    <?php endif; ?>
    <!-- Formulario para ingreso/actualización de empleados -->
    <form method="POST" id="formEmpleado" action="" class="mb-4">
        <!-- Campo oculto que identifica el registro para editar -->
        <input type="hidden" name="id" id="id" />
        <!-- Campos para editar o agregar empleado -->
        <input type="text" name="name" id="name" placeholder="Nombre" required class="form-control mb-2" />
        <input type="email" name="email" id="email" placeholder="Email" required class="form-control mb-2" />
        <input type="text" name="phone" id="phone" placeholder="Teléfono (opcional)" class="form-control mb-2" />
        <select name="department" id="department" class="form-select mb-2" required>
            <option value="">Seleccione departamento</option>
            <?php foreach ($departments as $dep): ?>
                <option value="<?= $dep['id'] ?>"><?= htmlspecialchars($dep['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="country" id="country" class="form-select mb-2" required>
            <option value="">Seleccione país</option>
            <?php foreach ($countries as $c): ?>
                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="state" id="state" class="form-select mb-2">
            <option value="">Seleccione estado</option>
            <?php foreach ($states as $st): ?>
                <option value="<?= $st['id'] ?>"><?= htmlspecialchars($st['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="city" id="city" class="form-select mb-2">
            <option value="">Seleccione ciudad</option>
            <?php foreach ($cities as $ci): ?>
                <option value="<?= $ci['id'] ?>"><?= htmlspecialchars($ci['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" name="save" class="btn btn-primary w-100">Guardar</button>
        <button type="button" onclick="limpiarFormulario()" class="btn btn-secondary w-100 mt-2">Limpiar</button>
    </form>
    <!-- Tabla que muestra todos los empleados -->
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Nombre</th><th>Email</th><th>Teléfono</th><th>Departamento</th><th>País</th><th>Estado</th><th>Ciudad</th><th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($employees as $emp): ?>
            <tr>
                <td><?= htmlspecialchars($emp['name']) ?></td>
                <td><?= htmlspecialchars($emp['email']) ?></td>
                <td><?= htmlspecialchars($emp['phone']) ?></td>
                <td><?= htmlspecialchars($emp['department']) ?></td>
                <td><?= htmlspecialchars($emp['country']) ?></td>
                <td><?= htmlspecialchars($emp['state']) ?></td>
                <td><?= htmlspecialchars($emp['city']) ?></td>
                <td>
                    <!-- Botón para cargar datos del empleado en el formulario para edición -->
                    <button class="btn btn-warning btn-sm"
                        onclick="editarEmpleado(
                            '<?= $emp['id'] ?>',
                            '<?= htmlspecialchars(addslashes($emp['name'])) ?>',
                            '<?= htmlspecialchars(addslashes($emp['email'])) ?>',
                            '<?= htmlspecialchars(addslashes($emp['phone'])) ?>',
                            '<?= $emp['department_id'] ?>',
                            '<?= $emp['country_id'] ?>',
                            '<?= $emp['state_id'] ?>',
                            '<?= $emp['city_id'] ?>'
                        )">
                        Editar
                    </button>
                    <!-- Formulario y botón para eliminar empleado (con confirmación) -->
                    <form method="POST" action="" style="display:inline-block;" onsubmit="return confirm('¿Seguro que deseas eliminar este empleado?');">
                        <input type="hidden" name="id_delete" value="<?= $emp['id'] ?>" />
                        <button type="submit" name="delete" class="btn btn-danger btn-sm">Eliminar</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<!-- Carga JS para funcionalidades de Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
