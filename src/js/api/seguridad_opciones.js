import { CrudManager } from "../helpers/crud.js";

const APP_URL = window.location.pathname.includes("sistema_aplicaciones")
  ? "/sistema_aplicaciones"
  : "";

const seguridadManager = new CrudManager({
  entidad: "Seguridad",
  entidadPlural: "Niveles de Seguridad",
  apiBase: `${APP_URL}/api/seguridad`,
  idCampo: "id_seguridad",
  tablaId: "tablaSeguridad",
  modalId: "modalSeguridad",
  formId: "formSeguridad",
  btnNuevo: "btnNuevoSeguridad",
  columnas: [
    (item) => `<td>${item.seguridad_nombre || ""}</td>`,
    (item) => `<td>${item.seguridad_descripcion || "Sin descripción"}</td>`,
    (item) => `<td>
      <span class="badge badge-warning">
        ${parseFloat(item.seguridad_factor || 1).toFixed(2)}x
      </span>
    </td>`,
  ],
  camposVer: (item) => `
    <div class="text-left">
      <p><strong>ID:</strong> ${item.id_seguridad}</p>
      <p><strong>Nombre:</strong> ${item.seguridad_nombre || ""}</p>
      <p><strong>Descripción:</strong> ${
        item.seguridad_descripcion || "Sin descripción"
      }</p>
      <p><strong>Factor:</strong> 
        <span class="badge badge-warning">
          ${parseFloat(item.seguridad_factor || 1).toFixed(2)}x
        </span>
      </p>
      <p class="text-muted mb-0">
        <small>Este factor se aplica al costo para reflejar los requerimientos de seguridad.</small>
      </p>
    </div>
  `,
});

window.seguridadManager = seguridadManager;

// Vista Guiada - Seguridad
document
  .getElementById("btnVistaGuiada")
  ?.addEventListener("click", function (e) {
    e.preventDefault();

    const steps = [
      {
        intro:
          "¡Bienvenido al módulo de Seguridad! Aquí gestionas los niveles de seguridad para el cálculo de costos.",
      },
      {
        element: "#btnNuevoSeguridad",
        intro: "Haz clic aquí para crear un nuevo nivel de seguridad.",
        position: "left",
      },
      {
        element: "#tablaSeguridad",
        intro: "Lista de todos los niveles de seguridad configurados.",
        position: "top",
      },
      {
        element: "#tablaSeguridad thead th:nth-child(1)",
        intro: "Nombre del nivel de seguridad.",
        position: "bottom",
      },
      {
        element: "#tablaSeguridad thead th:nth-child(2)",
        intro: "Descripción de los requerimientos de seguridad.",
        position: "bottom",
      },
      {
        element: "#tablaSeguridad thead th:nth-child(3)",
        intro: "Factor multiplicador que afecta el costo final.",
        position: "bottom",
      },
      {
        element: "#tablaSeguridad thead th:nth-child(4)",
        intro: "Acciones para gestionar cada nivel.",
        position: "bottom",
      },
      {
        element: "#tablaSeguridad tbody tr:first-child td:last-child",
        intro:
          "Usa este menú para ver, editar o eliminar niveles de seguridad.",
        position: "left",
      },
      {
        intro:
          "Ejemplo: Un factor de 1.25x aumenta el costo en un 25% por medidas de seguridad adicionales.",
      },
      {
        intro:
          "¡Listo! Los factores de seguridad ayudan a presupuestar adecuadamente los requerimientos de protección.",
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
