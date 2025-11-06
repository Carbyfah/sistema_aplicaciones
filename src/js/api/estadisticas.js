import Chart from "chart.js/auto";
import { mostrarError, mostrarExito } from "../helpers/modal.js";

const APP_URL = window.location.pathname.includes("sistema_aplicaciones")
  ? "/sistema_aplicaciones"
  : "";

class EstadisticasManager {
  constructor() {
    this.charts = {};
    this.filtroAmbito = "global";
    this.filtroProyectoId = null;
    this.filtroUsuarioId = null;
    this.init();
  }

  init() {
    this.eventListeners();
    this.cargarFiltros();
    this.cargarEstadisticas();
  }

  eventListeners() {
    document.getElementById("filtroAmbito")?.addEventListener("change", (e) => {
      this.filtroAmbito = e.target.value;
      this.mostrarFiltrosEspecificos();
      this.cargarEstadisticas();
    });

    document
      .getElementById("filtroProyecto")
      ?.addEventListener("change", (e) => {
        this.filtroProyectoId = e.target.value;
        this.cargarEstadisticas();
      });

    document
      .getElementById("filtroUsuario")
      ?.addEventListener("change", (e) => {
        this.filtroUsuarioId = e.target.value;
        this.cargarEstadisticas();
      });

    document
      .getElementById("btnActualizarEstadisticas")
      ?.addEventListener("click", () => {
        this.cargarEstadisticas(true);
      });
  }

  mostrarFiltrosEspecificos() {
    const proyectoContainer = document.getElementById(
      "filtroProyectoContainer"
    );
    const usuarioContainer = document.getElementById("filtroUsuarioContainer");

    proyectoContainer.style.display = "none";
    usuarioContainer.style.display = "none";

    if (this.filtroAmbito === "proyecto") {
      proyectoContainer.style.display = "block";
    } else if (this.filtroAmbito === "usuario") {
      usuarioContainer.style.display = "block";
    }
  }

  async cargarFiltros() {
    try {
      // Cargar proyectos
      const responseProyectos = await fetch(
        `${APP_URL}/api/proyectos?situacion=1`
      );
      const resultadoProyectos = await responseProyectos.json();

      if (resultadoProyectos.exito) {
        const selectProyecto = document.getElementById("filtroProyecto");
        selectProyecto.innerHTML =
          '<option value="">Seleccione un proyecto</option>';

        resultadoProyectos.data.forEach((proyecto) => {
          const option = document.createElement("option");
          option.value = proyecto.id_aplicacion;
          option.textContent = proyecto.aplicacion_nombre;
          selectProyecto.appendChild(option);
        });
      }

      // Cargar usuarios
      const responseUsuarios = await fetch(
        `${APP_URL}/api/usuarios?situacion=1`
      );
      const resultadoUsuarios = await responseUsuarios.json();

      if (resultadoUsuarios.exito) {
        const selectUsuario = document.getElementById("filtroUsuario");
        selectUsuario.innerHTML =
          '<option value="">Seleccione un usuario</option>';

        resultadoUsuarios.data.forEach((usuario) => {
          const option = document.createElement("option");
          option.value = usuario.id_usuarios;
          option.textContent = `${usuario.persona_nombres} ${usuario.persona_apellidos}`;
          selectUsuario.appendChild(option);
        });
      }
    } catch (error) {
      console.error("Error al cargar filtros:", error);
    }
  }

  async cargarEstadisticas(forzarActualizacion = false) {
    try {
      let url = `${APP_URL}/api/estadisticas?tipo=resumen&ambito=${this.filtroAmbito}`;

      if (this.filtroAmbito === "proyecto" && this.filtroProyectoId) {
        url += `&referencia_id=${this.filtroProyectoId}`;
      } else if (this.filtroAmbito === "usuario" && this.filtroUsuarioId) {
        url += `&referencia_id=${this.filtroUsuarioId}`;
      }

      if (forzarActualizacion) {
        url += "&forzar=1";
      }

      const response = await fetch(url);
      const resultado = await response.json();

      if (resultado.exito) {
        this.actualizarUI(resultado.data);
        if (resultado.desde_cache) {
          console.log("Datos cargados desde caché");
        }
      } else {
        throw new Error(resultado.mensaje);
      }
    } catch (error) {
      console.error("Error al cargar estadísticas:", error);
      mostrarError("No se pudieron cargar las estadísticas");
    }
  }

