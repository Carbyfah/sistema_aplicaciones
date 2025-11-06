import { CrudManager } from "../helpers/crud.js";
import { mostrarError } from "../helpers/modal.js";
import introJs from "intro.js";

const APP_URL = window.location.pathname.includes("sistema_aplicaciones")
  ? "/sistema_aplicaciones"
  : "";

const rolManager = new CrudManager({
  entidad: "Rol",
  entidadPlural: "roles",
  apiBase: `${APP_URL}/api/roles`,
  idCampo: "id_roles_persona",
  tablaId: "tablaRoles",
  modalId: "modalRol",
  formId: "formRol",
  btnNuevo: "btnNuevoRol",
  columnas: [
    (item) => `<td>${item.roles_persona_nombre}</td>`,
    (item) => `<td>${item.roles_persona_descripcion || "Sin descripción"}</td>`,
  ],
  camposVer: (item) => `
    <div class="text-left">
      <p><strong>ID:</strong> ${item.id_roles_persona}</p>
      <p><strong>Nombre:</strong> ${item.roles_persona_nombre}</p>
      <p><strong>Descripción:</strong> ${
        item.roles_persona_descripcion || "Sin descripción"
      }</p>
    </div>
  `,
});

rolManager.generarAccionesActivos = function (id, item) {
  const rolId = parseInt(id);

  if (rolId === 1) {
    return `
      <div class="btn-group">
        <button type="button" class="btn btn-secondary btn-sm" disabled title="Rol Administrador protegido">
          <i class="fas fa-lock"></i> Protegido
        </button>
      </div>
    `;
  }

  return `
    <div class="btn-group">
      <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown">
        <i class="fas fa-bars"></i>
      </button>
      <div class="dropdown-menu dropdown-menu-right">
        <a class="dropdown-item" href="#" data-accion="ver" data-id="${id}">
          <i class="fas fa-eye text-primary"></i> Ver
        </a>
        <a class="dropdown-item" href="#" data-accion="editar" data-id="${id}">
          <i class="fas fa-edit text-info"></i> Editar
        </a>
        <a class="dropdown-item" href="#" data-accion="eliminar" data-id="${id}">
          <i class="fas fa-trash text-danger"></i> Eliminar
        </a>
      </div>
    </div>
  `;
};

const editarOriginal = rolManager.editar.bind(rolManager);
rolManager.editar = async function (id) {
  const rolId = parseInt(id);

  if (rolId === 1) {
    mostrarError("No se puede editar el rol Administrador del sistema");
    return;
  }

  editarOriginal(id);
};

const eliminarOriginal = rolManager.eliminar.bind(rolManager);
rolManager.eliminar = async function (id) {
  const rolId = parseInt(id);

  if (rolId === 1) {
    mostrarError("No se puede eliminar el rol Administrador del sistema");
    return;
  }

  eliminarOriginal(id);
};

window.rolManager = rolManager;

// VISTA GUIADA - Roles
document
  .getElementById("btnVistaGuiada")
  ?.addEventListener("click", function (e) {
    e.preventDefault();

    const steps = [
      {
        intro:
          "Bienvenido al módulo de Roles! Aquí gestionas los roles o cargos del sistema.",
      },
      {
        element: "#btnNuevoRol",
        intro: "Haz clic aquí para crear un nuevo rol.",
        position: "left",
      },
      {
        element: "#tablaRoles",
        intro: "Lista de todos los roles configurados en el sistema.",
        position: "top",
      },
      {
        element: "#tablaRoles thead th:nth-child(1)",
        intro: "Nombre del rol (Ej: Administrador, Desarrollador, Usuario).",
        position: "bottom",
      },
      {
        element: "#tablaRoles thead th:nth-child(2)",
        intro: "Descripción del rol y sus responsabilidades.",
        position: "bottom",
      },
      {
        element: "#tablaRoles thead th:nth-child(3)",
        intro: "Acciones para gestionar cada rol.",
        position: "bottom",
      },
      {
        element: "#tablaRoles tbody tr:first-child td:last-child",
        intro: "Usa este menú para ver, editar o eliminar roles.",
        position: "left",
      },
      {
        intro:
          "El rol 'Administrador' está protegido y no se puede editar ni eliminar.",
      },
      {
        intro:
          "Los roles definen el nivel de acceso y permisos de los usuarios en el sistema.",
      },
      {
        intro:
          "Listo! Organiza los permisos del sistema a través de roles bien definidos.",
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
