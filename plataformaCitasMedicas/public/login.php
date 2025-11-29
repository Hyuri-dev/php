<?php 
session_start()
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Gestor Citas Medicas</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="../styles/login_styles.css">

</head>

<body>
  <div class="login-card">
    <div class="card shadow-sm border-0 rounded-3">
      
    </div>
    <h2 class="card-title text-center mb-4 fw-light fs-3">Iniciar Sesión</h2>
    <p class="text-center text-muted mb-4">Acceso al panel de Citas Medicas</p>

      <form action="../src/controller/login/autenticacion_login.php" method="POST">
        <div class="form-floating mb-3">
        <input type="text" class="form-control" id="usuario" name="usuario" placeholder="Ingrese el usuario" required>
          <label for="usuario">Usuario</label>
        </div>
          <div class="form-floating mb-3">
            <input type="text" class="form-control" id="usuario" name="contraseña" placeholder="Ingrese la contraseña" required>
          <label for="contraseña">Contraseña</label>
        </div>
        <?php
        //Mensaje que maneja el error del login
        if(isset($_SESSION['error_login'])){
          echo '<div class="alert alert-danger" role="alert">' . $_SESSION['error_login'] . '</div>';
          unset($_SESSION['error_login']);
        }  
        ?>
        <div class="d-grid">
          <button class="btn btn-primary btn-lg fw-bold" type="submit">Entrar</button>
        </div>
        <hr class="my-4">

        <div class="text-center">
          <p class="mb-0">¿No posees cuenta? <a href="#">Registrate Aqui</a></p>
        </div>
        
      </form>


  </div>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>