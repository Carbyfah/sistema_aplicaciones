import Chart from "chart.js/auto"; // Importar Chart.js correctamente

const APP_URL = window.location.pathname.includes("sistema_aplicaciones")
  ? "/sistema_aplicaciones"
  : "";

document.addEventListener("DOMContentLoaded", function () {
  verificarAdmin();

  const formLogin = document.getElementById("formLogin");
  const formRegistrarAdmin = document.getElementById("formRegistrarAdmin");
  const btnMostrarRegistro = document.getElementById("btnMostrarRegistro");
  const btnVolverLogin = document.getElementById("btnVolverLogin");

  if (formLogin) {
    formLogin.addEventListener("submit", handleLogin);
  }

  if (formRegistrarAdmin) {
    formRegistrarAdmin.addEventListener("submit", handleRegistrarAdmin);
  }

  if (btnMostrarRegistro) {
    btnMostrarRegistro.addEventListener("click", function () {
      document.getElementById("contenedorLogin").style.display = "none";
      document.getElementById("contenedorRegistro").style.display = "block";
    });
  }

  if (btnVolverLogin) {
    btnVolverLogin.addEventListener("click", function () {
      document.getElementById("contenedorRegistro").style.display = "none";
      document.getElementById("contenedorLogin").style.display = "block";
    });
  }

  // Iniciar dashboard si estamos en esa página
  if (document.getElementById("total-proyectos")) {
    iniciarDashboard();
  }
});

function iniciarDashboard() {
  cargarEstadisticasRapidas();
  cargarProyectosRecientes();
  cargarActividadReciente();
  cargarGraficoEstadosProyectos();
}

async function verificarAdmin() {
  try {
    const response = await fetch(`${APP_URL}/api/verificar-admin`);
    const resultado = await response.json();

    if (resultado.exito && !resultado.existe_admin) {
      document.getElementById("opcionRegistrarAdmin").style.display = "block";
    }
  } catch (error) {
    console.error("Error al verificar admin:", error);
  }
}

async function handleLogin(e) {
  e.preventDefault();

  const datos = {
    usuarios_nombre: document.getElementById("usuarios_nombre").value,
    usuarios_password: document.getElementById("usuarios_password").value,
  };

  try {
    const response = await fetch(`${APP_URL}/api/auth`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(datos),
    });

    const resultado = await response.json();

    if (resultado.exito) {
      Swal.fire({
        icon: "success",
        title: "¡Bienvenido!",
        text: resultado.mensaje,
        timer: 1500,
        showConfirmButton: false,
      }).then(() => {
        window.location.href = `${APP_URL}/dashboard`;
      });
    } else {
      Swal.fire({
        icon: "error",
        title: "Error al iniciar sesión",
        text: resultado.mensaje || "Credenciales incorrectas",
        confirmButtonText: "Aceptar",
      });
    }
  } catch (error) {
    console.error("Error:", error);
    Swal.fire({
      icon: "error",
      title: "Error de conexión",
      text: "No se pudo conectar con el servidor. Por favor, intenta nuevamente.",
      confirmButtonText: "Aceptar",
    });
  }
}

