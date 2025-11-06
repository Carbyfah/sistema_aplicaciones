import { CrudManager } from "../helpers/crud.js";

const APP_URL = window.location.pathname.includes("sistema_aplicaciones")
  ? "/sistema_aplicaciones"
  : "";

const costosManager = new CrudManager({
  entidad: "Costo",
  entidadPlural: "Costos",
  apiBase: `${APP_URL}/api/costos`,
  idCampo: "id_aplicacion_costos",
  tablaId: "tablaCostos",
  modalId: "modalCosto",
  formId: "formCosto",
  btnNuevo: "btnNuevoCosto",
  columnas: [
    (item) => `<td>${item.aplicacion_nombre || "Sin aplicación"}</td>`,
    (item) =>
      `<td>${parseFloat(item.costos_horas_estimadas || 0).toFixed(2)}h</td>`,
    (item) =>
      `<td>$ ${parseFloat(item.costos_tarifa_hora || 0).toFixed(2)}</td>`,
    (item) => `<td>
      ${
        item.complejidad_nombre
          ? `<span class="badge badge-info">${
              item.complejidad_nombre
            } (${parseFloat(item.complejidad_factor).toFixed(2)}x)</span>`
          : '<span class="text-muted">Sin complejidad</span>'
      }
    </td>`,
    (item) => `<td>
      ${
        item.seguridad_nombre
          ? `<span class="badge badge-warning">${
              item.seguridad_nombre
            } (${parseFloat(item.seguridad_factor).toFixed(2)}x)</span>`
          : '<span class="text-muted">Sin seguridad</span>'
      }
    </td>`,
    (item) =>
      `<td><strong>$ ${parseFloat(item.costos_total || 0).toFixed(
        2
      )}</strong></td>`,
  ],
  camposVer: (item) => `
    <div class="text-left">
      <p><strong>Aplicación:</strong> ${
        item.aplicacion_nombre || "Sin aplicación"
      }</p>
      <hr>
      <p><strong>Horas estimadas:</strong> ${parseFloat(
        item.costos_horas_estimadas || 0
      ).toFixed(2)} horas</p>
      <p><strong>Tarifa por hora:</strong> $ ${parseFloat(
        item.costos_tarifa_hora || 0
      ).toFixed(2)}</p>
      <p><strong>Costo base:</strong> $ ${(
        parseFloat(item.costos_horas_estimadas || 0) *
        parseFloat(item.costos_tarifa_hora || 0)
      ).toFixed(2)}</p>
      <hr>
      <p><strong>Complejidad:</strong> ${
        item.complejidad_nombre || "Sin complejidad"
      } ${
    item.complejidad_factor
      ? `(${parseFloat(item.complejidad_factor).toFixed(2)}x)`
      : ""
  }</p>
      <p><strong>Seguridad:</strong> ${
        item.seguridad_nombre || "Sin seguridad"
      } ${
    item.seguridad_factor
      ? `(${parseFloat(item.seguridad_factor).toFixed(2)}x)`
      : ""
  }</p>
      <hr>
      <p><strong>TOTAL:</strong> <span class="badge badge-success badge-lg">$ ${parseFloat(
        item.costos_total || 0
      ).toFixed(2)}</span></p>
      ${
        item.costos_notas
          ? `<hr><p><strong>Notas:</strong><br>${item.costos_notas}</p>`
          : ""
      }
    </div>
  `,
});

function calcularCostoTotal() {
  const horas =
    parseFloat(document.getElementById("costos_horas_estimadas").value) || 0;
  const tarifa =
    parseFloat(document.getElementById("costos_tarifa_hora").value) || 0;

  const complejidadSelect = document.getElementById("complejidad_id");
  const complejidadFactor =
    complejidadSelect.options[complejidadSelect.selectedIndex]?.dataset
      .factor || 1;

  const seguridadSelect = document.getElementById("seguridad_id");
  const seguridadFactor =
    seguridadSelect.options[seguridadSelect.selectedIndex]?.dataset.factor || 1;

  const costoBase = horas * tarifa;
  const costoTotal =
    costoBase * parseFloat(complejidadFactor) * parseFloat(seguridadFactor);

  document.getElementById("costos_total_preview").value = costoTotal.toFixed(2);
}

document
  .getElementById("costos_horas_estimadas")
  ?.addEventListener("input", calcularCostoTotal);
document
  .getElementById("costos_tarifa_hora")
  ?.addEventListener("input", calcularCostoTotal);
document
  .getElementById("complejidad_id")
  ?.addEventListener("change", calcularCostoTotal);
document
  .getElementById("seguridad_id")
  ?.addEventListener("change", calcularCostoTotal);

document
  .getElementById("modalCosto")
  ?.addEventListener("shown.bs.modal", () => {
    calcularCostoTotal();
  });

window.costosManager = costosManager;

// Vista Guiada - Gestión de Costos
document
  .getElementById("btnVistaGuiada")
  ?.addEventListener("click", function (e) {
    e.preventDefault();

    const steps = [
      {
        intro:
          "¡Bienvenido al módulo de Gestión de Costos! Aquí calculas y gestionas los costos de los proyectos.",
      },
      {
        element: "#btnNuevoCosto",
        intro: "Haz clic aquí para crear un nuevo cálculo de costo.",
        position: "left",
      },
      {
        element: "#tablaCostos",
        intro: "Lista de todos los cálculos de costos realizados.",
        position: "top",
      },
      {
        element: "#tablaCostos thead th:nth-child(1)",
        intro: "Nombre de la aplicación/proyecto.",
        position: "bottom",
      },
      {
        element: "#tablaCostos thead th:nth-child(2)",
        intro: "Horas estimadas para el desarrollo.",
        position: "bottom",
      },
      {
        element: "#tablaCostos thead th:nth-child(3)",
        intro: "Tarifa por hora aplicada.",
        position: "bottom",
      },
      {
        element: "#tablaCostos thead th:nth-child(4)",
        intro: "Nivel de complejidad y su factor multiplicador.",
        position: "bottom",
      },
      {
        element: "#tablaCostos thead th:nth-child(5)",
        intro: "Nivel de seguridad y su factor multiplicador.",
        position: "bottom",
      },
      {
        element: "#tablaCostos thead th:nth-child(6)",
        intro: "Costo total calculado automáticamente.",
        position: "bottom",
      },
      {
        element: "#tablaCostos thead th:nth-child(7)",
        intro: "Acciones para gestionar cada cálculo.",
        position: "bottom",
      },
      {
        element: "#formCosto",
        intro:
          "Al crear/editar costos, el sistema calcula automáticamente el total basado en horas, tarifa, complejidad y seguridad.",
        position: "top",
      },
      {
        intro:
          "¡Listo! El sistema calcula automáticamente: (Horas × Tarifa) × Complejidad × Seguridad = Costo Total",
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
