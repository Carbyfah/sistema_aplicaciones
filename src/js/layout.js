const APP_URL = window.location.pathname.includes("sistema_aplicaciones")
  ? "/sistema_aplicaciones"
  : "";

document.addEventListener("DOMContentLoaded", function () {
  inicializarDropdowns();
  inicializarNotificaciones();
  activarMenuActual();
});

function inicializarDropdowns() {
  const btnNotificaciones = document.getElementById("btnNotificaciones");
  if (btnNotificaciones) {
    btnNotificaciones.addEventListener("click", function (e) {
      e.preventDefault();
      e.stopPropagation();
      const dropdown = this.nextElementSibling;
      dropdown.classList.toggle("show");
    });
  }

  const btnUsuario = document.getElementById("btnUsuario");
  if (btnUsuario) {
    btnUsuario.addEventListener("click", function (e) {
      e.preventDefault();
      e.stopPropagation();
      const dropdown = this.nextElementSibling;
      dropdown.classList.toggle("show");
    });
  }

  document.addEventListener("click", function (e) {
    const dropdowns = document.querySelectorAll(".dropdown-menu.show");
    dropdowns.forEach((dropdown) => {
      if (!dropdown.parentElement.contains(e.target)) {
        dropdown.classList.remove("show");
      }
    });
  });
}

function activarMenuActual() {
  const rutaActual = window.location.pathname;
  const enlaces = document.querySelectorAll(".nav-sidebar .nav-link");

  enlaces.forEach((enlace) => {
    const href = enlace.getAttribute("href");

    if (
      href &&
      href !== "#" &&
      rutaActual.includes(href.replace(APP_URL, ""))
    ) {
      enlace.classList.add("active");

      const parentItem = enlace.closest(".nav-item");
      if (parentItem) {
        const parentMenu = parentItem.closest(".nav-treeview");
        if (parentMenu) {
          const parentLink = parentMenu.previousElementSibling;
          if (parentLink && parentLink.classList.contains("nav-link")) {
            parentLink.classList.add("active");
            const grandParentItem = parentLink.closest(".nav-item");
            if (grandParentItem) {
              grandParentItem.classList.add("menu-open");
              parentMenu.style.display = "block";
            }
          }
        }
      }
    }
  });
}

function inicializarNotificaciones() {
  cargarNotificaciones();
  setInterval(cargarNotificaciones, 30000);
}

async function cargarNotificaciones() {
  try {
    const response = await fetch(`${APP_URL}/api/notificaciones?limit=5`);

    if (!response.ok) {
      console.error(`Error HTTP: ${response.status}`);
      return;
    }

    const resultado = await response.json();

    if (resultado.exito && resultado.data) {
      const notificaciones = resultado.data;
      const noLeidas = notificaciones.filter(
        (n) => n.notificaciones_leida === 0 || n.notificaciones_leida === "0"
      );

      const contador = document.getElementById("contadorNotificaciones");
      const header = document.getElementById("headerNotificaciones");
      const lista = document.getElementById("listaNotificaciones");

      if (!contador || !header || !lista) {
        console.warn("Elementos de notificaciones no encontrados en el DOM");
        return;
      }

      if (noLeidas.length > 0) {
        contador.textContent = noLeidas.length;
        contador.style.display = "inline";
        header.textContent = `${noLeidas.length} notificaciones sin leer`;
      } else {
        contador.style.display = "none";
        header.textContent = "Sin notificaciones sin leer";
      }

      if (notificaciones.length > 0) {
        lista.innerHTML = notificaciones
          .slice(0, 5)
          .map(
            (n) => `
                    <a href="${APP_URL}/notificaciones" class="dropdown-item">
                        <i class="fas fa-${getTipoIcono(
                          n.notificaciones_tipo
                        )} mr-2"></i> ${n.notificaciones_titulo || "Sin título"}
                        <span class="float-right text-muted text-sm">${formatearFecha(
                          n.notificaciones_fecha
                        )}</span>
                    </a>
                    <div class="dropdown-divider"></div>
                `
          )
          .join("");
      } else {
        lista.innerHTML =
          '<a href="#" class="dropdown-item text-muted">No hay notificaciones</a>';
      }
    }
  } catch (error) {
    console.error("Error al cargar notificaciones:", error);
    const lista = document.getElementById("listaNotificaciones");
    if (lista) {
      lista.innerHTML =
        '<a href="#" class="dropdown-item text-danger">Error al cargar</a>';
    }
  }
}

function getTipoIcono(tipo) {
  const iconos = {
    INFO: "info-circle",
    ALERTA: "exclamation-triangle",
    EXITO: "check-circle",
    ERROR: "times-circle",
    PROYECTO: "project-diagram",
    DOCUMENTO: "file-alt",
    ASIGNACION: "user-tag",
  };
  return iconos[tipo] || "bell";
}

function formatearFecha(fecha) {
  if (!fecha) return "";

  const ahora = new Date();
  const fechaNot = new Date(fecha);
  const diff = Math.floor((ahora - fechaNot) / 1000 / 60);

  if (diff < 1) return "Ahora";
  if (diff < 60) return `${diff} min`;
  if (diff < 1440) return `${Math.floor(diff / 60)} h`;
  return `${Math.floor(diff / 1440)} d`;
}
