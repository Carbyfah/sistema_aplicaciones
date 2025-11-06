document.addEventListener("DOMContentLoaded", function () {
  console.log("Sistema de tareas cargado");

  // Configuración inicial
  const toggleEliminadas = document.getElementById("toggleEliminados");
  const toggleCompletadas = document.getElementById("toggleCompletadas");
  const btnNuevaTarea = document.getElementById("btnNuevaTarea");

  // Cargar proyectos en el selector
  cargarProyectos();

  // Verificar si hay un proyecto en la URL
  const urlParams = new URLSearchParams(window.location.search);
  const proyectoId = urlParams.get("proyecto_id");
  if (proyectoId) {
    // Si hay un proyecto en la URL, seleccionarlo
    setTimeout(() => {
      seleccionarProyectoDesdeURL(proyectoId);
    }, 500);
  }

  // Event listeners
  document
    .getElementById("selectProyecto")
    .addEventListener("change", function () {
      const proyectoId = this.value;
      if (proyectoId) {
        cargarTareas(proyectoId);
        btnNuevaTarea.disabled = false;
      } else {
        document.getElementById("tareasContainer").innerHTML = `
                <div class="text-center p-5 text-muted">
                    <i class="fas fa-tasks fa-3x mb-3"></i>
                    <h5>Seleccione un proyecto para visualizar sus tareas</h5>
                </div>
            `;
        btnNuevaTarea.disabled = true;
      }
    });

  if (toggleEliminadas) {
    toggleEliminadas.addEventListener("change", function () {
      const proyectoId = document.getElementById("selectProyecto").value;
      if (proyectoId) {
        cargarTareas(proyectoId);
      }
    });
  }

  if (toggleCompletadas) {
    toggleCompletadas.addEventListener("change", function () {
      const proyectoId = document.getElementById("selectProyecto").value;
      if (proyectoId) {
        cargarTareas(proyectoId);
      }
    });
  }

  if (btnNuevaTarea) {
    btnNuevaTarea.addEventListener("click", function () {
      abrirModalNuevaTarea();
    });
  }

  // Event listener para el formulario
  const formTarea = document.getElementById("formTarea");
  if (formTarea) {
    formTarea.addEventListener("submit", function (e) {
      e.preventDefault();
      guardarTarea();
    });
  }

  // Event delegation para las acciones de tareas
  document.body.addEventListener("click", function (e) {
    const target = e.target.closest("[data-accion]");
    if (!target) return;

    e.preventDefault();
    const accion = target.dataset.accion;
    const id = target.dataset.id;

    switch (accion) {
      case "ver":
        verTarea(id);
        break;
      case "editar":
        editarTarea(id);
        break;
      case "eliminar":
        eliminarTarea(id);
        break;
      case "completar":
        completarTarea(id);
        break;
      case "reabrir":
        reabrirTarea(id);
        break;
      case "recuperar":
        recuperarTarea(id);
        break;
    }
  });
});

