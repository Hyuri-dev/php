document
  .getElementById("appointment-form")
  .addEventListener("submit", function (e) {
    e.preventDefault(); // 1. Evitar que la página se recargue

    // 2. Recoger los datos del formulario
    var datosFormulario = new FormData(this);

    // 3. Enviarlos por "debajo de la mesa" (Fetch) al archivo PHP
    fetch("../src/controller/procesar_cita.php", {
      method: "POST",
      body: datosFormulario,
    })
      .then((response) => response.json()) // Convertir respuesta a JSON
      .then((data) => {
        if (data.success) {
          // A. Si todo salió bien:
          alert(data.message);
          $("#modalNewAppointment").modal("hide"); // Cerrar modal (usando jQuery de AdminLTE)
          location.reload(); // Recargar para ver la nueva cita en la tabla
        } else {
          // B. Si hubo error en PHP:
          alert(data.message);
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        alert("Ocurrió un error en la conexión");
      });
  });
