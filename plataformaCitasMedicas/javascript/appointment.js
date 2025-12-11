document
  .getElementById("appointment-form")
  .addEventListener("submit", function (e) {
    e.preventDefault();
    let datosFormulario = new FormData(this);
    fetch("crudAppointment.php", {
      method: "POST",
      body: datosFormulario,
    });
  });
