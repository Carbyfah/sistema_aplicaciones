import { CrudManager } from "../helpers/crud.js";

const APP_URL = window.location.pathname.includes("sistema_aplicaciones")
  ? "/sistema_aplicaciones"
  : "";

const camposManager = new CrudManager({
  entidad: "Campo",
  entidadPlural: "Campos",
  apiBase: `${APP_URL}/api/campos`,
  idCampo: "id_aplicacion_campos",
  tablaId: "tablaCampos",
  modalId: "modalCampo",
  formId: "formCampo",
  btnNuevo: "btnNuevoCampo",
  columnas: [
    (item) => `<td><code>${item.tablas_nombre || ""}</code></td>`,
    (item) => `<td><strong>${item.campos_nombre || ""}</strong></td>`,
    (item) => `<td><code>${item.tipos_dato_nombre || ""}</code></td>`,
    (item) => `<td>${item.campos_longitud || "-"}</td>`,
    (item) => `<td>
      ${
        item.campos_nulo == 1
          ? '<span class="badge badge-warning">YES</span>'
          : '<span class="badge badge-secondary">NO</span>'
      }
    </td>`,
    (item) => `<td>
      ${
        item.tipos_clave_nombre
          ? `<span class="badge badge-primary">${item.tipos_clave_nombre}</span>`
          : '<span class="text-muted">-</span>'
      }
    </td>`,
  ],
  camposVer: (item) => `
    <div class="text-left">
      <p><strong>Tabla:</strong> <code>${item.tablas_nombre || ""}</code></p>
      <hr>
      <p><strong>Campo:</strong> <code>${item.campos_nombre || ""}</code></p>
      <p><strong>Tipo de dato:</strong> <code>${
        item.tipos_dato_nombre || ""
      }</code></p>
      <p><strong>Longitud:</strong> ${
        item.campos_longitud || "No especificada"
      }</p>
      <p><strong>Permite NULL:</strong> ${
        item.campos_nulo == 1 ? "Sí" : "No"
      }</p>
      <p><strong>Tipo de clave:</strong> ${
        item.tipos_clave_nombre || "Sin clave"
      }</p>
      ${
        item.campos_descripcion
          ? `<hr><p><strong>Descripción:</strong><br>${item.campos_descripcion}</p>`
          : ""
      }
    </div>
  `,
});

window.camposManager = camposManager;

document
  .getElementById("btnVistaGuiada")
  ?.addEventListener("click", function (e) {
    e.preventDefault();

    const steps = [
      {
        intro:
          "¡Bienvenido al módulo de Campos! Gestiona los campos de las tablas de la base de datos.",
      },
      {
        element: "#tablaCampos",
        intro:
          "Aquí se muestran todos los campos registrados en las tablas del sistema.",
        position: "top",
      },
      {
        element: "#btnNuevoCampo",
        intro: "Haz clic aquí para crear un nuevo campo en una tabla.",
        position: "left",
      },
      {
        element: "#tablaCampos thead th:nth-child(1)",
        intro: "Nombre de la tabla a la que pertenece el campo.",
        position: "bottom",
      },
      {
        element: "#tablaCampos thead th:nth-child(2)",
        intro: "Nombre del campo en la base de datos.",
        position: "bottom",
      },
      {
        element: "#tablaCampos thead th:nth-child(3)",
        intro: "Tipo de dato del campo (INT, VARCHAR, DATE, etc.).",
        position: "bottom",
      },
      {
        element: "#tablaCampos thead th:nth-child(4)",
        intro: "Longitud o tamaño del campo cuando aplica.",
        position: "bottom",
      },
      {
        element: "#tablaCampos thead th:nth-child(5)",
        intro: "Indica si el campo permite valores NULL.",
        position: "bottom",
      },
      {
        element: "#tablaCampos thead th:nth-child(6)",
        intro: "Tipo de clave (Primaria, Foránea, etc.).",
        position: "bottom",
      },
      {
        element: "#tablaCampos tbody tr:first-child td:last-child",
        intro: "Usa el menú de acciones para ver, editar o eliminar campos.",
        position: "left",
      },
      {
        intro:
          "¡Listo! Ahora puedes gestionar la estructura de la base de datos.",
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
