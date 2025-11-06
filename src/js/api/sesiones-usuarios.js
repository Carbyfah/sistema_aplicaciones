import { initDataTable } from "../helpers/tablas.js";
import { cargarSelect } from "../helpers/selects.js";

const APP_URL = window.location.pathname.includes("sistema_aplicaciones")
  ? "/sistema_aplicaciones"
  : "";

let datatable = null;

async function cargarSesiones() {
  try {
    const response = await fetch(`${APP_URL}/api/sesiones`);
    const resultado = await response.json();

    if (resultado.exito) {
      renderizarTabla(resultado.data);
    }
  } catch (error) {
    console.error("Error al cargar sesiones:", error);
  }
}

function renderizarTabla(datos) {
  const tbody = document.querySelector("#tablaSesiones tbody");

  if (!tbody) return;

  if (datatable) {
    datatable.destroy();
    datatable = null;
  }

  tbody.innerHTML = "";

  if (datos.length === 0) {
    tbody.innerHTML = `
      <tr>
        <td colspan="7" class="text-center text-muted">
          No hay sesiones registradas
        </td>
      </tr>
    `;
    return;
  }

  datos.forEach((item) => {
    const row = document.createElement("tr");
    const estadoBadge = item.sesion_fecha_cierre
      ? '<span class="badge badge-secondary">Cerrada</span>'
      : '<span class="badge badge-success">Activa</span>';

    const nombreCompleto =
      item.persona_nombres && item.persona_apellidos
        ? `${item.persona_nombres} ${item.persona_apellidos}`
        : item.usuarios_nombre || "Sin usuario";

    row.innerHTML = `
      <td>${nombreCompleto}</td>
      <td>${item.sesion_ip || ""}</td>
      <td class="text-truncate" style="max-width: 200px;">${
        item.sesion_user_agent || ""
      }</td>
      <td>${item.sesion_fecha_inicio || ""}</td>
      <td>${item.sesion_fecha_cierre || "Aún activa"}</td>
      <td>${estadoBadge}</td>
      <td class="text-center">
        <button class="btn btn-info btn-sm" onclick="verDetalleSesion(${
          item.sesion_id
        })">
          <i class="bi bi-eye"></i>
        </button>
      </td>
    `;
    tbody.appendChild(row);
  });

  datatable = initDataTable("#tablaSesiones", {
    order: [[3, "desc"]],
    pageLength: 25,
  });
}

window.verDetalleSesion = async function (id) {
  try {
    const response = await fetch(`${APP_URL}/api/sesiones`);
    const resultado = await response.json();

    if (resultado.exito) {
      const sesion = resultado.data.find((s) => s.sesion_id == id);
      if (sesion) {
        const nombreCompleto =
          sesion.persona_nombres && sesion.persona_apellidos
            ? `${sesion.persona_nombres} ${sesion.persona_apellidos} (${sesion.usuarios_nombre})`
            : sesion.usuarios_nombre || "Sin usuario";

        document.getElementById("detalleUsuario").textContent = nombreCompleto;
        document.getElementById("detalleIp").textContent =
          sesion.sesion_ip || "";
        document.getElementById("detalleUserAgent").textContent =
          sesion.sesion_user_agent || "";
        document.getElementById("detalleInicio").textContent =
          sesion.sesion_fecha_inicio || "";
        document.getElementById("detalleFin").textContent =
          sesion.sesion_fecha_cierre || "Aún activa";
        document.getElementById("detalleToken").textContent =
          sesion.sesion_token || "";

        window.$("#modalDetalleSesion").modal("show");
      }
    }
  } catch (error) {
    console.error("Error al cargar detalle:", error);
  }
};

async function aplicarFiltros() {
  const estado = document.getElementById("filtroEstado")?.value || "";
  const usuarioId = document.getElementById("filtroUsuario")?.value || "";
  const fecha = document.getElementById("filtroFecha")?.value || "";

  let url = `${APP_URL}/api/sesiones?`;
  const params = [];

  if (estado) {
    if (estado === "activa") params.push("estado=1");
    if (estado === "cerrada") params.push("estado=0");
  }

  if (usuarioId) params.push(`usuario_id=${usuarioId}`);
  if (fecha) params.push(`fecha=${fecha}`);

  if (params.length > 0) {
    url += params.join("&");
  } else {
    url = `${APP_URL}/api/sesiones`;
  }

  try {
    const response = await fetch(url);
    const resultado = await response.json();

    if (resultado.exito) {
      renderizarTabla(resultado.data);
    }
  } catch (error) {
    console.error("Error al aplicar filtros:", error);
  }
}

document.getElementById("btnRefrescar")?.addEventListener("click", () => {
  cargarSesiones();
});

document
  .getElementById("filtroEstado")
  ?.addEventListener("change", aplicarFiltros);

document
  .getElementById("filtroUsuario")
  ?.addEventListener("change", aplicarFiltros);
document
  .getElementById("filtroFecha")
  ?.addEventListener("change", aplicarFiltros);

cargarSelect(
  `${APP_URL}/api/usuarios`,
  "filtroUsuario",
  "id_usuarios",
  "usuarios_nombre"
);

cargarSesiones();

// Vista Guiada - Sesiones de Usuarios
document
  .getElementById("btnVistaGuiada")
  ?.addEventListener("click", function (e) {
    e.preventDefault();

    const steps = [
      {
        intro:
          "¡Bienvenido al módulo de Sesiones de Usuarios! Aquí monitoreas las sesiones activas e históricas del sistema.",
      },
      {
        element: "#filtroEstado",
        intro: "Filtra por estado de sesión: Activas o Cerradas.",
        position: "bottom",
      },
      {
        element: "#filtroUsuario",
        intro: "Filtra por usuario específico.",
        position: "bottom",
      },
      {
        element: "#btnRefrescar",
        intro: "Haz clic aquí para actualizar la lista de sesiones.",
        position: "left",
      },
      {
        element: "#tablaSesiones",
        intro:
          "Lista completa de todas las sesiones de usuarios en el sistema.",
        position: "top",
      },
      {
        element: "#tablaSesiones thead th:nth-child(1)",
        intro: "Usuario que inició la sesión.",
        position: "bottom",
      },
      {
        element: "#tablaSesiones thead th:nth-child(2)",
        intro: "Dirección IP desde donde se conectó.",
        position: "bottom",
      },
      {
        element: "#tablaSesiones thead th:nth-child(3)",
        intro: "Navegador y dispositivo utilizado.",
        position: "bottom",
      },
      {
        element: "#tablaSesiones thead th:nth-child(4)",
        intro: "Fecha y hora de inicio de sesión.",
        position: "bottom",
      },
      {
        element: "#tablaSesiones thead th:nth-child(5)",
        intro: "Fecha y hora de cierre de sesión.",
        position: "bottom",
      },
      {
        element: "#tablaSesiones thead th:nth-child(6)",
        intro: "Estado actual de la sesión.",
        position: "bottom",
      },
      {
        element: "#tablaSesiones thead th:nth-child(7)",
        intro: "Ver detalles completos de la sesión.",
        position: "bottom",
      },
      {
        element: "#tablaSesiones tbody tr:first-child td:last-child",
        intro:
          "Haz clic en el botón de ojo para ver todos los detalles de una sesión específica.",
        position: "left",
      },
      {
        intro: "Verde = Sesión activa, Gris = Sesión cerrada.",
      },
      {
        intro:
          "¡Listo! Monitorea la actividad de conexión de los usuarios en tiempo real.",
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
