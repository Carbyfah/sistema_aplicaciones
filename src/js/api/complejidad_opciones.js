import { CrudManager } from "../helpers/crud.js";

const APP_URL = window.location.pathname.includes("sistema_aplicaciones")
  ? "/sistema_aplicaciones"
  : "";

const complejidadManager = new CrudManager({
  entidad: "Complejidad",
  entidadPlural: "Complejidades",
  apiBase: `${APP_URL}/api/complejidad`,
  idCampo: "id_complejidad",
  tablaId: "tablaComplejidad",
  modalId: "modalComplejidad",
  formId: "formComplejidad",
  btnNuevo: "btnNuevoComplejidad",
  columnas: [
    (item) => `<td>${item.complejidad_nombre || ""}</td>`,
    (item) => `<td>${item.complejidad_descripcion || "Sin descripción"}</td>`,
    (item) => `<td>
      <span class="badge badge-info">
        ${parseFloat(item.complejidad_factor || 1).toFixed(2)}x
      </span>
    </td>`,
  ],
  camposVer: (item) => `
    <div class="text-left">
      <p><strong>ID:</strong> ${item.id_complejidad}</p>
      <p><strong>Nombre:</strong> ${item.complejidad_nombre || ""}</p>
      <p><strong>Descripción:</strong> ${
        item.complejidad_descripcion || "Sin descripción"
      }</p>
      <p><strong>Factor:</strong> 
        <span class="badge badge-info">
          ${parseFloat(item.complejidad_factor || 1).toFixed(2)}x
        </span>
      </p>
      <p class="text-muted mb-0">
        <small>Este factor se multiplica por el costo base para obtener el costo final.</small>
      </p>
    </div>
  `,
});

window.complejidadManager = complejidadManager;

// Vista Guiada - Complejidad
document
  .getElementById("btnVistaGuiada")
  ?.addEventListener("click", function (e) {
    e.preventDefault();

    const steps = [
      {
        intro:
          "¡Bienvenido al módulo de Complejidad! Aquí gestionas los niveles de complejidad para calcular costos.",
      },
      {
        element: "#btnNuevoComplejidad",
        intro: "Haz clic aquí para crear un nuevo nivel de complejidad.",
        position: "left",
      },
      {
        element: "#tablaComplejidad",
        intro: "Lista de todos los niveles de complejidad configurados.",
        position: "top",
      },
      {
        element: "#tablaComplejidad thead th:nth-child(1)",
        intro: "Nombre del nivel de complejidad.",
        position: "bottom",
      },
      {
        element: "#tablaComplejidad thead th:nth-child(2)",
        intro: "Descripción detallada del nivel.",
        position: "bottom",
      },
      {
        element: "#tablaComplejidad thead th:nth-child(3)",
        intro: "Factor multiplicador que se aplica al costo base.",
        position: "bottom",
      },
      {
        element: "#tablaComplejidad thead th:nth-child(4)",
        intro: "Acciones para gestionar cada nivel.",
        position: "bottom",
      },
      {
        element: "#tablaComplejidad tbody tr:first-child td:last-child",
        intro:
          "Usa este menú para ver, editar o eliminar niveles de complejidad.",
        position: "left",
      },
      {
        intro: "Ejemplo: Un factor de 1.50x aumenta el costo base en un 50%.",
      },
      {
        intro:
          "¡Listo! Los factores de complejidad ayudan a calcular costos más precisos para los proyectos.",
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
