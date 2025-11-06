import { CrudManager } from "../helpers/crud.js";

const APP_URL = window.location.pathname.includes("sistema_aplicaciones")
  ? "/sistema_aplicaciones"
  : "";

const tiposDatoManager = new CrudManager({
  entidad: "Tipo de Dato",
  entidadPlural: "Tipos de Dato",
  apiBase: `${APP_URL}/api/tipos-dato`,
  idCampo: "id_tipo_dato",
  tablaId: "tablaTiposDato",
  modalId: "modalTipoDato",
  formId: "formTipoDato",
  btnNuevo: "btnNuevoTipoDato",
  columnas: [
    (item) => `<td><code>${item.tipos_dato_nombre || ""}</code></td>`,
    (item) => `<td>${item.tipos_dato_descripcion || "Sin descripción"}</td>`,
  ],
  camposVer: (item) => `
    <div class="text-left">
      <p><strong>ID:</strong> ${item.id_tipo_dato}</p>
      <p><strong>Nombre:</strong> <code>${
        item.tipos_dato_nombre || ""
      }</code></p>
      <p><strong>Descripción:</strong> ${
        item.tipos_dato_descripcion || "Sin descripción"
      }</p>
    </div>
  `,
});

window.tiposDatoManager = tiposDatoManager;

// Vista Guiada - Tipos de Dato
document
  .getElementById("btnVistaGuiada")
  ?.addEventListener("click", function (e) {
    e.preventDefault();

    const steps = [
      {
        intro:
          "¡Bienvenido al módulo de Tipos de Dato! Aquí gestionas los tipos de datos para los campos de la base de datos.",
      },
      {
        element: "#btnNuevoTipoDato",
        intro: "Haz clic aquí para crear un nuevo tipo de dato.",
        position: "left",
      },
      {
        element: "#tablaTiposDato",
        intro: "Lista de todos los tipos de dato configurados en el sistema.",
        position: "top",
      },
      {
        element: "#tablaTiposDato thead th:nth-child(1)",
        intro: "Nombre del tipo de dato (Ej: INT, VARCHAR, DATE, BOOLEAN).",
        position: "bottom",
      },
      {
        element: "#tablaTiposDato thead th:nth-child(2)",
        intro: "Descripción del propósito y uso de este tipo de dato.",
        position: "bottom",
      },
      {
        element: "#tablaTiposDato thead th:nth-child(3)",
        intro: "Acciones para gestionar cada tipo de dato.",
        position: "bottom",
      },
      {
        element: "#tablaTiposDato tbody tr:first-child td:last-child",
        intro: "Usa este menú para ver, editar o eliminar tipos de dato.",
        position: "left",
      },
      {
        intro:
          "Ejemplos: INT (números enteros), VARCHAR (texto), DATE (fechas), BOOLEAN (verdadero/falso).",
      },
      {
        intro:
          "¡Listo! Los tipos de dato definen qué información puede almacenar cada campo de la base de datos.",
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