function verTarea(tareaId) {
  // Mostrar loading en el modal
  const contenido = document.getElementById("modalVerTareaContenido");
  contenido.innerHTML = `
    <div class="text-center py-5">
      <i class="fas fa-spinner fa-spin fa-3x text-muted"></i>
      <p class="mt-3 text-muted">Cargando detalles de la tarea...</p>
    </div>
  `;

  // Mostrar el modal
  $("#modalVerTarea").modal("show");

  // Obtener datos de la tarea
  fetch(`/sistema_aplicaciones/api/tareas?situacion=1`)
    .then((response) => response.json())
    .then((data) => {
      if (data.exito) {
        const tarea = data.data.find(
          (t) => t.id_tareas_aplicaciones == tareaId
        );

        if (!tarea) {
          contenido.innerHTML = `
            <div class="alert alert-danger">
              <i class="fas fa-exclamation-circle"></i> Tarea no encontrada
            </div>
          `;
          return;
        }

        // Formatear fechas
        const fechaCreacion = tarea.tareas_aplicaciones_fecha_creacion
          ? new Date(tarea.tareas_aplicaciones_fecha_creacion).toLocaleString()
          : "No disponible";

        const fechaLimite = tarea.tareas_aplicaciones_fecha_limite
          ? new Date(tarea.tareas_aplicaciones_fecha_limite).toLocaleString()
          : "No establecida";

        const fechaCompletada = tarea.tareas_aplicaciones_fecha_completada
          ? new Date(
              tarea.tareas_aplicaciones_fecha_completada
            ).toLocaleString()
          : "No completada";

        // Determinar estado
        let estado = "Pendiente";
        let estadoClass = "warning";
        if (tarea.tareas_aplicaciones_completada == 1) {
          estado = "Completada";
          estadoClass = "success";
        }
        if (tarea.tareas_aplicaciones_situacion == 0) {
          estado = "Eliminada";
          estadoClass = "danger";
        }

        // Determinar clase de prioridad
        const prioridadClass = getBadgeClass(
          tarea.tareas_aplicaciones_prioridad
        );

        // Mostrar detalles en el modal
        contenido.innerHTML = `
          <div class="row">
            <div class="col-md-12">
              <div class="info-card mb-3">
                <div class="info-label">Título</div>
                <div class="info-value">${escapeHTML(
                  tarea.tareas_aplicaciones_titulo
                )}</div>
              </div>
            </div>
            
            ${
              tarea.tareas_aplicaciones_descripcion
                ? `
            <div class="col-md-12">
              <div class="info-card mb-3">
                <div class="info-label">Descripción</div>
                <div class="info-value">${escapeHTML(
                  tarea.tareas_aplicaciones_descripcion
                )}</div>
              </div>
            </div>
            `
                : ""
            }
            
            <div class="col-md-6">
              <div class="info-card mb-3">
                <div class="info-label">Estado</div>
                <div class="info-value">
                  <span class="badge badge-${estadoClass}">${estado}</span>
                </div>
              </div>
            </div>
            
            <div class="col-md-6">
              <div class="info-card mb-3">
                <div class="info-label">Prioridad</div>
                <div class="info-value">
                  <span class="badge badge-${prioridadClass}">${
          tarea.tareas_aplicaciones_prioridad
        }</span>
                </div>
              </div>
            </div>
            
            <div class="col-md-6">
              <div class="info-card mb-3">
                <div class="info-label">Fecha de Creación</div>
                <div class="info-value">${fechaCreacion}</div>
              </div>
            </div>
            
            <div class="col-md-6">
              <div class="info-card mb-3">
                <div class="info-label">Fecha Límite</div>
                <div class="info-value">${fechaLimite}</div>
              </div>
            </div>
            
            ${
              tarea.tareas_aplicaciones_completada == 1
                ? `
            <div class="col-md-12">
              <div class="info-card mb-3">
                <div class="info-label">Fecha de Completación</div>
                <div class="info-value">${fechaCompletada}</div>
              </div>
            </div>
            `
                : ""
            }
            
            <div class="col-md-12">
              <div class="info-card mb-3">
                <div class="info-label">ID de la Tarea</div>
                <div class="info-value">${tarea.id_tareas_aplicaciones}</div>
              </div>
            </div>
          </div>
        `;

        // Actualizar título del modal
        document.getElementById(
          "modalVerTareaTitulo"
        ).textContent = `Detalles: ${escapeHTML(
          tarea.tareas_aplicaciones_titulo
        )}`;
      } else {
        contenido.innerHTML = `
          <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i> Error al cargar los detalles de la tarea
          </div>
        `;
      }
    })
    .catch((error) => {
      console.error("Error al cargar detalles de la tarea:", error);
      contenido.innerHTML = `
        <div class="alert alert-danger">
          <i class="fas fa-exclamation-circle"></i> Error de conexión al cargar detalles
        </div>
      `;
    });
}

