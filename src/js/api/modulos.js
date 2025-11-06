import { CrudManager } from "../helpers/crud.js";
import { cargarSelect } from "../helpers/selects.js";

const APP_URL = window.location.pathname.includes("sistema_aplicaciones")
  ? "/sistema_aplicaciones"
  : "";

const crudConfig = {
  entidad: "Módulo",
  entidadPlural: "módulos",
  apiBase: `${APP_URL}/api/modulos`,
  tablaId: "tablaModulos",
  formId: "formModulo",
  modalId: "modalModulo",
  btnNuevo: "btnNuevoModulo",
  idCampo: "id_modulos",
  columnas: [
    (item) => `<td><strong>${item.modulos_nombre}</strong></td>`,
    (item) => `<td>${item.modulos_descripcion || "-"}</td>`,
    (item) => `<td>${item.padre_nombre || "Sin padre"}</td>`,
  ],
  camposVer: (item) => `
    <div class="text-left">
      <p><strong>Código:</strong> ${item.modulos_nombre}</p>
      <p><strong>Descripción:</strong> ${item.modulos_descripcion || "-"}</p>
      <p><strong>Módulo Padre:</strong> ${item.padre_nombre || "Sin padre"}</p>
      <p><strong>Situación:</strong> ${
        item.modulos_situacion == 1
          ? '<span class="badge badge-success">Activo</span>'
          : '<span class="badge badge-danger">Inactivo</span>'
      }</p>
    </div>
  `,
  onModalShow: async () => {
    await cargarModulosPadre();
  },
};

async function cargarModulosPadre() {
  try {
    const response = await fetch(`${APP_URL}/api/modulos?situacion=1`);
    const resultado = await response.json();

    if (resultado.exito) {
      const select = document.getElementById("modulo_padre_id");
      if (select) {
        select.innerHTML = '<option value="">-- Sin Padre --</option>';
        resultado.data.forEach((modulo) => {
          select.innerHTML += `<option value="${modulo.id_modulos}">${modulo.modulos_nombre}</option>`;
        });
      }
    }
  } catch (error) {
    console.error("Error al cargar módulos padre:", error);
  }
}

document.addEventListener("DOMContentLoaded", () => {
  new CrudManager(crudConfig);
});

// Vista Guiada - Módulos
document
  .getElementById("btnVistaGuiada")
  ?.addEventListener("click", function (e) {
    e.preventDefault();

    const steps = [
      {
        intro:
          "¡Bienvenido al módulo de Módulos! Aquí gestionas la estructura de módulos del sistema para permisos.",
      },
      {
        element: "#btnNuevoModulo",
        intro: "Haz clic aquí para crear un nuevo módulo.",
        position: "left",
      },
      {
        element: "#tablaModulos",
        intro: "Lista de todos los módulos configurados en el sistema.",
        position: "top",
      },
      {
        element: "#tablaModulos thead th:nth-child(1)",
        intro: "Nombre del módulo (código único).",
        position: "bottom",
      },
      {
        element: "#tablaModulos thead th:nth-child(2)",
        intro: "Descripción del módulo y su funcionalidad.",
        position: "bottom",
      },
      {
        element: "#tablaModulos thead th:nth-child(3)",
        intro: "Módulo padre (para crear jerarquías).",
        position: "bottom",
      },
      {
        element: "#tablaModulos thead th:nth-child(4)",
        intro: "Acciones para gestionar cada módulo.",
        position: "bottom",
      },
      {
        element: "#tablaModulos tbody tr:first-child td:last-child",
        intro: "Usa este menú para ver, editar o eliminar módulos.",
        position: "left",
      },
      {
        intro:
          "Los módulos definen las secciones del sistema para asignar permisos granulares.",
      },
      {
        intro:
          "Puedes crear módulos padres e hijos para organizar los permisos jerárquicamente.",
      },
      {
        intro:
          "¡Listo! La estructura de módulos permite un control detallado de permisos por usuario.",
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
