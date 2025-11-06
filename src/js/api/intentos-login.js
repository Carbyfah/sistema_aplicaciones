import { initDataTable } from "../helpers/tablas.js";

let datatable = null;

async function cargarIntentos() {
  try {
    const response = await fetch("/sistema_aplicaciones/api/intentos-login");
    const resultado = await response.json();

    if (resultado.exito) {
      renderizarTabla(resultado.data);
    }
  } catch (error) {
    console.error("Error al cargar intentos:", error);
  }
}

function renderizarTabla(datos) {
  const tbody = document.querySelector("#tablaIntentos tbody");

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
          No hay intentos de login registrados
        </td>
      </tr>
    `;
    return;
  }

  datos.forEach((item) => {
    const row = document.createElement("tr");
    const exitoso = item.intentos_login_exitoso == 1;
    const badge = exitoso
      ? '<span class="badge badge-success">Exitoso</span>'
      : '<span class="badge badge-danger">Fallido</span>';

    row.innerHTML = `
      <td>${item.intentos_login_fecha || ""}</td>
      <td>${item.intentos_login_usuario || ""}</td>
      <td>${item.intentos_login_ip || ""}</td>
      <td>${badge}</td>
      <td>${item.intentos_login_mensaje || ""}</td>
      <td class="text-center">
        <button class="btn btn-info btn-sm" onclick="verDetalle(${
          item.id_intentos_login
        })">
          <i class="bi bi-eye"></i>
        </button>
      </td>
    `;
    tbody.appendChild(row);
  });

  datatable = initDataTable("#tablaIntentos", {
    order: [[0, "desc"]],
    pageLength: 25,
  });
}

window.verDetalle = async function (id) {
  try {
    const response = await fetch("/sistema_aplicaciones/api/intentos-login");
    const resultado = await response.json();

    if (resultado.exito) {
      const intento = resultado.data.find((i) => i.id_intentos_login == id);
      if (intento) {
        document.getElementById("detalleFecha").textContent =
          intento.intentos_login_fecha || "";
        document.getElementById("detalleUsuario").textContent =
          intento.intentos_login_usuario || "";
        document.getElementById("detalleIp").textContent =
          intento.intentos_login_ip || "";
        document.getElementById("detalleUserAgent").textContent =
          intento.intentos_login_user_agent || "";

        const exitoso =
          intento.intentos_login_exitoso == 1 ? "Exitoso" : "Fallido";
        document.getElementById("detalleExitoso").innerHTML =
          intento.intentos_login_exitoso == 1
            ? '<span class="badge badge-success">Exitoso</span>'
            : '<span class="badge badge-danger">Fallido</span>';

        document.getElementById("detalleMensaje").textContent =
          intento.intentos_login_mensaje || "";

        window.$("#modalDetalleIntento").modal("show");
      }
    }
  } catch (error) {
    console.error("Error al cargar detalle:", error);
  }
};

document.getElementById("btnRefrescar")?.addEventListener("click", () => {
  cargarIntentos();
});

document
  .getElementById("filtroExitoso")
  ?.addEventListener("change", async (e) => {
    const exitoso = e.target.value;
    if (exitoso !== "") {
      const response = await fetch(
        `/sistema_aplicaciones/api/intentos-login?exitoso=${exitoso}`
      );
      const resultado = await response.json();
      if (resultado.exito) {
        renderizarTabla(resultado.data);
      }
    } else {
      cargarIntentos();
    }
  });

cargarIntentos();

// Vista Guiada - Intentos de Login
document
  .getElementById("btnVistaGuiada")
  ?.addEventListener("click", function (e) {
    e.preventDefault();

    const steps = [
      {
        intro:
          "¡Bienvenido al módulo de Intentos de Login! Aquí monitoreas todos los intentos de acceso al sistema.",
      },
      {
        element: "#filtroExitoso",
        intro: "Filtra por resultado del intento: Exitosos o Fallidos.",
        position: "bottom",
      },
      {
        element: "#btnRefrescar",
        intro: "Haz clic aquí para actualizar la lista de intentos.",
        position: "left",
      },
      {
        element: "#tablaIntentos",
        intro:
          "Lista completa de todos los intentos de inicio de sesión en el sistema.",
        position: "top",
      },
      {
        element: "#tablaIntentos thead th:nth-child(1)",
        intro: "Fecha y hora exacta del intento.",
        position: "bottom",
      },
      {
        element: "#tablaIntentos thead th:nth-child(2)",
        intro: "Nombre de usuario utilizado en el intento.",
        position: "bottom",
      },
      {
        element: "#tablaIntentos thead th:nth-child(3)",
        intro: "Dirección IP desde donde se intentó acceder.",
        position: "bottom",
      },
      {
        element: "#tablaIntentos thead th:nth-child(4)",
        intro: "Resultado del intento de login.",
        position: "bottom",
      },
      {
        element: "#tablaIntentos thead th:nth-child(5)",
        intro: "Mensaje o descripción del intento.",
        position: "bottom",
      },
      {
        element: "#tablaIntentos thead th:nth-child(6)",
        intro: "Ver detalles completos del intento.",
        position: "bottom",
      },
      {
        element: "#tablaIntentos tbody tr:first-child td:last-child",
        intro:
          "Haz clic en el botón de ojo para ver todos los detalles de un intento específico.",
        position: "left",
      },
      {
        intro: "Verde = Login exitoso, Rojo = Login fallido.",
      },
      {
        intro:
          "¡Listo! Monitorea la seguridad de acceso a tu sistema y detecta intentos sospechosos.",
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
