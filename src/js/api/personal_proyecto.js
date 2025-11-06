import {
  mostrarExito,
  mostrarError,
  abrirModal,
  confirmarEliminacion,
  confirmarRecuperacion,
} from "../helpers/modal.js";
import { initSelect2 } from "../helpers/selects.js";
import { initDataTable } from "../helpers/tablas.js";
import Swal from "sweetalert2";

const APP_URL = window.location.pathname.includes("sistema_aplicaciones")
  ? "/sistema_aplicaciones"
  : "";

let proyectoSeleccionado = null;
let mostrandoEliminados = false;
let asignaciones = [];
let datatable = null;

document.addEventListener("DOMContentLoaded", function () {
  initSelect2("#selectProyecto");

  const urlParams = new URLSearchParams(window.location.search);
  const proyectoId = urlParams.get("proyecto_id");

  cargarProyectos(proyectoId);
  cargarDatos();

  document
    .getElementById("selectProyecto")
    .addEventListener("change", seleccionarProyecto);
  document
    .getElementById("toggleEliminados")
    .addEventListener("change", function () {
      mostrandoEliminados = this.checked;
      cargarDatos();
    });
  document
    .getElementById("btnNuevoPersonalProyecto")
    .addEventListener("click", nuevaAsignacion);
  document
    .getElementById("formPersonalProyecto")
    .addEventListener("submit", guardarAsignacion);

  agregarEventListenersAsignaciones();
});

async function cargarProyectos(proyectoIdPreseleccionar = null) {
  try {
    const response = await fetch(`${APP_URL}/api/proyectos-asignados`);
    const resultado = await response.json();

    if (resultado.exito) {
      const proyectos = resultado.data;
      const select = document.getElementById("selectProyecto");
      select.innerHTML = '<option value="">Todos los proyectos</option>';

      proyectos.forEach((proyecto) => {
        const option = document.createElement("option");
        option.value = proyecto.id_ordenes_aplicaciones;
        option.textContent = `${proyecto.ordenes_aplicaciones_codigo} - ${proyecto.aplicacion_nombre}`;
        select.appendChild(option);
      });

      $(select).select2();

      if (proyectoIdPreseleccionar) {
        $(select).val(proyectoIdPreseleccionar).trigger("change");
        proyectoSeleccionado = proyectoIdPreseleccionar;
      }
    }
  } catch (error) {
    console.error("Error al cargar proyectos:", error);
    mostrarError("No se pudieron cargar los proyectos");
  }
}

async function cargarDatos() {
  try {
    const situacion = mostrandoEliminados ? 0 : 1;
    const response = await fetch(
      `${APP_URL}/api/personal-proyecto?situacion=${situacion}`
    );
    const resultado = await response.json();

    if (resultado.exito) {
      asignaciones = resultado.data;
      renderizarTabla();
    } else {
      asignaciones = [];
      renderizarTabla();
    }
  } catch (error) {
    console.error("Error al cargar asignaciones:", error);
    mostrarError("No se pudieron cargar las asignaciones");
    asignaciones = [];
    renderizarTabla();
  }
}

function seleccionarProyecto() {
  const proyectoId = document.getElementById("selectProyecto").value;
  proyectoSeleccionado = proyectoId || null;

  renderizarTabla();
}

function renderizarTabla() {
  const container = document.getElementById("asignacionesContainer");

  if (datatable) {
    datatable.destroy();
    datatable = null;
  }

  let asignacionesFiltradas = [...asignaciones];

  if (proyectoSeleccionado) {
    asignacionesFiltradas = asignacionesFiltradas.filter(
      (a) =>
        a.ordenes_aplicaciones_id_ordenes_aplicaciones == proyectoSeleccionado
    );
  }

  if (asignacionesFiltradas.length === 0) {
    const mensaje = proyectoSeleccionado
      ? "No hay personal asignado a este proyecto"
      : mostrandoEliminados
      ? "No hay asignaciones eliminadas"
      : "No hay asignaciones registradas";

    container.innerHTML = `
      <div class="text-center p-5 text-muted">
        <i class="fas fa-users fa-3x mb-3"></i>
        <h5>${mensaje}</h5>
        ${
          proyectoSeleccionado && !mostrandoEliminados
            ? '<p>Utilice el botón "Nueva Asignación" para agregar personal al proyecto.</p>'
            : ""
        }
      </div>
    `;
    return;
  }

  container.innerHTML = `
    <table class="table table-hover table-striped" id="tablaAsignaciones">
      <thead>
        <tr>
          <th width="50px">Estado</th>
          <th>Código Proyecto</th>
          <th>Proyecto</th>
          <th>Personal</th>
          <th>Fecha Asignación</th>
          <th width="150px" class="text-center">Acciones</th>
        </tr>
      </thead>
      <tbody>
        ${asignacionesFiltradas
          .map((asignacion) => generarFilaAsignacion(asignacion))
          .join("")}
      </tbody>
    </table>
  `;

  datatable = initDataTable("#tablaAsignaciones", {
    order: [[4, "desc"]],
  });
}