  actualizarUI(datos) {
    // Actualizar tarjetas de resumen
    this.actualizarTarjetasResumen(datos);

    // Actualizar gráficas
    this.actualizarGraficas(datos);
  }

  actualizarTarjetasResumen(datos) {
    document.getElementById("totalProyectos").textContent =
      datos.total_proyectos || 0;
    document.getElementById("totalDocumentos").textContent =
      datos.total_documentos || 0;
    document.getElementById("totalPersonal").textContent =
      datos.total_personal || 0;

    const tareasPendientes = datos.tareas?.pendientes || 0;
    document.getElementById("tareasPendientes").textContent = tareasPendientes;
  }

  actualizarGraficas(datos) {
    this.crearGraficaProyectosEstado(datos.proyectos_por_estado || []);
    this.crearGraficaDocumentosCategoria(datos.documentos_por_categoria || []);
    this.crearGraficaTareas(datos.tareas || {});
    this.crearGraficaTopProgramadores(datos.proyectos_por_programador || []);
  }

  crearGraficaProyectosEstado(proyectosEstado) {
    const ctx = document
      .getElementById("chartProyectosEstado")
      .getContext("2d");

    if (this.charts.proyectosEstado) {
      this.charts.proyectosEstado.destroy();
    }

    const labels = proyectosEstado.map((item) => item.estados_nombre);
    const data = proyectosEstado.map((item) => item.cantidad);
    const backgroundColors = proyectosEstado.map(
      (item) => item.estados_color || this.generarColorAleatorio()
    );

    this.charts.proyectosEstado = new Chart(ctx, {
      type: "doughnut",
      data: {
        labels: labels,
        datasets: [
          {
            data: data,
            backgroundColor: backgroundColors,
            borderWidth: 2,
            borderColor: "#fff",
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: "bottom",
          },
          title: {
            display: true,
            text: "Distribución de Proyectos",
          },
        },
      },
    });
  }

  crearGraficaDocumentosCategoria(documentosCategoria) {
    const ctx = document
      .getElementById("chartDocumentosCategoria")
      .getContext("2d");

    if (this.charts.documentosCategoria) {
      this.charts.documentosCategoria.destroy();
    }

    const labels = documentosCategoria.map(
      (item) => item.categorias_documentos_nombre
    );
    const data = documentosCategoria.map((item) => item.cantidad);

    this.charts.documentosCategoria = new Chart(ctx, {
      type: "bar",
      data: {
        labels: labels,
        datasets: [
          {
            label: "Documentos",
            data: data,
            backgroundColor: "rgba(54, 162, 235, 0.8)",
            borderColor: "rgba(54, 162, 235, 1)",
            borderWidth: 1,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              stepSize: 1,
            },
          },
        },
        plugins: {
          legend: {
            display: false,
          },
          title: {
            display: true,
            text: "Documentos por Categoría",
          },
        },
      },
    });
  }

  crearGraficaTareas(tareas) {
    const ctx = document.getElementById("chartTareas").getContext("2d");

    if (this.charts.tareas) {
      this.charts.tareas.destroy();
    }

    this.charts.tareas = new Chart(ctx, {
      type: "pie",
      data: {
        labels: ["Pendientes", "Completadas"],
        datasets: [
          {
            data: [tareas.pendientes || 0, tareas.completadas || 0],
            backgroundColor: [
              "rgba(255, 99, 132, 0.8)",
              "rgba(75, 192, 192, 0.8)",
            ],
            borderColor: ["rgba(255, 99, 132, 1)", "rgba(75, 192, 192, 1)"],
            borderWidth: 2,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: "bottom",
          },
          title: {
            display: true,
            text: "Estado de Tareas",
          },
        },
      },
    });
  }

  crearGraficaTopProgramadores(topProgramadores) {
    const ctx = document
      .getElementById("chartTopProgramadores")
      .getContext("2d");

    if (this.charts.topProgramadores) {
      this.charts.topProgramadores.destroy();
    }

    const labels = topProgramadores.map((item) =>
      `${item.persona_nombres} ${item.persona_apellidos}`.substring(0, 15)
    );
    const data = topProgramadores.map((item) => item.cantidad);

    this.charts.topProgramadores = new Chart(ctx, {
      type: "bar",
      data: {
        labels: labels,
        datasets: [
          {
            label: "Proyectos Asignados",
            data: data,
            backgroundColor: "rgba(153, 102, 255, 0.8)",
            borderColor: "rgba(153, 102, 255, 1)",
            borderWidth: 1,
          },
        ],
      },
      options: {
        indexAxis: "y",
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          x: {
            beginAtZero: true,
            ticks: {
              stepSize: 1,
            },
          },
        },
        plugins: {
          legend: {
            display: false,
          },
          title: {
            display: true,
            text: "Top Programadores",
          },
        },
      },
    });
  }

  formatearFecha(fechaStr) {
    if (!fechaStr) return "";
    const fecha = new Date(fechaStr);
    return fecha.toLocaleString("es-ES");
  }

  generarColorAleatorio() {
    return `#${Math.floor(Math.random() * 16777215).toString(16)}`;
  }

  // Limpiar todos los charts al salir
  destroy() {
    Object.values(this.charts).forEach((chart) => {
      if (chart) chart.destroy();
    });
  }
}

