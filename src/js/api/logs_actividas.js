import { initDataTable } from "../helpers/tablas.js";
import { exportarAExcel } from "../helpers/exportaciones.js";

const APP_URL = window.location.pathname.includes("sistema_aplicaciones")
  ? "/sistema_aplicaciones"
  : "";

let datatable = null;

async function cargarLogs() {
  try {
    const response = await fetch(`${APP_URL}/api/logs`);
    const resultado = await response.json();

    if (resultado.exito) {
      renderizarTabla(resultado.data);
      cargarFiltros(resultado.data);
    }
  } catch (error) {
    console.error("Error al cargar logs:", error);
  }
}

function cargarFiltros(datos) {
  const tablas = [...new Set(datos.map((item) => item.logs_actividad_tabla))];
  const usuarios = [
    ...new Set(
      datos.map((item) =>
        item.persona_nombres
          ? `${item.persona_nombres} ${item.persona_apellidos}`
          : "Sin usuario"
      )
    ),
  ];

  const selectTabla = document.getElementById("filtroTabla");
  const selectUsuario = document.getElementById("filtroUsuario");

  if (selectTabla) {
    tablas.forEach((tabla) => {
      const option = document.createElement("option");
      option.value = tabla;
      option.textContent = tabla;
      selectTabla.appendChild(option);
    });
  }

  if (selectUsuario) {
    usuarios.forEach((usuario) => {
      const option = document.createElement("option");
      option.value = usuario;
      option.textContent = usuario;
      selectUsuario.appendChild(option);
    });
  }
}

function renderizarTabla(datos) {
  const tbody = document.querySelector("#tablaLogs tbody");

  if (!tbody) return;

  if (datatable) {
    datatable.destroy();
    datatable = null;
  }

  tbody.innerHTML = "";

  if (datos.length === 0) {
    tbody.innerHTML = `
      <tr>
        <td colspan="6" class="text-center text-muted">
          No hay actividades registradas
        </td>
      </tr>
    `;
    return;
  }

  datos.forEach((item) => {
    const row = document.createElement("tr");
    const usuario = item.persona_nombres
      ? `${item.persona_nombres} ${item.persona_apellidos}`
      : "Sin usuario";

    row.innerHTML = `
      <td>${item.logs_actividad_fecha || ""}</td>
      <td>${usuario}</td>
      <td><span class="badge badge-primary">${
        item.logs_actividad_accion || ""
      }</span></td>
      <td>${item.logs_actividad_tabla || ""}</td>
      <td>${item.logs_actividad_registro_id || ""}</td>
      <td class="text-center">
        <button class="btn btn-info btn-sm" onclick="verDetalle(${
          item.id_logs_actividad
        })">
          <i class="bi bi-eye"></i>
        </button>
      </td>
    `;
    tbody.appendChild(row);
  });

  datatable = initDataTable("#tablaLogs", {
    order: [[0, "desc"]],
    pageLength: 25,
  });
}

window.verDetalle = async function (id) {
  try {
    const response = await fetch(`${APP_URL}/api/logs`);
    const resultado = await response.json();

    if (resultado.exito) {
      const log = resultado.data.find((l) => l.id_logs_actividad == id);
      if (log) {
        const usuario = log.persona_nombres
          ? `${log.persona_nombres} ${log.persona_apellidos}`
          : "Sin usuario";

        document.getElementById("detalleAccion").textContent =
          log.logs_actividad_accion || "";
        document.getElementById("detalleTabla").textContent =
          log.logs_actividad_tabla || "";
        document.getElementById("detalleRegistroId").textContent =
          log.logs_actividad_registro_id || "";
        document.getElementById("detalleFecha").textContent =
          log.logs_actividad_fecha || "";
        document.getElementById("detalleUsuario").textContent = usuario;
        document.getElementById("detalleIp").textContent =
          log.logs_actividad_ip || "";

        const divCambios = document.getElementById("detalleCambios");
        divCambios.innerHTML = "";

        if (
          log.logs_actividad_datos_antiguos ||
          log.logs_actividad_datos_nuevos
        ) {
          let htmlCambios = '<div class="mb-2">';

          if (log.logs_actividad_datos_antiguos) {
            htmlCambios += "<h6>Datos Anteriores:</h6>";
            htmlCambios +=
              '<pre class="bg-light p-2 rounded">' +
              formatJSON(log.logs_actividad_datos_antiguos) +
              "</pre>";
          }

          if (log.logs_actividad_datos_nuevos) {
            htmlCambios += "<h6>Datos Nuevos:</h6>";
            htmlCambios +=
              '<pre class="bg-light p-2 rounded">' +
              formatJSON(log.logs_actividad_datos_nuevos) +
              "</pre>";
          }

          htmlCambios += "</div>";
          divCambios.innerHTML = htmlCambios;
        } else {
          divCambios.innerHTML =
            '<p class="text-muted">Sin cambios registrados</p>';
        }

        window.$("#modalDetalles").modal("show");
      }
    }
  } catch (error) {
    console.error("Error al cargar detalle:", error);
  }
};

function formatJSON(jsonString) {
  try {
    const obj = JSON.parse(jsonString);
    return JSON.stringify(obj, null, 2);
  } catch (e) {
    return jsonString;
  }
}