function cargarProyectos() {
  const select = document.getElementById("selectProyecto");

  // Mostrar indicador de carga
  select.innerHTML = '<option value="">Cargando proyectos...</option>';

  // Obtener proyectos vía AJAX
  fetch("/sistema_aplicaciones/api/proyectos-asignados")
    .then((response) => response.json())
    .then((data) => {
      if (data.exito) {
        // Limpiar select
        select.innerHTML = '<option value="">Seleccione un proyecto</option>';

        // Agregar cada proyecto
        data.data.forEach((proyecto) => {
          const option = document.createElement("option");
          option.value = proyecto.id_ordenes_aplicaciones;
          option.textContent =
            proyecto.aplicacion_nombre || proyecto.ordenes_aplicaciones_codigo;
          select.appendChild(option);
        });

        console.log("Proyectos cargados:", data.data.length);
      } else {
        select.innerHTML =
          '<option value="">Error al cargar proyectos</option>';
        console.error("Error al cargar proyectos:", data.mensaje);
      }
    })
    .catch((error) => {
      select.innerHTML = '<option value="">Error de conexión</option>';
      console.error("Error de conexión al cargar proyectos:", error);
    });
}

function seleccionarProyectoDesdeURL(proyectoId) {
  const select = document.getElementById("selectProyecto");
  select.value = proyectoId;

  // Disparar evento change para cargar las tareas
  const event = new Event("change");
  select.dispatchEvent(event);
}

