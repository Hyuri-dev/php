<?php

session_start();


if (!isset($_SESSION['logueado']) || $_SESSION['logueado'] !== true) {
    header("Location: login.php");
    exit;
}


function verificarRol($roles_permitidos) {

    if (!in_array($_SESSION['rol'], $roles_permitidos)) {

        echo "<script>alert('Acceso Denegado: No tienes permisos para ver esta sección.'); window.location.href='index.php';</script>";
        exit;
    }
}
?>