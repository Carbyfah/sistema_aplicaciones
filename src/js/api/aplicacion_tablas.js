import { CrudManager } from "../helpers/crud.js";

const APP_URL = window.location.pathname.includes("sistema_aplicaciones")
  ? "/sistema_aplicaciones"
  : "";

const tablasManager = new CrudManager({
  entidad: "Tabla",
  entidadPlural: "Tablas",
  apiBase: `${APP_URL}/api/tablas`,
  idCampo: "id_aplicacion_tablas",
  tablaId: "tablaAplicacionTablas",
  modalId: "modalTabla",
  formId: "formTabla",
  btnNuevo: "btnNuevaTabla",
  columnas: [
    (item) => `<td>${item.aplicacion_nombre || "Sin aplicación"}</td>`,
    (item) => `<td><code>${item.tablas_nombre || ""}</code></td>`,
    (item) => `<td>
      ${
        item.tipos_tabla_nombre
          ? `<span class="badge badge-info">${item.tipos_tabla_nombre}</span>`
          : '<span class="text-muted">Sin tipo</span>'
      }
    </td>`,
    (item) => `<td>${item.tablas_descripcion || "Sin descripción"}</td>`,
  ],
  camposVer: (item) => `
    <div class="text-left">
      <p><strong>Aplicación:</strong> ${
        item.aplicacion_nombre || "Sin aplicación"
      }</p>
      <p><strong>Nombre:</strong> <code>${item.tablas_nombre || ""}</code></p>
      <p><strong>Tipo:</strong> ${item.tipos_tabla_nombre || "Sin tipo"}</p>
      <p><strong>Descripción:</strong> ${
        item.tablas_descripcion || "Sin descripción"
      }</p>
      ${
        item.total_campos
          ? `<hr><p><strong>Total de campos:</strong> <span class="badge badge-success">${item.total_campos}</span></p>`
          : ""
      }
    </div>
  `,
});

window.tablasManager = tablasManager;

// Vista Guiada - Tablas
document
  .getElementById("btnVistaGuiada")
  ?.addEventListener("click", function (e) {
    e.preventDefault();

    const steps = [
      {
        intro:
          "¡Bienvenido al módulo de Tablas! Aquí gestionas las tablas de la base de datos para cada aplicación.",
      },
      {
        element: "#btnNuevaTabla",
        intro: "Haz clic aquí para crear una nueva tabla.",
        position: "left",
      },
      {
        element: "#tablaAplicacionTablas",
        intro: "Lista de todas las tablas registradas en el sistema.",
        position: "top",
      },
      {
        element: "#tablaAplicacionTablas thead th:nth-child(1)",
        intro: "Aplicación a la que pertenece la tabla.",
        position: "bottom",
      },
      {
        element: "#tablaAplicacionTablas thead th:nth-child(2)",
        intro: "Nombre de la tabla en la base de datos.",
        position: "bottom",
      },
      {
        element: "#tablaAplicacionTablas thead th:nth-child(3)",
        intro: "Tipo de tabla (Maestra, Transaccional, etc.).",
        position: "bottom",
      },
      {
        element: "#tablaAplicacionTablas thead th:nth-child(4)",
        intro: "Descripción del propósito de la tabla.",
        position: "bottom",
      },
      {
        element: "#tablaAplicacionTablas thead th:nth-child(5)",
        intro: "Acciones para gestionar cada tabla.",
        position: "bottom",
      },
      {
        element: "#tablaAplicacionTablas tbody tr:first-child td:last-child",
        intro:
          "Usa este menú para ver, editar, eliminar o gestionar los campos de la tabla.",
        position: "left",
      },
      {
        intro:
          "Después de crear una tabla, puedes agregarle campos en el módulo 'Campos'.",
      },
      {
        intro:
          "¡Listo! Las tablas definen la estructura de datos de cada aplicación.",
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