// Inicializar cuando el DOM esté listo
document.addEventListener("DOMContentLoaded", function () {
  window.estadisticasManager = new EstadisticasManager();
});

// Limpiar al salir de la página
window.addEventListener("beforeunload", function () {
  if (window.estadisticasManager) {
    window.estadisticasManager.destroy();
  }
});

// Vista Guiada - Estadísticas
document
  .getElementById("btnVistaGuiada")
  ?.addEventListener("click", function (e) {
    e.preventDefault();

    const steps = [
      {
        intro:
          "¡Bienvenido al módulo de Estadísticas! Aquí puedes ver análisis detallados del sistema.",
      },
      {
        element: "#filtroAmbito",
        intro:
          "Selecciona el ámbito de las estadísticas: Global, por Proyecto o por Usuario.",
        position: "bottom",
      },
      {
        element: "#filtroProyectoContainer",
        intro: "Si seleccionas 'Proyecto', aquí eliges el proyecto específico.",
        position: "bottom",
      },
      {
        element: "#filtroUsuarioContainer",
        intro: "Si seleccionas 'Usuario', aquí eliges el usuario específico.",
        position: "bottom",
      },
      {
        element: "#btnActualizarEstadisticas",
        intro:
          "Haz clic aquí para actualizar las estadísticas con los filtros seleccionados.",
        position: "left",
      },
      {
        element: "#totalProyectos",
        intro: "Total de proyectos en el sistema.",
        position: "top",
      },
      {
        element: "#totalDocumentos",
        intro: "Total de documentos cargados.",
        position: "top",
      },
      {
        element: "#totalPersonal",
        intro: "Total de personal asignado a proyectos.",
        position: "top",
      },
      {
        element: "#tareasPendientes",
        intro: "Tareas pendientes por completar.",
        position: "top",
      },
      {
        element: "#chartProyectosEstado",
        intro:
          "Distribución de proyectos por estado (Pendiente, En Proceso, Completado, etc.).",
        position: "top",
      },
      {
        element: "#chartDocumentosCategoria",
        intro: "Documentos organizados por categorías.",
        position: "top",
      },
      {
        element: "#chartTareas",
        intro: "Estado general de las tareas (Pendientes vs Completadas).",
        position: "top",
      },
      {
        element: "#chartTopProgramadores",
        intro: "Top de programadores con más proyectos asignados.",
        position: "top",
      },
      {
        intro:
          "¡Listo! Usa los filtros para analizar diferentes perspectivas del sistema.",
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
