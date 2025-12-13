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

// A. EVENTO AL ABRIR EL MODAL DE EDICIÓN
// Usamos "delegación de eventos" porque los botones están dentro de una tabla
$(document).on("click", ".btn-editar", function () {
  // 1. Obtener los datos del botón que fue clickeado
  const id = $(this).data("id");
  const user = $(this).data("user");
  const doctor = $(this).data("doctor");
  const specialty = $(this).data("specialty");
  const date = $(this).data("date");
  const status = $(this).data("status");

  // 2. Rellenar el formulario del modal
  $("#edit_id").val(id);
  $("#edit_idUser").val(user);
  $("#edit_idDoctor").val(doctor);
  $("#edit_idSpecialty").val(specialty);
  $("#edit_dateAppointment").val(date);
  $("#edit_idStatus").val(status);

  // 3. Mostrar el modal
  $("#modalEditAppointment").modal("show");
});

// B. EVENTO AL GUARDAR (SUBMIT DEL FORMULARIO EDITAR)
document
  .getElementById("edit-appointment-form")
  .addEventListener("submit", function (e) {
    e.preventDefault();
    var datosFormulario = new FormData(this);

    fetch("../src/controller/actualizar_cita.php", {
      // OJO: Archivo nuevo
      method: "POST",
      body: datosFormulario,
    })
      .then((response) => response.text())
      .then((text) => {
        try {
          return JSON.parse(text);
        } catch (error) {
          throw new Error("Respuesta no válida del servidor: " + text);
        }
      })
      .then((data) => {
        if (data.success) {
          alert(data.message);
          $("#modalEditAppointment").modal("hide");
          location.reload();
        } else {
          alert("Error: " + data.message);
        }
      })
      .catch((error) => {
        console.error(error);
        alert("Ocurrió un error al actualizar");
      });
  });