async function handleRegistrarAdmin(e) {
  e.preventDefault();

  const datos = {
    persona_nombres: document.getElementById("persona_nombres").value,
    persona_apellidos: document.getElementById("persona_apellidos").value,
    persona_identidad: document.getElementById("persona_identidad").value,
    usuarios_nombre: document.getElementById("usuarios_nombre_admin").value,
    usuarios_password: document.getElementById("usuarios_password_admin").value,
  };

  try {
    const response = await fetch(`${APP_URL}/api/registrar-primer-admin`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(datos),
    });

    const resultado = await response.json();

    if (resultado.exito) {
      // Usar SweetAlert2 para el mensaje de éxito
      Swal.fire({
        icon: "success",
        title: "¡Éxito!",
        text: resultado.mensaje,
        confirmButtonText: "Aceptar",
      }).then(() => {
        // Esto se ejecuta después de que el usuario hace clic en "Aceptar"
        document.getElementById("contenedorRegistro").style.display = "none";
        document.getElementById("contenedorLogin").style.display = "block";
        document.getElementById("opcionRegistrarAdmin").style.display = "none";
        document.getElementById("formRegistrarAdmin").reset();
      });
    } else {
      // Usar SweetAlert2 para el mensaje de error
      Swal.fire({
        icon: "error",
        title: "Error",
        text: resultado.mensaje || "Error al registrar administrador",
        confirmButtonText: "Aceptar",
      });
    }
  } catch (error) {
    console.error("Error:", error);
    // Usar SweetAlert2 para el error de conexión
    Swal.fire({
      icon: "error",
      title: "Error de conexión",
      text: "Error al conectar con el servidor",
      confirmButtonText: "Aceptar",
    });
  }
}

async function cargarEstadisticasRapidas() {
  try {
    const response = await fetch(`${APP_URL}/api/estadisticas-rapidas`);
    const resultado = await response.json();

    console.log("Estadísticas rápidas:", resultado);

    if (resultado.exito && resultado.data) {
      const data = resultado.data;

      // Actualizar los contadores directamente con los datos de la API
      const elementos = {
        "total-proyectos": "total_proyectos",
        "total-completados": "proyectos_completados",
        "total-documentos": "total_documentos",
        "total-pendientes": "tareas_pendientes",
      };

      // Actualizar cada elemento si existe
      Object.entries(elementos).forEach(([elementId, dataKey]) => {
        const elemento = document.getElementById(elementId);
        if (elemento && data[dataKey] !== undefined) {
          elemento.textContent = data[dataKey];
        }
      });
    }
  } catch (error) {
    console.error("Error al cargar estadísticas:", error);
  }
}

async function cargarProyectosRecientes() {
  try {
    const response = await fetch(
      `${APP_URL}/api/proyectos-asignados?situacion=1`
    );
    const resultado = await response.json();

    console.log("Datos recibidos:", resultado); // Para depuración

    const tbody = document.getElementById("lista-proyectos-recientes");

    if (resultado.exito && resultado.data && resultado.data.length > 0) {
      // Primero, vaciar la tabla
      tbody.innerHTML =
        '<tr><td colspan="5" class="text-center">Cargando proyectos...</td></tr>';

      // Luego tomar solo los 5 proyectos más recientes
      const proyectosRecientes = resultado.data.slice(0, 5);

      // Crear las filas, pero sin progreso aún
      tbody.innerHTML = proyectosRecientes
        .map((proyecto) => {
          console.log("Procesando proyecto:", proyecto); // Para depuración
          return `
        <tr>
          <td>${proyecto.ordenes_aplicaciones_codigo || "N/A"}</td>
          <td>${proyecto.aplicacion_nombre || "Sin nombre"}</td>
          <td>${proyecto.persona_nombres || ""} ${
            proyecto.persona_apellidos || ""
          }</td>
          <td><span class="badge badge-${getBadgeColorByStatus(
            proyecto.estados_nombre
          )}">${proyecto.estados_nombre || "Sin estado"}</span></td>
          <td>
            <div class="progress progress-xs" data-proyecto-id="${
              proyecto.id_ordenes_aplicaciones
            }">
              <div class="progress-bar bg-info" style="width: 0%"></div>
            </div>
          </td>
        </tr>
      `;
        })
        .join("");

      // Ahora cargar el progreso para cada proyecto
      proyectosRecientes.forEach((proyecto) => {
        cargarProgresoDashboard(proyecto.id_ordenes_aplicaciones);
      });
    } else {
      tbody.innerHTML =
        '<tr><td colspan="5" class="text-center">No hay proyectos recientes</td></tr>';
    }
  } catch (error) {
    console.error("Error al cargar proyectos:", error);
    document.getElementById("lista-proyectos-recientes").innerHTML =
      '<tr><td colspan="5" class="text-center text-danger">Error al cargar proyectos</td></tr>';
  }
}

