import { mostrarExito, mostrarError } from "../helpers/modal.js";
import { formatearFecha } from "../helpers/utilidades.js";

const APP_URL = window.location.pathname.includes("sistema_aplicaciones")
  ? "/sistema_aplicaciones"
  : "";

document.addEventListener("DOMContentLoaded", function () {
  cargarNotificaciones();

  document
    .getElementById("tab-no-leidas")
    ?.addEventListener("click", () => cargarNoLeidas());
  document
    .getElementById("tab-todas")
    ?.addEventListener("click", () => cargarTodas());

  document
    .getElementById("btnMarcarTodasLeidas")
    ?.addEventListener("click", marcarTodasLeidas);
});

async function cargarNotificaciones() {
  await Promise.all([cargarNoLeidas(), cargarTodas()]);
}

async function cargarNoLeidas() {
  try {
    const response = await fetch(`${APP_URL}/api/notificaciones?leidas=0`);

    if (!response.ok) {
      console.error(`Error HTTP: ${response.status}`);
      return;
    }

    const resultado = await response.json();

    const container = document.getElementById("listaNoLeidas");
    if (!container) return;

    container.innerHTML = "";

    if (resultado.exito) {
      const notificaciones = resultado.data;
      const contador = document.getElementById("contadorNoLeidas");
      if (contador) {
        contador.textContent = notificaciones.length;
      }

      if (notificaciones.length === 0) {
        container.innerHTML = `
          <div class="text-center p-4 text-muted">
            <i class="fas fa-check-circle fa-2x mb-3"></i>
            <p>No tienes notificaciones pendientes</p>
          </div>
        `;
        return;
      }

      notificaciones.forEach((notificacion) => {
        container.appendChild(crearElementoNotificacion(notificacion, true));
      });
    }
  } catch (error) {
    console.error("Error al cargar notificaciones no leídas:", error);
    mostrarError("No se pudieron cargar las notificaciones");
  }
}

async function cargarTodas() {
  try {
    const response = await fetch(`${APP_URL}/api/notificaciones`);

    if (!response.ok) {
      console.error(`Error HTTP: ${response.status}`);
      return;
    }

    const resultado = await response.json();

    const container = document.getElementById("listaTodas");
    if (!container) return;

    container.innerHTML = "";

    if (resultado.exito) {
      const notificaciones = resultado.data;

      if (notificaciones.length === 0) {
        container.innerHTML = `
          <div class="text-center p-4 text-muted">
            <i class="fas fa-bell-slash fa-2x mb-3"></i>
            <p>No hay notificaciones disponibles</p>
          </div>
        `;
        return;
      }

      notificaciones.forEach((notificacion) => {
        container.appendChild(crearElementoNotificacion(notificacion, false));
      });
    }
  } catch (error) {
    console.error("Error al cargar todas las notificaciones:", error);
    mostrarError("No se pudieron cargar las notificaciones");
  }
}

function crearElementoNotificacion(notificacion, mostrarBtnLeer) {
  const item = document.createElement("div");
  item.className = `list-group-item list-group-item-action ${
    notificacion.notificaciones_leida === 0 ? "font-weight-bold" : ""
  }`;

  let icono = "fa-bell";
  let colorIcono = "text-primary";

  switch (notificacion.notificaciones_tipo) {
    case "INFO":
      icono = "fa-info-circle";
      colorIcono = "text-info";
      break;
    case "WARNING":
      icono = "fa-exclamation-triangle";
      colorIcono = "text-warning";
      break;
    case "ERROR":
      icono = "fa-exclamation-circle";
      colorIcono = "text-danger";
      break;
    case "SUCCESS":
      icono = "fa-check-circle";
      colorIcono = "text-success";
      break;
  }

  item.innerHTML = `
    <div class="d-flex w-100 justify-content-between align-items-center">
      <div>
        <span class="mr-2 ${colorIcono}"><i class="fas ${icono}"></i></span>
        <h5 class="mb-1">${notificacion.notificaciones_titulo}</h5>
      </div>
      <small>${formatearFecha(notificacion.notificaciones_fecha)}</small>
    </div>
    <p class="mb-1">${notificacion.notificaciones_mensaje}</p>
    <div class="d-flex w-100 justify-content-between align-items-center mt-2">
      <small>
        ${
          notificacion.notificaciones_objeto_tipo
            ? `Referencia: ${notificacion.notificaciones_objeto_tipo} #${notificacion.notificaciones_objeto_id}`
            : ""
        }
      </small>
      ${
        mostrarBtnLeer && notificacion.notificaciones_leida === 0
          ? `<button class="btn btn-sm btn-outline-primary btn-marcar-leida" 
              data-id="${notificacion.id_notificaciones}">
              <i class="bi bi-check"></i> Marcar como leída
          </button>`
          : ""
      }
    </div>
  `;

  if (mostrarBtnLeer && notificacion.notificaciones_leida === 0) {
    const btn = item.querySelector(".btn-marcar-leida");
    if (btn) {
      btn.addEventListener("click", (e) => {
        e.preventDefault();
        marcarLeida(notificacion.id_notificaciones);
      });
    }
  }

  return item;
}

async function marcarLeida(id) {
  try {
    const response = await fetch(`${APP_URL}/api/notificaciones/marcar-leida`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ id: id }),
    });

    const resultado = await response.json();

    if (resultado.exito) {
      await cargarNotificaciones();
      mostrarExito("Notificación marcada como leída");
    } else {
      mostrarError(resultado.mensaje);
    }
  } catch (error) {
    console.error("Error al marcar como leída:", error);
    mostrarError("No se pudo marcar la notificación como leída");
  }
}

async function marcarTodasLeidas() {
  try {
    const response = await fetch(
      `${APP_URL}/api/notificaciones/marcar-todas-leidas`,
      {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
      }
    );

    const resultado = await response.json();

    if (resultado.exito) {
      await cargarNotificaciones();
      mostrarExito("Todas las notificaciones marcadas como leídas");
    } else {
      mostrarError(resultado.mensaje);
    }
  } catch (error) {
    console.error("Error al marcar todas como leídas:", error);
    mostrarError("No se pudieron marcar las notificaciones como leídas");
  }
}
