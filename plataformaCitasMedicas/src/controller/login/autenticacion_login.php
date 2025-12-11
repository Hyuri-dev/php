<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    require_once "../../../config/database.php";

    try {
        $user_form = $_POST["usuario"];
        $password_form = $_POST["contraseña"];

        // CORRECCIÓN 1: Agregué u.idTypeUser al SELECT para poder usarlo en la sesión
        // CORRECCIÓN 2: Cambié 'typeusers' a 'typeUsers' (según tu imagen anterior)
        $sql = "SELECT 
                    u.id, 
                    u.idTypeUser,  
                    u.username, 
                    u.password, 
                    t.name as rol  
                FROM users u
                INNER JOIN typeUsers t ON u.idTypeUser = t.id
                WHERE u.username = :usuario 
                LIMIT 1";

        $stmt = $conn->prepare($sql);
        $stmt->execute([':usuario' => $user_form]);
        $user_db = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user_db) {
            // Nota: Aquí estás comparando texto plano. Para producción usa password_verify()
            if ($password_form === $user_db["password"]) {
                
                $_SESSION['user_id'] = $user_db['id'];
                $_SESSION['username'] = $user_db['username'];
                // Ahora esto SÍ funcionará porque lo agregamos al SELECT arriba
                $_SESSION['rol'] = $user_db['idTypeUser']; 
                $_SESSION['rol_nombre'] = $user_db['rol']; // Opcional: Guardar el nombre del rol (Admin/Medico)
                $_SESSION['logueado'] = true;

              // ------- REDIRECCIÓN SEGUN EL USUARIO AUTENTICADO -------
                switch($_SESSION['rol']){
                case 1: 
                  header('location: ../../../public/index.php');
                  exit;
                  break;
                case 2:
                  header('location: ../../../public/views/index_client.php');
                  exit;
                  break;
                case 3:
                  header('location: ../../../public/views/index_doctor.php');
                  exit;
                  break;
              


          
                };
                // header('location: ../../../public/index.php');

            } else {
                $_SESSION['error_login'] = "Contraseña incorrecta, intentelo de nuevo";
                header("Location: ../../public/login.php");
                exit;
            }
        } else {
            $_SESSION["error_login"] = "Usuario no encontrado";
            header("Location: ../../public/login.php");
            exit;
        }
    } catch (PDOException $e) {
        $_SESSION['error_login'] = "Error en la base de datos: " . $e->getMessage();
        header("Location: ../../public/login.php");
        exit;
    } finally {
        $conn = null;
    }
} else {
    header("Location: ../../../public/login.php");
    exit;
}
?>