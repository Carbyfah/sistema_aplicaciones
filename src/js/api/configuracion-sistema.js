import { CrudManager } from "../helpers/crud.js";
import { mostrarError } from "../helpers/modal.js";

const APP_URL = window.location.pathname.includes("sistema_aplicaciones")
  ? "/sistema_aplicaciones"
  : "";

const configuracionManager = new CrudManager({
  entidad: "Configuración",
  entidadPlural: "configuraciones",
  apiBase: `${APP_URL}/api/sistema`,
  idCampo: "id_configuracion_sistema",
  tablaId: "tablaConfiguraciones",
  modalId: "modalConfiguracion",
  formId: "formConfiguracion",
  btnNuevo: "btnNuevaConfiguracion",
  columnas: [
    (item) => `<td><code>${item.configuracion_sistema_clave}</code></td>`,
    (item) => `<td>
            ${
              item.configuracion_sistema_tipo === "bool"
                ? item.configuracion_sistema_valor === "1" ||
                  item.configuracion_sistema_valor === "true"
                  ? '<span class="badge badge-success">true</span>'
                  : '<span class="badge badge-danger">false</span>'
                : item.configuracion_sistema_valor
            }
        </td>`,
    (item) => `<td>${item.configuracion_sistema_tipo}</td>`,
    (item) =>
      `<td>${item.configuracion_sistema_descripcion || "Sin descripción"}</td>`,
  ],
  camposVer: (item) => `
        <div class="text-left">
            <p><strong>ID:</strong> ${item.id_configuracion_sistema}</p>
            <p><strong>Clave:</strong> <code>${
              item.configuracion_sistema_clave
            }</code></p>
            <p><strong>Valor:</strong> 
                ${
                  item.configuracion_sistema_tipo === "bool"
                    ? item.configuracion_sistema_valor === "1" ||
                      item.configuracion_sistema_valor === "true"
                      ? '<span class="badge badge-success">true</span>'
                      : '<span class="badge badge-danger">false</span>'
                    : item.configuracion_sistema_valor
                }
            </p>
            <p><strong>Tipo:</strong> ${item.configuracion_sistema_tipo}</p>
            <p><strong>Descripción:</strong> ${
              item.configuracion_sistema_descripcion || "Sin descripción"
            }</p>
        </div>
    `,
  beforeSave: (datos) => {
    if (datos.configuracion_sistema_clave) {
      datos.configuracion_sistema_clave =
        datos.configuracion_sistema_clave.toUpperCase();
    }

    if (datos.configuracion_sistema_tipo === "bool") {
      datos.configuracion_sistema_valor =
        datos.configuracion_sistema_valor === "true" ||
        datos.configuracion_sistema_valor === "1" ||
        datos.configuracion_sistema_valor === "on"
          ? "1"
          : "0";
    }

    return datos;
  },
});

const configuracionesProtegidas = [
  "SISTEMA_NOMBRE",
  "SISTEMA_VERSION",
  "SISTEMA_ADMIN_EMAIL",
  "SISTEMA_URL",
];

configuracionManager.generarAccionesActivos = function (id, item) {
  if (configuracionesProtegidas.includes(item.configuracion_sistema_clave)) {
    return `
            <div class="btn-group">
                <button type="button" class="btn btn-secondary btn-sm" disabled title="Configuración del sistema protegida">
                    <i class="fas fa-lock"></i>
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
                    <i class="bi bi-eye text-primary"></i> Ver
                </a>
                <a class="dropdown-item" href="#" data-accion="editar" data-id="${id}">
                    <i class="bi bi-pencil text-info"></i> Editar
                </a>
                <a class="dropdown-item" href="#" data-accion="eliminar" data-id="${id}">
                    <i class="bi bi-trash text-danger"></i> Eliminar
                </a>
            </div>
        </div>
    `;
};

const editarOriginal = configuracionManager.editar.bind(configuracionManager);
configuracionManager.editar = function (id) {
  fetch(`${APP_URL}/api/sistema?situacion=1`)
    .then((response) => response.json())
    .then((resultado) => {
      if (resultado.exito) {
        const configuracion = resultado.data.find(
          (c) => c.id_configuracion_sistema == id
        );
        if (
          configuracion &&
          configuracionesProtegidas.includes(
            configuracion.configuracion_sistema_clave
          )
        ) {
          mostrarError(
            "No se pueden editar las configuraciones base del sistema"
          );
          return;
        }
        editarOriginal(id);
      }
    });
};

const eliminarOriginal =
  configuracionManager.eliminar.bind(configuracionManager);
configuracionManager.eliminar = function (id) {
  fetch(`${APP_URL}/api/sistema?situacion=1`)
    .then((response) => response.json())
    .then((resultado) => {
      if (resultado.exito) {
        const configuracion = resultado.data.find(
          (c) => c.id_configuracion_sistema == id
        );
        if (
          configuracion &&
          configuracionesProtegidas.includes(
            configuracion.configuracion_sistema_clave
          )
        ) {
          mostrarError(
            "No se pueden eliminar las configuraciones base del sistema"
          );
          return;
        }
        eliminarOriginal(id);
      }
    });
};
