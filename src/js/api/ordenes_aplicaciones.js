import { CrudManager } from "../helpers/crud.js";
import { cargarSelect, initSelect2 } from "../helpers/selects.js";
import { formatearFecha } from "../helpers/utilidades.js";

const APP_URL = window.location.pathname.includes("sistema_aplicaciones")
  ? "/sistema_aplicaciones"
  : "";

// Función para verificar si todas las tareas están completadas
async function verificarTareasCompletadas(proyectoId) {
  try {
    const response = await fetch(
      `${APP_URL}/api/tareas?proyecto_id=${proyectoId}&situacion=1`
    );

    if (!response.ok) {
      throw new Error(`Error HTTP: ${response.status}`);
    }

    const resultado = await response.json();

    if (resultado.exito && resultado.data && resultado.data.length > 0) {
      // Verificar si hay tareas y si todas están completadas
      const tareas = resultado.data;
      const todasCompletadas = tareas.every(
        (tarea) => tarea.tareas_aplicaciones_completada == 1
      );

      return {
        hayTareas: tareas.length > 0,
        todasCompletadas: todasCompletadas,
      };
    }

    return { hayTareas: false, todasCompletadas: false };
  } catch (error) {
    console.error("Error al verificar tareas completadas:", error);
    return { hayTareas: false, todasCompletadas: false };
  }
}

// Función para marcar un proyecto como completado
async function marcarProyectoCompletado(proyectoId) {
  // Usar SweetAlert2 para la confirmación
  const { isConfirmed } = await Swal.fire({
    title: "¿Marcar como completado?",
    text: "¿Está seguro de marcar este proyecto como completado?",
    icon: "question",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Sí, marcar como completado",
    cancelButtonText: "Cancelar",
  });

  if (!isConfirmed) return;

  try {
    const response = await fetch(
      `${APP_URL}/api/proyectos-asignados/marcar-completado`,
      {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ id: proyectoId }),
      }
    );

    if (!response.ok) {
      throw new Error(`Error HTTP: ${response.status}`);
    }

    const resultado = await response.json();

    if (resultado.exito) {
      Swal.fire({
        icon: "success",
        title: "Proyecto Completado",
        text:
          resultado.mensaje ||
          "El proyecto ha sido marcado como completado exitosamente",
        timer: 2000,
        showConfirmButton: false,
      });

      // Recargar la lista de proyectos
      proyectoAsignadoManager.cargarDatos();
    } else {
      Swal.fire({
        icon: "error",
        title: "Error",
        text:
          resultado.mensaje || "No se pudo marcar el proyecto como completado",
      });
    }
  } catch (error) {
    console.error("Error al marcar proyecto como completado:", error);
    Swal.fire({
      icon: "error",
      title: "Error",
      text: "Error de conexión al servidor",
    });
  }
}

async function marcarProyectoRecibido(proyectoId) {
  // Usar SweetAlert2 para la confirmación
  const { isConfirmed } = await Swal.fire({
    title: "¿Marcar como recibido?",
    text: "¿Está seguro de marcar este proyecto como recibido?",
    icon: "question",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Sí, marcar como recibido",
    cancelButtonText: "Cancelar",
  });

  if (!isConfirmed) return;

  try {
    const response = await fetch(
      `${APP_URL}/api/proyectos-asignados/marcar-recibida`,
      {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ id: proyectoId }),
      }
    );

    const resultado = await response.json();

    if (resultado.exito) {
      Swal.fire({
        icon: "success",
        title: "Proyecto Recibido",
        text: resultado.mensaje,
        timer: 2000,
        showConfirmButton: false,
      });
      proyectoAsignadoManager.cargarDatos();
    } else {
      Swal.fire({
        icon: "error",
        title: "Error",
        text: resultado.mensaje,
      });
    }
  } catch (error) {
    console.error("Error al marcar proyecto:", error);
    Swal.fire({
      icon: "error",
      title: "Error",
      text: "No se pudo marcar el proyecto como recibido",
    });
  }
}

// Cache de estados de tareas para no hacer múltiples solicitudes
const tareasCache = new Map();