document.getElementById("btnRefrescar")?.addEventListener("click", () => {
  cargarLogs();
});

document.getElementById("btnExportar")?.addEventListener("click", async () => {
  try {
    const response = await fetch(`${APP_URL}/api/logs`);
    const resultado = await response.json();

    if (resultado.exito) {
      const columnas = [
        {
          header: "Fecha",
          obtenerValor: (item) => item.logs_actividad_fecha,
        },
        {
          header: "Usuario",
          obtenerValor: (item) =>
            item.persona_nombres
              ? `${item.persona_nombres} ${item.persona_apellidos}`
              : "Sin usuario",
        },
        {
          header: "Acción",
          obtenerValor: (item) => item.logs_actividad_accion,
        },
        {
          header: "Tabla",
          obtenerValor: (item) => item.logs_actividad_tabla,
        },
        {
          header: "Registro ID",
          obtenerValor: (item) => item.logs_actividad_registro_id,
        },
        {
          header: "IP",
          obtenerValor: (item) => item.logs_actividad_ip,
        },
      ];

      exportarAExcel(resultado.data, columnas, "logs_actividad.xlsx");
    }
  } catch (error) {
    console.error("Error al exportar:", error);
  }
});

document
  .getElementById("filtroAccion")
  ?.addEventListener("change", filtrarTabla);
document
  .getElementById("filtroTabla")
  ?.addEventListener("change", filtrarTabla);
document
  .getElementById("filtroUsuario")
  ?.addEventListener("change", filtrarTabla);

async function filtrarTabla() {
  const accion = document.getElementById("filtroAccion")?.value || "";
  const tabla = document.getElementById("filtroTabla")?.value || "";
  const usuario = document.getElementById("filtroUsuario")?.value || "";

  try {
    const response = await fetch(`${APP_URL}/api/logs`);
    const resultado = await response.json();

    if (resultado.exito) {
      let datosFiltrados = resultado.data;

      if (accion) {
        datosFiltrados = datosFiltrados.filter(
          (item) => item.logs_actividad_accion === accion
        );
      }

      if (tabla) {
        datosFiltrados = datosFiltrados.filter(
          (item) => item.logs_actividad_tabla === tabla
        );
      }

      if (usuario) {
        datosFiltrados = datosFiltrados.filter((item) => {
          const nombreCompleto = item.persona_nombres
            ? `${item.persona_nombres} ${item.persona_apellidos}`
            : "Sin usuario";
          return nombreCompleto === usuario;
        });
      }

      renderizarTabla(datosFiltrados);
    }
  } catch (error) {
    console.error("Error al filtrar:", error);
  }
}

cargarLogs();

// Vista Guiada - Logs de Actividad
document
  .getElementById("btnVistaGuiada")
  ?.addEventListener("click", function (e) {
    e.preventDefault();

    const steps = [
      {
        intro:
          "¡Bienvenido al módulo de Logs de Actividad! Aquí monitoreas todas las acciones realizadas en el sistema.",
      },
      {
        element: "#filtroAccion",
        intro: "Filtra por tipo de acción (crear, editar, eliminar, etc.).",
        position: "bottom",
      },
      {
        element: "#filtroTabla",
        intro: "Filtra por tabla específica de la base de datos.",
        position: "bottom",
      },
      {
        element: "#filtroUsuario",
        intro: "Filtra por usuario específico.",
        position: "bottom",
      },
      {
        element: "#btnRefrescar",
        intro: "Haz clic aquí para actualizar la lista de logs.",
        position: "left",
      },
      {
        element: "#btnExportar",
        intro: "Exporta todos los logs a Excel para análisis externo.",
        position: "left",
      },
      {
        element: "#tablaLogs",
        intro:
          "Lista completa de todas las actividades registradas en el sistema.",
        position: "top",
      },
      {
        element: "#tablaLogs thead th:nth-child(1)",
        intro: "Fecha y hora exacta de la actividad.",
        position: "bottom",
      },
      {
        element: "#tablaLogs thead th:nth-child(2)",
        intro: "Usuario que realizó la acción.",
        position: "bottom",
      },
      {
        element: "#tablaLogs thead th:nth-child(3)",
        intro: "Tipo de acción realizada.",
        position: "bottom",
      },
      {
        element: "#tablaLogs thead th:nth-child(4)",
        intro: "Tabla de la base de datos afectada.",
        position: "bottom",
      },
      {
        element: "#tablaLogs thead th:nth-child(5)",
        intro: "ID del registro modificado.",
        position: "bottom",
      },
      {
        element: "#tablaLogs thead th:nth-child(6)",
        intro: "Ver detalles completos de la actividad.",
        position: "left",
      },
      {
        element: "#tablaLogs tbody tr:first-child td:last-child",
        intro:
          "Haz clic en el botón de ojo para ver todos los detalles de una actividad específica.",
        position: "left",
      },
      {
        intro:
          "Los logs registran automáticamente todas las acciones importantes para auditoría y seguimiento.",
      },
      {
        intro: "¡Listo! Mantén el control de todo lo que sucede en tu sistema.",
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
