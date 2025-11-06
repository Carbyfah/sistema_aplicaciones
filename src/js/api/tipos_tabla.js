import { CrudManager } from "../helpers/crud.js";

const APP_URL = window.location.pathname.includes("sistema_aplicaciones")
  ? "/sistema_aplicaciones"
  : "";

const tiposTablaManager = new CrudManager({
  entidad: "Tipo de Tabla",
  entidadPlural: "Tipos de Tabla",
  apiBase: `${APP_URL}/api/tipos-tabla`,
  idCampo: "id_tipo_tabla",
  tablaId: "tablaTiposTabla",
  modalId: "modalTipoTabla",
  formId: "formTipoTabla",
  btnNuevo: "btnNuevoTipoTabla",
  columnas: [
    (item) => `<td>${item.tipos_tabla_nombre || ""}</td>`,
    (item) => `<td>${item.tipos_tabla_descripcion || "Sin descripción"}</td>`,
  ],
  camposVer: (item) => `
    <div class="text-left">
      <p><strong>ID:</strong> ${item.id_tipo_tabla}</p>
      <p><strong>Nombre:</strong> ${item.tipos_tabla_nombre || ""}</p>
      <p><strong>Descripción:</strong> ${
        item.tipos_tabla_descripcion || "Sin descripción"
      }</p>
    </div>
  `,
});

window.tiposTablaManager = tiposTablaManager;

// Vista Guiada - Tipos de Tabla
document
  .getElementById("btnVistaGuiada")
  ?.addEventListener("click", function (e) {
    e.preventDefault();

    const steps = [
      {
        intro:
          "¡Bienvenido al módulo de Tipos de Tabla! Aquí gestionas las clasificaciones de tablas para la base de datos.",
      },
      {
        element: "#btnNuevoTipoTabla",
        intro: "Haz clic aquí para crear un nuevo tipo de tabla.",
        position: "left",
      },
      {
        element: "#tablaTiposTabla",
        intro: "Lista de todos los tipos de tabla configurados en el sistema.",
        position: "top",
      },
      {
        element: "#tablaTiposTabla thead th:nth-child(1)",
        intro:
          "Nombre del tipo de tabla (Ej: Maestra, Transaccional, Configuración).",
        position: "bottom",
      },
      {
        element: "#tablaTiposTabla thead th:nth-child(2)",
        intro: "Descripción del propósito y uso de este tipo de tabla.",
        position: "bottom",
      },
      {
        element: "#tablaTiposTabla thead th:nth-child(3)",
        intro: "Acciones para gestionar cada tipo de tabla.",
        position: "bottom",
      },
      {
        element: "#tablaTiposTabla tbody tr:first-child td:last-child",
        intro: "Usa este menú para ver, editar o eliminar tipos de tabla.",
        position: "left",
      },
      {
        intro:
          "Ejemplos de tipos: Maestra (datos base), Transaccional (movimientos), Configuración (parámetros).",
      },
      {
        intro:
          "¡Listo! Los tipos de tabla ayudan a organizar y clasificar la estructura de la base de datos.",
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
