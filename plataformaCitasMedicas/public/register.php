<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registro - Gestor Citas Medicas</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="../styles/login_styles.css">
  
  <style>
    /* Un pequeño estilo extra para centrar todo en la pantalla */

  </style>
</head>

<body>

  <div class="card shadow-lg border-0 rounded-4" style="width: 100%; max-width: 800px;">
    
    <div class="card-body p-5">
      <h1 class="card-title text-center mb-2 fw-bold text-primary">Crear Cuenta</h1>
      <p class="text-center text-muted mb-4">Complete el formulario para registrarse en el sistema</p>

      <form action="" method="POST">
        
        <div class="row g-3 mb-3">
          <div class="col-md-6">
            <div class="form-floating">
              <input type="text" class="form-control" id="name" name="name" placeholder="Nombre" required>
              <label for="name">Nombre</label>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-floating">
              <input type="text" class="form-control" id="lastName" name="lastName" placeholder="Apellido" required>
              <label for="lastName">Apellido</label>
            </div>
          </div>
        </div>

        <div class="row g-3 mb-3">
          <div class="col-md-6">
            <div class="form-floating">
              <input type="text" class="form-control" id="usuario" name="usuario" placeholder="Usuario" required>
              <label for="usuario">Usuario</label>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-floating">
              <input type="date" class="form-control" id="birthday" name="birthday" placeholder="Fecha de nacimiento" required>
              <label for="birthday">Fecha de nacimiento</label>
            </div>
          </div>
        </div>

        <div class="form-floating mb-3">
          <input type="email" class="form-control" id="email" name="email" placeholder="nombre@ejemplo.com" required>
          <label for="email">Correo Electrónico</label>
        </div>

        <div class="form-floating mb-4">
          <input type="password" class="form-control" id="password" name="password" placeholder="Contraseña" required>
          <label for="password">Contraseña</label>
        </div>
        
        <?php
        // Mensaje de error (si existe)
        if(isset($_SESSION['error_login'])){
          echo '<div class="alert alert-danger" role="alert"><i class="bi bi-exclamation-triangle-fill"></i> ' . $_SESSION['error_login'] . '</div>';
          unset($_SESSION['error_login']);
        }  
        ?>
        
        <div class="d-grid gap-2">
          <button class="btn btn-primary btn-lg fw-bold rounded-pill" type="submit">Registrarse</button>
        </div>

        <hr class="my-4">
        
        <div class="text-center">
          <span class="text-muted">¿Ya tienes cuenta? </span>
          <a href="login.php" class="text-decoration-none fw-bold">Inicia Sesión aquí</a>
        </div>

      </form>
    </div>
  </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>