function cargarTareas(proyectoId) {
  console.log("Cargando tareas del proyecto:", proyectoId);

  const tareasContainer = document.getElementById("tareasContainer");
  const mostrarEliminadas =
    document.getElementById("toggleEliminados")?.checked || false;
  const mostrarCompletadas =
    document.getElementById("toggleCompletadas")?.checked || true;

  // Mostrar indicador de carga
  tareasContainer.innerHTML = `
        <div class="text-center p-3">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Cargando...</span>
            </div>
            <p class="mt-2">Cargando tareas...</p>
        </div>
    `;

  fetch(
    `/sistema_aplicaciones/api/tareas?proyecto_id=${proyectoId}&situacion=${
      mostrarEliminadas ? "0" : "1"
    }`
  )
    .then((response) => response.json())
    .then((data) => {
      if (data.exito) {
        // Filtrar tareas según configuración
        let tareas = data.data || [];

        if (!mostrarCompletadas) {
          tareas = tareas.filter(
            (tarea) => tarea.tareas_aplicaciones_completada == 0
          );
        }

        if (tareas.length === 0) {
          tareasContainer.innerHTML = `
                        <div class="text-center p-5 text-muted">
                            <i class="fas fa-clipboard-check fa-3x mb-3"></i>
                            <h5>No hay tareas para este proyecto</h5>
                            <p>Utilice el botón "Nueva Tarea" para agregar tareas.</p>
                        </div>
                    `;
          return;
        }

        // Generar tabla de tareas
        let html = `
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="50px">Estado</th>
                                <th>Título</th>
                                <th>Prioridad</th>
                                <th>Descripción</th>
                                <th width="120px">Fecha Límite</th>
                                <th width="120px">Completada</th>
                                <th width="150px" class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                `;

        tareas.forEach((tarea) => {
          const estaEliminada = tarea.tareas_aplicaciones_situacion == 0;
          const estaCompletada = tarea.tareas_aplicaciones_completada == 1;

          // Clases y estilos
          const rowClass = estaEliminada
            ? "table-danger"
            : estaCompletada
            ? "table-success"
            : "";
          const iconClass = estaEliminada
            ? "fa-trash text-danger"
            : estaCompletada
            ? "fa-check-circle text-success"
            : "fa-clock text-warning";
          const statusText = estaEliminada
            ? "Eliminada"
            : estaCompletada
            ? "Completada"
            : "Pendiente";

          // Formatear fechas
          const fechaCompletada = tarea.tareas_aplicaciones_fecha_completada
            ? new Date(
                tarea.tareas_aplicaciones_fecha_completada
              ).toLocaleDateString()
            : "N/A";

          const fechaLimite = tarea.tareas_aplicaciones_fecha_limite
            ? new Date(
                tarea.tareas_aplicaciones_fecha_limite
              ).toLocaleDateString()
            : "Sin fecha";

          // Generar HTML de la fila
          html += `
                        <tr class="${rowClass}">
                            <td class="text-center">
                                <i class="fas ${iconClass}" title="${statusText}"></i>
                            </td>
                            <td>${escapeHTML(
                              tarea.tareas_aplicaciones_titulo
                            )}</td>
                            <td>
                                <span class="badge badge-${getBadgeClass(
                                  tarea.tareas_aplicaciones_prioridad
                                )}">
                                    ${tarea.tareas_aplicaciones_prioridad}
                                </span>
                            </td>
                            <td>${
                              tarea.tareas_aplicaciones_descripcion
                                ? escapeHTML(
                                    tarea.tareas_aplicaciones_descripcion
                                  )
                                : '<em class="text-muted">Sin descripción</em>'
                            }</td>
                            <td>${fechaLimite}</td>
                            <td class="text-center">
                                ${
                                  estaCompletada
                                    ? `<span class="badge badge-success">${fechaCompletada}</span>`
                                    : '<span class="badge badge-secondary">Pendiente</span>'
                                }
                            </td>
                            <td class="text-center">
                    `;

          // Para tareas eliminadas:
          if (estaEliminada) {
            html += `
              <div class="dropdown">
                <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton-${tarea.id_tareas_aplicaciones}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  Acciones
                </button>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton-${tarea.id_tareas_aplicaciones}">
                  <a class="dropdown-item" href="#" data-accion="recuperar" data-id="${tarea.id_tareas_aplicaciones}">
                    <i class="fas fa-undo text-success"></i> Recuperar
                  </a>
                </div>
              </div>
            `;
          } else {
            // Para tareas activas (pendientes o completadas)
            html += `
              <div class="dropdown">
                <button class="btn btn-sm btn-primary dropdown-toggle" type="button" id="dropdownMenuButton-${
                  tarea.id_tareas_aplicaciones
                }" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  Acciones
                </button>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton-${
                  tarea.id_tareas_aplicaciones
                }">
                  <a class="dropdown-item" href="#" data-accion="ver" data-id="${
                    tarea.id_tareas_aplicaciones
                  }">
                    <i class="fas fa-eye text-info"></i> Ver
                  </a>
                  <a class="dropdown-item" href="#" data-accion="editar" data-id="${
                    tarea.id_tareas_aplicaciones
                  }">
                    <i class="fas fa-edit text-primary"></i> Editar
                  </a>
                  ${
                    !estaCompletada
                      ? `<a class="dropdown-item" href="#" data-accion="completar" data-id="${tarea.id_tareas_aplicaciones}">
                        <i class="fas fa-check text-success"></i> Completar
                      </a>`
                      : `<a class="dropdown-item" href="#" data-accion="reabrir" data-id="${tarea.id_tareas_aplicaciones}">
                        <i class="fas fa-redo text-warning"></i> Reabrir
                      </a>`
                  }
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item" href="#" data-accion="eliminar" data-id="${
                    tarea.id_tareas_aplicaciones
                  }">
                    <i class="fas fa-trash text-danger"></i> Eliminar
                  </a>
                </div>
              </div>
            `;
          }

          html += `
                            </td>
                        </tr>
                    `;
        });

        html += `
                        </tbody>
                    </table>
                `;

        tareasContainer.innerHTML = html;
        console.log("Tareas cargadas:", tareas.length);
      } else {
        tareasContainer.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i> Error al cargar tareas: ${
                          data.mensaje || "Error desconocido"
                        }
                    </div>
                `;
        console.error("Error al cargar tareas:", data.mensaje);
      }
    })
    .catch((error) => {
      tareasContainer.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> Error de conexión al cargar tareas
                </div>
            `;
      console.error("Error de conexión al cargar tareas:", error);
    });
}

// Función helper para los colores de prioridad (debe estar fuera de cargarTareas)
function getBadgeClass(prioridad) {
  switch (prioridad) {
    case "Alta":
      return "danger";
    case "Media":
      return "warning";
    case "Baja":
      return "info";
    default:
      return "secondary";
  }
}

function abrirModalNuevaTarea() {
  const proyectoId = document.getElementById("selectProyecto").value;

  if (!proyectoId) {
    alert("Seleccione un proyecto primero");
    return;
  }

  // Limpiar formulario
  document.getElementById("formTarea").reset();

  // Establecer valores por defecto
  document.getElementById("id_tareas_aplicaciones").value = "";
  document.getElementById(
    "ordenes_aplicaciones_id_ordenes_aplicaciones"
  ).value = proyectoId;
  document.getElementById("tareas_aplicaciones_prioridad").value = "Media";

  // Actualizar título del modal
  document.getElementById("modalTareaTitulo").textContent = "Nueva Tarea";

  // Mostrar el modal
  $("#modalTarea").modal("show");
}

function editarTarea(tareaId) {
  // Obtener datos de la tarea
  fetch(`/sistema_aplicaciones/api/tareas?situacion=1`)
    .then((response) => response.json())
    .then((data) => {
      if (data.exito) {
        const tarea = data.data.find(
          (t) => t.id_tareas_aplicaciones == tareaId
        );

        if (!tarea) {
          alert("Tarea no encontrada");
          return;
        }

        // Llenar el formulario
        document.getElementById("id_tareas_aplicaciones").value =
          tarea.id_tareas_aplicaciones;
        document.getElementById(
          "ordenes_aplicaciones_id_ordenes_aplicaciones"
        ).value = tarea.ordenes_aplicaciones_id_ordenes_aplicaciones;
        document.getElementById("tareas_aplicaciones_titulo").value =
          tarea.tareas_aplicaciones_titulo || "";
        document.getElementById("tareas_aplicaciones_descripcion").value =
          tarea.tareas_aplicaciones_descripcion || "";
        document.getElementById("tareas_aplicaciones_prioridad").value =
          tarea.tareas_aplicaciones_prioridad || "Media";

        // Formatear fecha para el input datetime-local
        if (tarea.tareas_aplicaciones_fecha_limite) {
          const fecha = new Date(tarea.tareas_aplicaciones_fecha_limite);
          const fechaFormateada = fecha.toISOString().slice(0, 16);
          document.getElementById("tareas_aplicaciones_fecha_limite").value =
            fechaFormateada;
        } else {
          document.getElementById("tareas_aplicaciones_fecha_limite").value =
            "";
        }

        // Actualizar título del modal
        document.getElementById("modalTareaTitulo").textContent =
          "Editar Tarea";

        // Mostrar el modal
        $("#modalTarea").modal("show");
      } else {
        alert("Error al cargar detalles de la tarea");
        console.error("Error al cargar detalles de la tarea:", data.mensaje);
      }
    })
    .catch((error) => {
      alert("Error de conexión al cargar detalles de la tarea");
      console.error("Error de conexión al cargar detalles:", error);
    });
}

function guardarTarea() {
  const formData = new FormData(document.getElementById("formTarea"));
  const datos = Object.fromEntries(formData.entries());

  // Validar datos
  if (!datos.tareas_aplicaciones_titulo.trim()) {
    alert("El título de la tarea es obligatorio");
    return;
  }

  // Enviar datos
  fetch("/sistema_aplicaciones/api/tareas", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(datos),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.exito) {
        // Cerrar modal
        $("#modalTarea").modal("hide");

        // Mostrar mensaje
        mostrarNotificacion("Tarea guardada correctamente", "success");

        // Recargar tareas
        const proyectoId = document.getElementById("selectProyecto").value;
        cargarTareas(proyectoId);
      } else {
        mostrarNotificacion("Error: " + data.mensaje, "error");
      }
    })
    .catch((error) => {
      console.error("Error al guardar tarea:", error);
      mostrarNotificacion("Error de conexión al guardar", "error");
    });
}

