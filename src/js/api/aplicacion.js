import { CrudManager } from "../helpers/crud.js";
import introJs from "intro.js";

const APP_URL = window.location.pathname.includes("sistema_aplicaciones")
  ? "/sistema_aplicaciones"
  : "";

const crudProyectos = new CrudManager({
  apiBase: `${APP_URL}/api/proyectos`,
  formId: "formProyecto",
  tablaId: "tablaProyectos",
  modalId: "modalProyecto",
  btnNuevo: "btnNuevoProyecto",
  entidad: "Proyecto",
  entidadPlural: "Proyectos",
  idCampo: "id_aplicacion",
  columnas: [
    (item) => `<td>${item.aplicacion_nombre || ""}</td>`,
    (item) => `<td>${item.aplicacion_desc_corta || ""}</td>`,
    (item) =>
      `<td><span class="badge badge-${
        item.aplicacion_situacion == 1 ? "success" : "danger"
      }">${item.aplicacion_situacion == 1 ? "Activo" : "Inactivo"}</span></td>`,
  ],
  camposVer: (item) => `
    <div class="text-left">
      <p><strong>Nombre:</strong> ${item.aplicacion_nombre || ""}</p>
      <p><strong>Descripción Corta:</strong> ${
        item.aplicacion_desc_corta || ""
      }</p>
      <p><strong>Descripción Larga:</strong> ${
        item.aplicacion_larga || "N/A"
      }</p>
    </div>
  `,
});

window.crudProyectos = crudProyectos;

// VISTA GUIADA - Listener directo al botón
document
  .getElementById("btnVistaGuiada")
  ?.addEventListener("click", function (e) {
    e.preventDefault();

    introJs()
      .setOptions({
        steps: [
          {
            intro:
              "Bienvenido al módulo de Proyectos! Gestiona tus proyectos de desarrollo de aplicaciones.",
          },
          {
            element: "#btnNuevoProyecto",
            intro:
              "Haz clic aquí para crear un nuevo proyecto con su nombre, descripción y detalles.",
            position: "left",
          },
          {
            element: "#tablaProyectos",
            intro:
              "Aquí se muestran todos los proyectos registrados. Puedes buscar, ordenar y filtrar.",
            position: "top",
          },
          {
            element: "#tablaProyectos tbody tr:first-child td:last-child",
            intro:
              "Usa el menú de acciones para ver, editar o eliminar proyectos.",
            position: "left",
          },
          {
            element: "#toggleEliminados",
            intro:
              "Activa este switch para ver y recuperar proyectos eliminados.",
            position: "left",
          },
          {
            intro:
              "Listo! Ahora puedes gestionar tus proyectos de forma eficiente.",
          },
        ],
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
