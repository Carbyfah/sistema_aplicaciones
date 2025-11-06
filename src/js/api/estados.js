import { CrudManager } from "../helpers/crud.js";
import { mostrarError } from "../helpers/modal.js";

const APP_URL = window.location.pathname.includes("sistema_aplicaciones")
  ? "/sistema_aplicaciones"
  : "";

const estadoManager = new CrudManager({
  entidad: "Estado",
  entidadPlural: "Estados",
  apiBase: `${APP_URL}/api/estados`,
  idCampo: "id_estados",
  tablaId: "tablaEstados",
  modalId: "modalEstado",
  formId: "formEstado",
  btnNuevo: "btnNuevoEstado",
  columnas: [
    (item) => `<td>${item.estados_nombre || ""}</td>`,
    (item) => `<td>${item.estados_descripcion || "Sin descripción"}</td>`,
    (item) => `<td>
      <span class="badge" style="background-color: ${
        item.estados_color || "#3788d8"
      }; color: white;">
        ${item.estados_color || "#3788d8"}
      </span>
    </td>`,
  ],
  camposVer: (item) => `
    <div class="text-left">
      <p><strong>ID:</strong> ${item.id_estados}</p>
      <p><strong>Nombre:</strong> ${item.estados_nombre || ""}</p>
      <p><strong>Descripción:</strong> ${
        item.estados_descripcion || "Sin descripción"
      }</p>
      <p><strong>Color:</strong> 
        <span class="badge" style="background-color: ${
          item.estados_color || "#3788d8"
        }; color: white;">
          ${item.estados_color || "#3788d8"}
        </span>
      </p>
    </div>
  `,
});

estadoManager.generarAccionesActivos = function (id, item) {
  const estadosProtegidos = [
    "Pendiente",
    "En Proceso",
    "Completado",
    "Cancelado",
  ];

  if (estadosProtegidos.includes(item.estados_nombre)) {
    return `
      <div class="btn-group">
        <button type="button" class="btn btn-secondary btn-sm" disabled title="Estado del sistema protegido">
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

const editarOriginal = estadoManager.editar.bind(estadoManager);
estadoManager.editar = async function (id) {
  try {
    const response = await fetch(`${APP_URL}/api/estados?situacion=1`);
    const resultado = await response.json();

    if (resultado.exito) {
      const item = resultado.data.find((i) => i.id_estados == id);

      if (item) {
        const estadosProtegidos = [
          "Pendiente",
          "En Proceso",
          "Completado",
          "Cancelado",
        ];

        if (estadosProtegidos.includes(item.estados_nombre)) {
          mostrarError("No se pueden editar los estados del sistema");
          return;
        }

        editarOriginal(id);
      }
    }
  } catch (error) {
    console.error("Error al verificar estado:", error);
  }
};

const eliminarOriginal = estadoManager.eliminar.bind(estadoManager);
estadoManager.eliminar = async function (id) {
  try {
    const response = await fetch(`${APP_URL}/api/estados?situacion=1`);
    const resultado = await response.json();

    if (resultado.exito) {
      const item = resultado.data.find((i) => i.id_estados == id);

      if (item) {
        const estadosProtegidos = [
          "Pendiente",
          "En Proceso",
          "Completado",
          "Cancelado",
        ];

        if (estadosProtegidos.includes(item.estados_nombre)) {
          mostrarError("No se pueden eliminar los estados del sistema");
          return;
        }

        eliminarOriginal(id);
      }
    }
  } catch (error) {
    console.error("Error al verificar estado:", error);
  }
};

window.estadoManager = estadoManager;

document
  .getElementById("btnVistaGuiada")
  ?.addEventListener("click", function (e) {
    e.preventDefault();

    const steps = [
      {
        intro:
          "¡Bienvenido al módulo de Estados! Gestiona los estados de los proyectos.",
      },
      {
        element: "#tablaEstados",
        intro: "Catálogo completo de estados para proyectos de aplicaciones.",
        position: "top",
      },
      {
        element: "#btnNuevoEstado",
        intro: "Haz clic aquí para crear un nuevo estado personalizado.",
        position: "left",
      },
      {
        intro:
          "Estados protegidos del sistema: Pendiente, En Proceso, Completado, Cancelado.",
      },
      {
        intro:
          "Los estados protegidos tienen un candado y NO se pueden editar ni eliminar.",
      },
      {
        element: "#tablaEstados tbody tr:first-child td:last-child",
        intro:
          "Usa el menú de acciones para ver, editar o eliminar estados personalizados.",
        position: "left",
      },
      {
        intro:
          "¡Listo! Los estados controlan el flujo de vida de los proyectos.",
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