function eliminarTarea(tareaId) {
  if (confirm("¿Está seguro de eliminar esta tarea?")) {
    fetch("/sistema_aplicaciones/api/tareas/eliminar", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ id: tareaId }),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.exito) {
          mostrarNotificacion("Tarea eliminada correctamente", "success");

          // Recargar tareas
          const proyectoId = document.getElementById("selectProyecto").value;
          cargarTareas(proyectoId);
        } else {
          mostrarNotificacion("Error: " + data.mensaje, "error");
        }
      })
      .catch((error) => {
        console.error("Error al eliminar tarea:", error);
        mostrarNotificacion("Error de conexión al eliminar", "error");
      });
  }
}

function completarTarea(tareaId) {
  fetch("/sistema_aplicaciones/api/tareas/marcar-completada", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({ id: tareaId }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.exito) {
        mostrarNotificacion("Tarea completada", "success");

        // Recargar tareas
        const proyectoId = document.getElementById("selectProyecto").value;
        cargarTareas(proyectoId);
      } else {
        mostrarNotificacion("Error: " + data.mensaje, "error");
      }
    })
    .catch((error) => {
      console.error("Error al completar tarea:", error);
      mostrarNotificacion("Error de conexión", "error");
    });
}

function reabrirTarea(tareaId) {
  fetch("/sistema_aplicaciones/api/tareas/reabrir", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({ id: tareaId }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.exito) {
        mostrarNotificacion("Tarea reabierta", "success");

        // Recargar tareas
        const proyectoId = document.getElementById("selectProyecto").value;
        cargarTareas(proyectoId);
      } else {
        mostrarNotificacion("Error: " + data.mensaje, "error");
      }
    })
    .catch((error) => {
      console.error("Error al reabrir tarea:", error);
      mostrarNotificacion("Error de conexión", "error");
    });
}

