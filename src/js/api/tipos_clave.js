import { CrudManager } from "../helpers/crud.js";

const APP_URL = window.location.pathname.includes("sistema_aplicaciones")
  ? "/sistema_aplicaciones"
  : "";

const tiposClaveManager = new CrudManager({
  entidad: "Tipo de Clave",
  entidadPlural: "Tipos de Clave",
  apiBase: `${APP_URL}/api/tipos-clave`,
  idCampo: "id_tipo_clave",
  tablaId: "tablaTiposClave",
  modalId: "modalTipoClave",
  formId: "formTipoClave",
  btnNuevo: "btnNuevoTipoClave",
  columnas: [
    (item) =>
      `<td><span class="badge badge-secondary">${
        item.tipos_clave_nombre || ""
      }</span></td>`,
    (item) => `<td>${item.tipos_clave_descripcion || "Sin descripción"}</td>`,
  ],
  camposVer: (item) => `
    <div class="text-left">
      <p><strong>ID:</strong> ${item.id_tipo_clave}</p>
      <p><strong>Nombre:</strong> <span class="badge badge-secondary">${
        item.tipos_clave_nombre || ""
      }</span></p>
      <p><strong>Descripción:</strong> ${
        item.tipos_clave_descripcion || "Sin descripción"
      }</p>
    </div>
  `,
});

window.tiposClaveManager = tiposClaveManager;

// Vista Guiada - Tipos de Clave
document
  .getElementById("btnVistaGuiada")
  ?.addEventListener("click", function (e) {
    e.preventDefault();

    const steps = [
      {
        intro:
          "¡Bienvenido al módulo de Tipos de Clave! Aquí gestionas los tipos de claves para la base de datos.",
      },
      {
        element: "#btnNuevoTipoClave",
        intro: "Haz clic aquí para crear un nuevo tipo de clave.",
        position: "left",
      },
      {
        element: "#tablaTiposClave",
        intro: "Lista de todos los tipos de clave configurados en el sistema.",
        position: "top",
      },
      {
        element: "#tablaTiposClave thead th:nth-child(1)",
        intro: "Nombre del tipo de clave (Ej: Primaria, Foránea, Única).",
        position: "bottom",
      },
      {
        element: "#tablaTiposClave thead th:nth-child(2)",
        intro: "Descripción del propósito y uso de este tipo de clave.",
        position: "bottom",
      },
      {
        element: "#tablaTiposClave thead th:nth-child(3)",
        intro: "Acciones para gestionar cada tipo de clave.",
        position: "bottom",
      },
      {
        element: "#tablaTiposClave tbody tr:first-child td:last-child",
        intro: "Usa este menú para ver, editar o eliminar tipos de clave.",
        position: "left",
      },
      {
        intro:
          "Ejemplos: Primaria (identificador único), Foránea (relaciones entre tablas), Única (valores no repetidos).",
      },
      {
        intro:
          "¡Listo! Los tipos de clave definen las relaciones e integridad de la base de datos.",
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
