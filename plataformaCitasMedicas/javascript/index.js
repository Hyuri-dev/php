document.getElementById("btnEditar").addEventListener("click", function () {
  let field_password = document.getElementById("password");
  field_password.readOnly = true;

  let botonLimpiar = document.getElementById("btnLimpiar");
  botonLimpiar.innerText = "Cancelar";
  botonLimpiar.classList.remove("btn-secondary");
  botonLimpiar.classList.add("btn-danger");

  botonLimpiar.type = "button";
  botonLimpiar.onclick = function () {
    window.location.href = "index.php";
  };
});