function getBadgeColorByStatus(status) {
  if (!status) return "secondary";

  const statusLower = status.toLowerCase();
  switch (statusLower) {
    case "pendiente":
      return "warning";
    case "en proceso":
      return "info";
    case "completado":
      return "success";
    case "cancelado":
      return "danger";
    default:
      return "secondary";
  }
}

async function cargarProgresoDashboard(proyectoId) {
  try {
    const response = await fetch(
      `${APP_URL}/api/tareas-aplicaciones/progreso?proyecto_id=${proyectoId}`
    );
    const resultado = await response.json();

    if (resultado.exito) {
      const progreso = resultado.data.progreso || 0;
      const totalTareas = resultado.data.total_tareas || 0;
      const completadas = resultado.data.completadas || 0;

      // Actualizar todas las barras de progreso asociadas con este proyecto
      const barrasProgreso = document.querySelectorAll(
        `.progress[data-proyecto-id="${proyectoId}"] .progress-bar`
      );

      barrasProgreso.forEach((barra) => {
        barra.style.width = `${progreso}%`;

        // Mostrar el porcentaje exacto en la barra - como en Mis Proyectos
        barra.textContent = `${progreso}%`;

        // Cambiar color según el progreso
        barra.classList.remove(
          "bg-success",
          "bg-warning",
          "bg-danger",
          "bg-info"
        );
        if (progreso >= 75) {
          barra.classList.add("bg-success");
        } else if (progreso >= 25) {
          barra.classList.add("bg-warning");
        } else if (progreso > 0) {
          barra.classList.add("bg-danger");
        } else {
          barra.classList.add("bg-info");
        }

        // Agregar tooltip con información detallada
        barra.setAttribute(
          "title",
          `${completadas} de ${totalTareas} tareas completadas (${progreso}%)`
        );
      });
    }
  } catch (error) {
    console.error(
      `Error al cargar progreso del proyecto ${proyectoId}:`,
      error
    );
  }
}

async function cargarActividadReciente() {
  try {
    const response = await fetch(`${APP_URL}/api/logs?limite=10`);
    const resultado = await response.json();

    const tbody = document.getElementById("lista-actividad");

    if (resultado.exito && resultado.data && resultado.data.length > 0) {
      tbody.innerHTML = resultado.data
        .map(
          (log) => `
        <tr>
          <td>${new Date(log.logs_actividad_fecha).toLocaleString()}</td>
          <td>${log.persona_nombres || ""} ${log.persona_apellidos || ""}</td>
          <td><span class="badge badge-primary">${
            log.logs_actividad_accion || ""
          }</span></td>
          <td>${log.logs_actividad_tabla || ""}</td>
          <td>#${log.logs_actividad_registro_id || ""}</td>
        </tr>
      `
        )
        .join("");
    } else {
      tbody.innerHTML =
        '<tr><td colspan="5" class="text-center">No hay actividad reciente</td></tr>';
    }
  } catch (error) {
    console.error("Error al cargar actividad:", error);
    document.getElementById("lista-actividad").innerHTML =
      '<tr><td colspan="5" class="text-center text-danger">Error al cargar actividad</td></tr>';
  }
}

