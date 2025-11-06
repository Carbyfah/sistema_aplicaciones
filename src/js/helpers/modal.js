import Swal from "sweetalert2";

export const abrirModal = (modalId, titulo, data = null, formId) => {
  const tituloElement = document.getElementById(`${modalId}Titulo`);
  const form = document.getElementById(formId);

  if (tituloElement) {
    tituloElement.textContent = titulo;
  }

  if (form) {
    form.reset();

    const inputs = form.querySelectorAll("input, select, textarea");
    inputs.forEach((input) => {
      if (input.type !== "hidden") {
        input.value = "";
      }
    });

    if (data) {
      Object.keys(data).forEach((key) => {
        const input = form.querySelector(`[name="${key}"]`);
        if (input) {
          input.value = data[key] || "";
        }
      });
    }
  }

  window.$(`#${modalId}`).modal("show");
};

export const cerrarModal = (modalId) => {
  window.$(`#${modalId}`).modal("hide");
};

export const confirmarEliminacion = async (
  titulo = "¿Estás seguro?",
  texto = "Esta acción no se puede revertir"
) => {
  const resultado = await Swal.fire({
    title: titulo,
    text: texto,
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    confirmButtonText: "Sí, eliminar",
    cancelButtonText: "Cancelar",
  });

  return resultado.isConfirmed;
};

export const confirmarRecuperacion = async (
  titulo = "¿Recuperar registro?",
  texto = "Este registro volverá a estar activo"
) => {
  const resultado = await Swal.fire({
    title: titulo,
    text: texto,
    icon: "question",
    showCancelButton: true,
    confirmButtonColor: "#28a745",
    cancelButtonColor: "#6c757d",
    confirmButtonText: "Sí, recuperar",
    cancelButtonText: "Cancelar",
  });

  return resultado.isConfirmed;
};

export const mostrarExito = (mensaje) => {
  return Swal.fire({
    position: "top-end",
    icon: "success",
    title: mensaje,
    showConfirmButton: false,
    timer: 2000,
    toast: true,
    timerProgressBar: true,
  });
};

export const mostrarError = (mensaje) => {
  return Swal.fire({
    position: "top-end",
    icon: "error",
    title: mensaje,
    showConfirmButton: false,
    timer: 2000,
    toast: true,
    timerProgressBar: true,
  });
};