function generarFilaAsignacion(asignacion) {
  const estaEliminada = asignacion.personal_proyecto_situacion == 0;

  const claseFila = estaEliminada ? "table-danger" : "";
  const iconoEstado = estaEliminada
    ? "fa-user-slash text-danger"
    : "fa-user-check text-success";
  const textoEstado = estaEliminada ? "Eliminada" : "Activa";

  const fecha = asignacion.personal_proyecto_fecha_asignacion
    ? new Date(asignacion.personal_proyecto_fecha_asignacion).toLocaleString(
        "es-ES",
        {
          year: "numeric",
          month: "2-digit",
          day: "2-digit",
          hour: "2-digit",
          minute: "2-digit",
        }
      )
    : "";

  return `
    <tr class="${claseFila}" data-id="${asignacion.id_personal_proyecto}">
      <td class="text-center">
        <i class="fas ${iconoEstado}" title="${textoEstado}"></i>
      </td>
      <td>
        <div class="font-weight-bold">${escapeHtml(
          asignacion.ordenes_aplicaciones_codigo
        )}</div>
        ${
          estaEliminada
            ? '<small class="text-danger"><i>Eliminada</i></small>'
            : ""
        }
      </td>
      <td>${escapeHtml(asignacion.aplicacion_nombre)}</td>
      <td>
        <div>
          <i class="fas fa-user mr-1"></i>
          ${escapeHtml(asignacion.persona_nombres)} ${escapeHtml(
    asignacion.persona_apellidos
  )}
        </div>
      </td>
      <td>${fecha}</td>
      <td class="text-center">
        ${generarBotonesAccion(asignacion)}
      </td>
    </tr>
  `;
}

function generarBotonesAccion(asignacion) {
  const estaEliminada = asignacion.personal_proyecto_situacion == 0;

  if (estaEliminada) {
    return `
      <div class="btn-group">
        <button type="button" class="btn btn-secondary btn-sm dropdown-toggle" data-toggle="dropdown">
          <i class="fas fa-bars"></i>
        </button>
        <div class="dropdown-menu dropdown-menu-right">
          <a class="dropdown-item" href="#" data-accion="ver" data-id="${asignacion.id_personal_proyecto}">
            <i class="fas fa-eye text-primary"></i> Ver
          </a>
          <a class="dropdown-item" href="#" data-accion="recuperar" data-id="${asignacion.id_personal_proyecto}">
            <i class="fas fa-undo text-success"></i> Recuperar
          </a>
        </div>
      </div>
    `;
  }

  return `
    <div class="btn-group">
      <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown">
        <i class="fas fa-bars"></i>
      </button>
      <div class="dropdown-menu dropdown-menu-right">
        <a class="dropdown-item" href="#" data-accion="ver" data-id="${asignacion.id_personal_proyecto}">
          <i class="fas fa-eye text-primary"></i> Ver
        </a>
        <a class="dropdown-item" href="#" data-accion="editar" data-id="${asignacion.id_personal_proyecto}">
          <i class="fas fa-edit text-info"></i> Editar
        </a>
        <a class="dropdown-item" href="#" data-accion="eliminar" data-id="${asignacion.id_personal_proyecto}">
          <i class="fas fa-trash text-danger"></i> Eliminar
        </a>
      </div>
    </div>
  `;
}

function agregarEventListenersAsignaciones() {
  const container = document.getElementById("asignacionesContainer");

  container.addEventListener("click", function (e) {
    const boton = e.target.closest("a[data-accion]");
    if (!boton) return;

    e.preventDefault();
    const accion = boton.dataset.accion;
    const id = parseInt(boton.dataset.id);

    switch (accion) {
      case "ver":
        verAsignacion(id);
        break;
      case "editar":
        editarAsignacion(id);
        break;
      case "eliminar":
        eliminarAsignacion(id);
        break;
      case "recuperar":
        recuperarAsignacion(id);
        break;
    }
  });
}