function generarAccionesPersonalizadas(item) {
  const esPendiente = item.estados_nombre === "Pendiente";
  const esEnProceso = item.estados_nombre === "En Proceso";
  const proyectoId = item.id_ordenes_aplicaciones;

  // Iniciar el HTML vacío
  let html = "";

  // Opción para marcar como recibido (proyectos pendientes)
  if (esPendiente) {
    html += `
      <a class="dropdown-item" href="#" data-accion="marcar-recibida" data-proyecto-id="${proyectoId}">
        <i class="fas fa-check-circle text-success"></i> Marcar como Recibida
      </a>
      <div class="dropdown-divider"></div>
    `;
  }

  // Para proyectos en proceso
  if (esEnProceso) {
    html += `<div id="btn-completar-${proyectoId}"></div>`;

    // Verificar tareas completadas de forma asíncrona
    verificarTareasCompletadas(proyectoId)
      .then((estado) => {
        // Guardar en cache
        tareasCache.set(proyectoId, estado);

        // Actualizar el DOM con el botón si corresponde
        const btnContainer = document.getElementById(
          `btn-completar-${proyectoId}`
        );
        if (btnContainer && estado.hayTareas && estado.todasCompletadas) {
          btnContainer.innerHTML = `
          <a class="dropdown-item" href="#" data-accion="marcar-completado" data-proyecto-id="${proyectoId}">
            <i class="fas fa-check-double text-success"></i> Marcar como Completado
          </a>
          <div class="dropdown-divider"></div>
        `;
        }
      })
      .catch((error) => {
        console.error("Error verificando tareas:", error);
      });
  }

  // Opciones estándar para todos los proyectos
  html += `
    <a class="dropdown-item" href="#" data-accion="gestionar-tareas" data-proyecto-id="${proyectoId}">
      <i class="fas fa-tasks text-primary"></i> Gestionar Tareas
    </a>
    <a class="dropdown-item" href="#" data-accion="equipo-trabajo" data-proyecto-id="${proyectoId}">
      <i class="fas fa-users text-info"></i> Equipo de Trabajo
    </a>
    <a class="dropdown-item" href="#" data-accion="documentos" data-proyecto-id="${proyectoId}">
      <i class="fas fa-file-alt text-secondary"></i> Documentos
    </a>
  `;

  return html;
}

const proyectoAsignadoManager = new CrudManager({
  entidad: "Proyecto Asignado",
  entidadPlural: "Proyectos Asignados",
  apiBase: `${APP_URL}/api/proyectos-asignados`,
  idCampo: "id_ordenes_aplicaciones",
  tablaId: "tablaProyectosAsignados",
  modalId: "modalProyectoAsignado",
  formId: "formProyectoAsignado",
  btnNuevo: "btnNuevoProyectoAsignado",
  columnas: [
    (item) => `<td>${item.ordenes_aplicaciones_codigo || ""}</td>`,
    (item) => `<td>${item.aplicacion_nombre || ""}</td>`,
    (item) =>
      `<td>${item.persona_nombres || ""} ${item.persona_apellidos || ""}</td>`,
    (item) => `<td>
      <span class="badge" style="background-color: ${
        item.estados_color || "#3788d8"
      }; color: white;">
        ${item.estados_nombre || "Sin estado"}
      </span>
    </td>`,
    (item) =>
      `<td>${formatearFecha(
        item.ordenes_aplicaciones_fecha_entrega,
        false
      )}</td>`,
  ],
  camposVer: (item) => `
    <div class="text-left">
      <p><strong>Código:</strong> ${item.ordenes_aplicaciones_codigo || ""}</p>
      <p><strong>Proyecto:</strong> ${item.aplicacion_nombre || ""}</p>
      <p><strong>Responsable:</strong> ${item.persona_nombres || ""} ${
    item.persona_apellidos || ""
  }</p>
      <p><strong>Estado:</strong> 
        <span class="badge" style="background-color: ${
          item.estados_color || "#3788d8"
        }; color: white;">
          ${item.estados_nombre || "Sin estado"}
        </span>
      </p>
      <p><strong>Fecha Asignación:</strong> ${formatearFecha(
        item.ordenes_aplicaciones_fecha_asignacion
      )}</p>
      <p><strong>Fecha Entrega:</strong> ${formatearFecha(
        item.ordenes_aplicaciones_fecha_entrega,
        false
      )}</p>
      <p><strong>Notas:</strong> ${
        item.ordenes_aplicaciones_notas || "Sin notas"
      }</p>
    </div>
  `,
  onModalShow: async () => {
    initSelect2("#aplicacion_id_aplicacion");
    initSelect2("#usuarios_id_usuarios");
    initSelect2("#estados_id_estados");

    await cargarSelect(
      `${APP_URL}/api/proyectos?situacion=1`,
      "aplicacion_id_aplicacion",
      "id_aplicacion",
      "aplicacion_nombre"
    );

    await cargarSelect(
      `${APP_URL}/api/usuarios?situacion=1`,
      "usuarios_id_usuarios",
      "id_usuarios",
      "nombre_completo_identidad"
    );

    await cargarSelect(
      `${APP_URL}/api/estados?situacion=1`,
      "estados_id_estados",
      "id_estados",
      "estados_nombre"
    );

    if (!document.getElementById("id_ordenes_aplicaciones").value) {
      const fechaEntrega = document.getElementById(
        "ordenes_aplicaciones_fecha_entrega"
      );
      const hoy = new Date();
      hoy.setDate(hoy.getDate() + 14);
      fechaEntrega.value = hoy.toISOString().split("T")[0];
    }
  },
  accionesPersonalizadas: generarAccionesPersonalizadas,
});