async function cargarGraficoEstadosProyectos() {
  try {
    const response = await fetch(
      `${APP_URL}/api/proyectos-asignados?situacion=1`
    );
    const resultado = await response.json();

    if (resultado.exito && resultado.data && resultado.data.length > 0) {
      // Agrupar proyectos por estado - CORREGIR AQUÍ
      const estadosMap = {};
      resultado.data.forEach((proyecto) => {
        // Cambiar estado_nombre por estados_nombre
        const estado = proyecto.estados_nombre || "Sin estado";
        if (!estadosMap[estado]) {
          estadosMap[estado] = 0;
        }
        estadosMap[estado]++;
      });

      // Resto del código igual...
      const labels = Object.keys(estadosMap);
      const data = Object.values(estadosMap);

      // Colores para los estados
      const colorsMap = {
        Pendiente: "#ffc107", // warning
        "En Proceso": "#17a2b8", // info
        Completado: "#28a745", // success
        Cancelado: "#dc3545", // danger
        "Sin estado": "#6c757d", // secondary
      };

      const backgroundColors = labels.map(
        (label) => colorsMap[label] || "#6c757d"
      );

      // Crear el gráfico
      const ctx = document.getElementById("grafico-estados").getContext("2d");
      if (window.estadosChart) {
        window.estadosChart.destroy();
      }

      window.estadosChart = new Chart(ctx, {
        type: "doughnut",
        data: {
          labels: labels,
          datasets: [
            {
              data: data,
              backgroundColor: backgroundColors,
              borderWidth: 1,
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          legend: {
            position: "right",
            labels: {
              boxWidth: 12,
              padding: 10,
            },
          },
          tooltips: {
            callbacks: {
              label: function (tooltipItem, data) {
                const dataset = data.datasets[tooltipItem.datasetIndex];
                const total = dataset.data.reduce(
                  (previousValue, currentValue) => previousValue + currentValue
                );
                const currentValue = dataset.data[tooltipItem.index];
                const percentage = Math.floor(
                  (currentValue / total) * 100 + 0.5
                );
                return `${
                  data.labels[tooltipItem.index]
                }: ${currentValue} (${percentage}%)`;
              },
            },
          },
        },
      });
    } else {
      // No hay datos para mostrar en el gráfico
      const ctx = document.getElementById("grafico-estados").getContext("2d");
      if (window.estadosChart) {
        window.estadosChart.destroy();
      }

      ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height);
      ctx.font = "14px Arial";
      ctx.textAlign = "center";
      ctx.fillStyle = "#6c757d";
      ctx.fillText(
        "No hay datos disponibles",
        ctx.canvas.width / 2,
        ctx.canvas.height / 2
      );
    }
  } catch (error) {
    console.error("Error al cargar el gráfico de estados:", error);
    const ctx = document.getElementById("grafico-estados").getContext("2d");
    if (window.estadosChart) {
      window.estadosChart.destroy();
    }

    ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height);
    ctx.font = "14px Arial";
    ctx.textAlign = "center";
    ctx.fillStyle = "#dc3545";
    ctx.fillText(
      "Error al cargar datos",
      ctx.canvas.width / 2,
      ctx.canvas.height / 2
    );
  }
}

document
  .getElementById("btnVistaGuiada")
  ?.addEventListener("click", function (e) {
    e.preventDefault();

    const steps = [
      {
        intro:
          "¡Bienvenido al Dashboard! Aquí tienes una vista general del sistema.",
      },
      {
        element: "#total-proyectos",
        intro: "Total de proyectos activos en el sistema.",
        position: "top",
      },
      {
        element: "#total-completados",
        intro: "Proyectos que han sido completados exitosamente.",
        position: "top",
      },
      {
        element: "#total-documentos",
        intro: "Documentos cargados en todos los proyectos.",
        position: "top",
      },
      {
        element: "#total-pendientes",
        intro: "Tareas pendientes por completar.",
        position: "top",
      },
      {
        element: "#grafico-estados",
        intro:
          "Distribución de proyectos por estado (Pendiente, En Proceso, Completado, etc.).",
        position: "left",
      },
      {
        element: "#lista-proyectos-recientes",
        intro: "Proyectos más recientes con su progreso actual.",
        position: "top",
      },
      {
        element: "#lista-actividad",
        intro: "Actividad reciente del sistema (logs de usuarios).",
        position: "top",
      },
      {
        intro:
          "¡Listo! Ahora puedes navegar por el sistema usando el menú lateral.",
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