function verAsignacion(id) {
  const asignacion = asignaciones.find((a) => a.id_personal_proyecto == id);

  if (!asignacion) return;

  const fecha = asignacion.personal_proyecto_fecha_asignacion
    ? new Date(asignacion.personal_proyecto_fecha_asignacion).toLocaleString(
        "es-ES",
        {
          year: "numeric",
          month: "2-digit",
          day: "2-digit",
          hour: "2-digit",
          minute: "2-digit",
        }
      )
    : "";

  const estadoTexto =
    asignacion.personal_proyecto_situacion == 1 ? "Activa" : "Eliminada";
  const estadoClase =
    asignacion.personal_proyecto_situacion == 1 ? "success" : "danger";

  Swal.fire({
    title: "Detalle de Asignación",
    html: `
      <div class="text-left">
        <p><strong>Proyecto:</strong> ${escapeHtml(
          asignacion.ordenes_aplicaciones_codigo
        )} - ${escapeHtml(asignacion.aplicacion_nombre)}</p>
        <p><strong>Personal:</strong> ${escapeHtml(
          asignacion.persona_nombres
        )} ${escapeHtml(asignacion.persona_apellidos)}</p>
        <p><strong>Fecha Asignación:</strong> ${fecha}</p>
        <p><strong>Estado:</strong> <span class="badge badge-${estadoClase}">${estadoTexto}</span></p>
      </div>
    `,
    icon: "info",
    confirmButtonText: "Cerrar",
  });
}

async function editarAsignacion(id) {
  const asignacion = asignaciones.find((a) => a.id_personal_proyecto == id);

  if (!asignacion) return;

  abrirModal(
    "modalPersonalProyecto",
    "Editar Asignación",
    asignacion,
    "formPersonalProyecto"
  );

  await cargarProyectosEnModal();
  await cargarPersonalDisponible();

  setTimeout(() => {
    document.getElementById("id_personal_proyecto").value =
      asignacion.id_personal_proyecto;

    $("#ordenes_aplicaciones_id_ordenes_aplicaciones")
      .val(asignacion.ordenes_aplicaciones_id_ordenes_aplicaciones)
      .trigger("change");

    $("#persona_id_persona")
      .val(asignacion.persona_id_persona)
      .trigger("change");
  }, 300);
}

async function nuevaAsignacion() {
  abrirModal(
    "modalPersonalProyecto",
    "Nueva Asignación",
    null,
    "formPersonalProyecto"
  );

  await cargarProyectosEnModal();
  await cargarPersonalDisponible();

  setTimeout(() => {
    document.getElementById("id_personal_proyecto").value = "";

    if (proyectoSeleccionado) {
      $("#ordenes_aplicaciones_id_ordenes_aplicaciones")
        .val(proyectoSeleccionado)
        .trigger("change");
    }
  }, 300);
}

async function cargarPersonalDisponible() {
  try {
    const response = await fetch(`${APP_URL}/api/personal?situacion=1`);
    const resultado = await response.json();

    if (resultado.exito) {
      const select = document.getElementById("persona_id_persona");
      select.innerHTML = '<option value="">Seleccione una persona</option>';

      resultado.data.forEach((persona) => {
        const option = document.createElement("option");
        option.value = persona.id_persona;
        option.textContent = `${persona.persona_nombres} ${persona.persona_apellidos}`;
        select.appendChild(option);
      });

      $("#persona_id_persona").select2();
    }
  } catch (error) {
    console.error("Error al cargar personal:", error);
    mostrarError("No se pudo cargar el listado de personal");
  }
}

async function cargarProyectosEnModal() {
  try {
    const response = await fetch(`${APP_URL}/api/proyectos-asignados`);
    const resultado = await response.json();

    if (resultado.exito) {
      const select = document.getElementById(
        "ordenes_aplicaciones_id_ordenes_aplicaciones"
      );
      select.innerHTML = '<option value="">Seleccione un proyecto</option>';

      resultado.data.forEach((proyecto) => {
        const option = document.createElement("option");
        option.value = proyecto.id_ordenes_aplicaciones;
        option.textContent = `${proyecto.ordenes_aplicaciones_codigo} - ${proyecto.aplicacion_nombre}`;
        select.appendChild(option);
      });

      $("#ordenes_aplicaciones_id_ordenes_aplicaciones").select2({
        theme: "bootstrap4",
        dropdownParent: $("#modalPersonalProyecto"),
      });
    }
  } catch (error) {
    console.error("Error al cargar proyectos:", error);
    mostrarError("No se pudieron cargar los proyectos");
  }
}