// Event listener global para manejar acciones
document.addEventListener("click", (e) => {
  const marcarRecibida = e.target.closest('[data-accion="marcar-recibida"]');
  if (marcarRecibida) {
    e.preventDefault();
    const proyectoId = marcarRecibida.dataset.proyectoId;
    marcarProyectoRecibido(proyectoId);
    return;
  }

  const marcarCompletado = e.target.closest(
    '[data-accion="marcar-completado"]'
  );
  if (marcarCompletado) {
    e.preventDefault();
    const proyectoId = marcarCompletado.dataset.proyectoId;
    marcarProyectoCompletado(proyectoId);
    return;
  }

  const gestionarTareas = e.target.closest('[data-accion="gestionar-tareas"]');
  if (gestionarTareas) {
    e.preventDefault();
    const proyectoId = gestionarTareas.dataset.proyectoId;
    window.location.href = `${APP_URL}/tareas?proyecto_id=${proyectoId}`;
    return;
  }

  const equipoTrabajo = e.target.closest('[data-accion="equipo-trabajo"]');
  if (equipoTrabajo) {
    e.preventDefault();
    const proyectoId = equipoTrabajo.dataset.proyectoId;
    window.location.href = `${APP_URL}/personal-proyecto?proyecto_id=${proyectoId}`;
    return;
  }

  const documentos = e.target.closest('[data-accion="documentos"]');
  if (documentos) {
    e.preventDefault();
    const proyectoId = documentos.dataset.proyectoId;
    window.location.href = `${APP_URL}/documentos?proyecto_id=${proyectoId}`;
    return;
  }
});

// Añadir al final del archivo, justo antes de window.proyectoAsignadoManager = proyectoAsignadoManager;
document.addEventListener("DOMContentLoaded", function () {
  // Crear estilo para forzar que los dropdowns se muestren correctamente
  const style = document.createElement("style");
  style.textContent = `
    /* Fix dropdown dentro de tablas */
    .table-responsive {
      overflow: visible !important;
    }
    .dataTables_wrapper {
      overflow: visible !important;
    }
    .table-responsive .dropdown-menu {
      position: absolute !important;
      z-index: 9999 !important;
      right: 0 !important;
      left: auto !important;
    }
    .table-responsive td {
      position: relative !important;
      overflow: visible !important;
    }
    .table-responsive th {
      position: relative !important;
      overflow: visible !important;
    }
  `;
  document.head.appendChild(style);
});

window.proyectoAsignadoManager = proyectoAsignadoManager;

// Vista Guiada - Proyectos Asignados
document
  .getElementById("btnVistaGuiada")
  ?.addEventListener("click", function (e) {
    e.preventDefault();

    const steps = [
      {
        intro:
          "¡Bienvenido al módulo de Proyectos Asignados! Aquí gestionas todos los proyectos del sistema.",
      },
      {
        element: "#btnNuevoProyectoAsignado",
        intro:
          "Haz clic aquí para asignar un nuevo proyecto a un desarrollador.",
        position: "left",
      },
      {
        element: "#tablaProyectosAsignados",
        intro: "Lista completa de proyectos asignados en el sistema.",
        position: "top",
      },
      {
        element: "#tablaProyectosAsignados thead th:nth-child(1)",
        intro: "Código único del proyecto asignado.",
        position: "bottom",
      },
      {
        element: "#tablaProyectosAsignados thead th:nth-child(2)",
        intro: "Nombre de la aplicación/proyecto.",
        position: "bottom",
      },
      {
        element: "#tablaProyectosAsignados thead th:nth-child(3)",
        intro: "Desarrollador responsable del proyecto.",
        position: "bottom",
      },
      {
        element: "#tablaProyectosAsignados thead th:nth-child(4)",
        intro: "Estado actual del proyecto.",
        position: "bottom",
      },
      {
        element: "#tablaProyectosAsignados thead th:nth-child(5)",
        intro: "Fecha límite de entrega.",
        position: "bottom",
      },
      {
        element: "#tablaProyectosAsignados thead th:nth-child(6)",
        intro: "Acciones disponibles para cada proyecto.",
        position: "bottom",
      },
      {
        element: "#tablaProyectosAsignados tbody tr:first-child td:last-child",
        intro:
          "Menú de acciones con opciones específicas según el estado del proyecto.",
        position: "left",
      },
      {
        intro:
          "Opciones del menú: Marcar como Recibida, Completado, Gestionar Tareas, Equipo de Trabajo y Documentos.",
      },
      {
        intro:
          "¡Listo! Ahora puedes gestionar eficientemente todos los proyectos asignados.",
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
