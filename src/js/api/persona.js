import { CrudManager } from "../helpers/crud.js";
import { mostrarError } from "../helpers/modal.js";
import introJs from "intro.js";

const APP_URL = window.location.pathname.includes("sistema_aplicaciones")
  ? "/sistema_aplicaciones"
  : "";

const personaManager = new CrudManager({
  entidad: "Persona",
  entidadPlural: "personas",
  apiBase: `${APP_URL}/api/personal`,
  idCampo: "id_persona",
  tablaId: "tablaPersonal",
  modalId: "modalPersona",
  formId: "formPersona",
  btnNuevo: "btnNuevaPersona",
  columnas: [
    (item) => `<td>${item.persona_nombres} ${item.persona_apellidos}</td>`,
    (item) => `<td>${item.persona_identidad}</td>`,
    (item) => `<td>${item.persona_telefono || "No disponible"}</td>`,
    (item) => `<td>${item.persona_correo || "No disponible"}</td>`,
  ],
  camposVer: (item) => `
        <div class="text-left">
            <p><strong>ID:</strong> ${item.id_persona}</p>
            <p><strong>Nombres:</strong> ${item.persona_nombres}</p>
            <p><strong>Apellidos:</strong> ${item.persona_apellidos}</p>
            <p><strong>Identidad:</strong> ${item.persona_identidad}</p>
            <p><strong>Teléfono:</strong> ${
              item.persona_telefono || "No disponible"
            }</p>
            <p><strong>Correo:</strong> ${
              item.persona_correo || "No disponible"
            }</p>
        </div>
    `,
});

personaManager.generarAccionesActivos = function (id, item) {
  if (id == 1) {
    return `
      <div class="btn-group">
        <button type="button" class="btn btn-secondary btn-sm" disabled title="Usuario administrador protegido">
          <i class="fas fa-lock"></i>
        </button>
      </div>
    `;
  }

  let tieneUsuario = false;
  fetch(`${APP_URL}/api/usuarios?persona_id=${id}`)
    .then((response) => response.json())
    .then((resultado) => {
      if (resultado.exito && resultado.data.length > 0) {
        tieneUsuario = true;
      }
    });

  let accionesPersonalizadas = "";
  if (!tieneUsuario) {
    accionesPersonalizadas = `
      <a class="dropdown-item" href="#" data-accion="crear-usuario" data-persona-id="${id}">
        <i class="bi bi-person-plus text-success"></i> Crear Usuario
      </a>
    `;
  }

  return `
    <div class="btn-group">
      <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown">
        <i class="fas fa-bars"></i>
      </button>
      <div class="dropdown-menu dropdown-menu-right">
        <a class="dropdown-item" href="#" data-accion="ver" data-id="${id}">
          <i class="bi bi-eye text-primary"></i> Ver
        </a>
        <a class="dropdown-item" href="#" data-accion="editar" data-id="${id}">
          <i class="bi bi-pencil text-info"></i> Editar
        </a>
        <a class="dropdown-item" href="#" data-accion="eliminar" data-id="${id}">
          <i class="bi bi-trash text-danger"></i> Eliminar
        </a>
        ${accionesPersonalizadas}
      </div>
    </div>
  `;
};

document.addEventListener("click", (e) => {
  if (e.target.closest('[data-accion="crear-usuario"]')) {
    e.preventDefault();
    const personaId = e.target.closest('[data-accion="crear-usuario"]').dataset
      .personaId;
    window.location.href = `${APP_URL}/usuarios?persona_id=${personaId}`;
  }
});

const editarOriginal = personaManager.editar.bind(personaManager);
personaManager.editar = function (id) {
  if (id == 1) {
    mostrarError("No se puede editar el usuario administrador del sistema");
    return;
  }
  editarOriginal(id);
};

const eliminarOriginal = personaManager.eliminar.bind(personaManager);
personaManager.eliminar = function (id) {
  if (id == 1) {
    mostrarError("No se puede eliminar el usuario administrador del sistema");
    return;
  }
  eliminarOriginal(id);
};

// VISTA GUIADA - Personal
document
  .getElementById("btnVistaGuiada")
  ?.addEventListener("click", function (e) {
    e.preventDefault();

    const steps = [
      {
        intro:
          "Bienvenido al módulo de Personal! Aquí gestionas a todas las personas del sistema.",
      },
      {
        element: "#btnNuevaPersona",
        intro: "Haz clic aquí para registrar una nueva persona.",
        position: "left",
      },
      {
        element: "#tablaPersonal",
        intro: "Lista de todo el personal registrado en el sistema.",
        position: "top",
      },
      {
        element: "#tablaPersonal thead th:nth-child(1)",
        intro: "Nombre completo de la persona.",
        position: "bottom",
      },
      {
        element: "#tablaPersonal thead th:nth-child(2)",
        intro: "Número de identidad.",
        position: "bottom",
      },
      {
        element: "#tablaPersonal thead th:nth-child(3)",
        intro: "Número de teléfono de contacto.",
        position: "bottom",
      },
      {
        element: "#tablaPersonal thead th:nth-child(4)",
        intro: "Correo electrónico.",
        position: "bottom",
      },
      {
        element: "#tablaPersonal thead th:nth-child(5)",
        intro: "Acciones para gestionar cada persona.",
        position: "left",
      },
      {
        element: "#tablaPersonal tbody tr:first-child td:last-child",
        intro:
          "Usa este menú para ver, editar, eliminar o crear usuario para esta persona.",
        position: "left",
      },
      {
        intro:
          "El usuario administrador principal está protegido y no se puede editar ni eliminar.",
      },
      {
        intro:
          "Primero crea la persona, luego créale un usuario y finalmente asígnale permisos.",
      },
      {
        intro: "Listo! Mantén actualizada la información de todo tu personal.",
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