async function guardarAsignacion(e) {
  e.preventDefault();

  const formData = new FormData(e.target);
  const datos = Object.fromEntries(formData.entries());

  if (!datos.ordenes_aplicaciones_id_ordenes_aplicaciones) {
    mostrarError("Debe seleccionar un proyecto");
    return;
  }

  if (!datos.persona_id_persona) {
    mostrarError("Debe seleccionar una persona");
    return;
  }

  try {
    const response = await fetch(`${APP_URL}/api/personal-proyecto`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(datos),
    });

    const resultado = await response.json();

    if (resultado.exito) {
      mostrarExito(resultado.mensaje);
      document
        .getElementById("modalPersonalProyecto")
        .querySelector(".close")
        .click();

      await cargarDatos();
    } else {
      mostrarError(resultado.mensaje);
    }
  } catch (error) {
    console.error("Error al guardar asignación:", error);
    mostrarError("No se pudo guardar la asignación");
  }
}

async function eliminarAsignacion(id) {
  const confirmado = await confirmarEliminacion(
    "¿Eliminar asignación?",
    "La asignación se marcará como eliminada pero podrá recuperarla después."
  );

  if (!confirmado) return;

  try {
    const response = await fetch(`${APP_URL}/api/personal-proyecto/eliminar`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ id: id }),
    });

    const resultado = await response.json();

    if (resultado.exito) {
      mostrarExito(resultado.mensaje);
      await cargarDatos();
    } else {
      mostrarError(resultado.mensaje);
    }
  } catch (error) {
    console.error("Error al eliminar asignación:", error);
    mostrarError("No se pudo eliminar la asignación");
  }
}

async function recuperarAsignacion(id) {
  const confirmado = await confirmarRecuperacion();

  if (!confirmado) return;

  try {
    const response = await fetch(`${APP_URL}/api/personal-proyecto/recuperar`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ id: id }),
    });

    const resultado = await response.json();

    if (resultado.exito) {
      mostrarExito(resultado.mensaje);
      await cargarDatos();
    } else {
      mostrarError(resultado.mensaje);
    }
  } catch (error) {
    console.error("Error al recuperar asignación:", error);
    mostrarError("No se pudo recuperar la asignación");
  }
}

function escapeHtml(unsafe) {
  if (!unsafe) return "";
  return unsafe
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;");
}

// Vista Guiada - Personal Proyecto
document
  .getElementById("btnVistaGuiada")
  ?.addEventListener("click", function (e) {
    e.preventDefault();

    const steps = [
      {
        intro:
          "¡Bienvenido al módulo de Personal Proyecto! Aquí asignas personal a los proyectos.",
      },
      {
        element: "#selectProyecto",
        intro:
          "Selecciona un proyecto específico para ver solo sus asignaciones, o déjalo en 'Todos' para ver todo.",
        position: "bottom",
      },
      {
        element: "#toggleEliminados",
        intro:
          "Activa este switch para ver las asignaciones eliminadas y poder recuperarlas.",
        position: "left",
      },
      {
        element: "#btnNuevoPersonalProyecto",
        intro: "Haz clic aquí para asignar una nueva persona a un proyecto.",
        position: "left",
      },
      {
        element: "#asignacionesContainer",
        intro:
          "Aquí se muestran todas las asignaciones de personal a proyectos.",
        position: "top",
      },
      {
        element: "#tablaAsignaciones thead th:nth-child(1)",
        intro: "Estado de la asignación (Activa o Eliminada).",
        position: "bottom",
      },
      {
        element: "#tablaAsignaciones thead th:nth-child(2)",
        intro: "Código único del proyecto.",
        position: "bottom",
      },
      {
        element: "#tablaAsignaciones thead th:nth-child(3)",
        intro: "Nombre del proyecto/aplicación.",
        position: "bottom",
      },
      {
        element: "#tablaAsignaciones thead th:nth-child(4)",
        intro: "Persona asignada al proyecto.",
        position: "bottom",
      },
      {
        element: "#tablaAsignaciones thead th:nth-child(5)",
        intro: "Fecha en que se realizó la asignación.",
        position: "bottom",
      },
      {
        element: "#tablaAsignaciones thead th:nth-child(6)",
        intro: "Acciones para gestionar cada asignación.",
        position: "left",
      },
      {
        element: "#tablaAsignaciones tbody tr:first-child td:last-child",
        intro:
          "Usa este menú para ver detalles, editar o eliminar asignaciones.",
        position: "left",
      },
      {
        intro:
          "Las asignaciones eliminadas se muestran en rojo y pueden recuperarse.",
      },
      {
        intro:
          "¡Listo! Gestiona eficientemente el equipo de trabajo para cada proyecto.",
      },
    ];

    introJs()
      .setOptions({
        steps: steps,
        showProgress: true,
        showBullets: true,
        exitOnOverlayClick: false,
        nextLabel: "Siguiente →",
        prevLabel: "← Anterior",
        doneLabel: "Finalizar",
        skipLabel: "Salir",
      })
      .start();
  });