function recuperarTarea(tareaId) {
  fetch("/sistema_aplicaciones/api/tareas/recuperar", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({ id: tareaId }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.exito) {
        mostrarNotificacion("Tarea recuperada", "success");

        // Recargar tareas
        const proyectoId = document.getElementById("selectProyecto").value;
        cargarTareas(proyectoId);
      } else {
        mostrarNotificacion("Error: " + data.mensaje, "error");
      }
    })
    .catch((error) => {
      console.error("Error al recuperar tarea:", error);
      mostrarNotificacion("Error de conexión", "error");
    });
}

function mostrarNotificacion(mensaje, tipo = "info") {
  if (typeof Swal !== "undefined") {
    Swal.fire({
      title: mensaje,
      icon: tipo,
      toast: true,
      position: "top-end",
      showConfirmButton: false,
      timer: 3000,
    });
  } else {
    alert(mensaje);
  }
}

function escapeHTML(str) {
  if (!str) return "";
  return str
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;");
}

// Vista Guiada - Tareas
document
  .getElementById("btnVistaGuiada")
  ?.addEventListener("click", function (e) {
    e.preventDefault();

    const steps = [
      {
        intro:
          "¡Bienvenido al módulo de Tareas! Aquí gestionas las tareas de cada proyecto.",
      },
      {
        element: "#selectProyecto",
        intro: "Selecciona un proyecto para ver y gestionar sus tareas.",
        position: "bottom",
      },
      {
        element: "#toggleEliminados",
        intro:
          "Activa este switch para ver tareas eliminadas y poder recuperarlas.",
        position: "left",
      },
      {
        element: "#toggleCompletadas",
        intro: "Desactiva este switch para ocultar las tareas ya completadas.",
        position: "left",
      },
      {
        element: "#btnNuevaTarea",
        intro:
          "Haz clic aquí para crear una nueva tarea en el proyecto seleccionado.",
        position: "left",
      },
      {
        element: "#tareasContainer",
        intro: "Aquí se muestran todas las tareas del proyecto seleccionado.",
        position: "top",
      },
      {
        element: "#tareasContainer table thead th:nth-child(1)",
        intro: "Estado de la tarea: Pendiente, Completada o Eliminada.",
        position: "bottom",
      },
      {
        element: "#tareasContainer table thead th:nth-child(2)",
        intro: "Título de la tarea.",
        position: "bottom",
      },
      {
        element: "#tareasContainer table thead th:nth-child(3)",
        intro: "Descripción detallada de la tarea.",
        position: "bottom",
      },
      {
        element: "#tareasContainer table thead th:nth-child(4)",
        intro: "Fecha en que se completó la tarea (si aplica).",
        position: "bottom",
      },
      {
        element: "#tareasContainer table thead th:nth-child(5)",
        intro: "Acciones para gestionar cada tarea.",
        position: "left",
      },
      {
        element: "#tareasContainer table tbody tr:first-child td:last-child",
        intro:
          "Usa este menú para editar, completar, reabrir o eliminar tareas.",
        position: "left",
      },
      {
        intro:
          "Colores: Verde = Completada, Rojo = Eliminada, Normal = Pendiente.",
      },
      {
        intro:
          "¡Listo! Mantén el control del progreso de tus proyectos a través de las tareas.",
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
