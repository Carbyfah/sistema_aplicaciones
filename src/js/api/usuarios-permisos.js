import Swal from "sweetalert2";

const APP_URL = window.location.pathname.includes("sistema_aplicaciones")
  ? "/sistema_aplicaciones"
  : "";

document.addEventListener("DOMContentLoaded", function () {
  const selectUsuario = document.getElementById("selectUsuario");
  const cardPermisos = document.getElementById("cardPermisos");
  const tablaPermisosBody = document.getElementById("tablaPermisosBody");
  const btnGuardarPermisos = document.getElementById("btnGuardarPermisos");
  const btnMarcarTodos = document.getElementById("btnMarcarTodos");
  const btnDesmarcarTodos = document.getElementById("btnDesmarcarTodos");

  cargarUsuarios();

  selectUsuario.addEventListener("change", function () {
    const usuarioId = this.value;
    if (usuarioId) {
      cargarModulos(usuarioId);
      cardPermisos.style.display = "block";
    } else {
      cardPermisos.style.display = "none";
    }
  });

  btnGuardarPermisos.addEventListener("click", guardarPermisos);
  btnMarcarTodos.addEventListener("click", marcarTodos);
  btnDesmarcarTodos.addEventListener("click", desmarcarTodos);

  function cargarUsuarios() {
    fetch(`${APP_URL}/api/usuarios?situacion=1`)
      .then((response) => response.json())
      .then((data) => {
        if (data.exito) {
          let options = '<option value="">-- Seleccione un usuario --</option>';
          data.data.forEach((usuario) => {
            options += `<option value="${usuario.id_usuarios}">${usuario.usuarios_nombre} (${usuario.persona_nombres} ${usuario.persona_apellidos})</option>`;
          });
          selectUsuario.innerHTML = options;
        } else {
          mostrarMensaje("error", "Error al cargar usuarios");
        }
      })
      .catch((error) => {
        console.error("Error al cargar usuarios:", error);
        mostrarMensaje("error", "Error al cargar usuarios");
      });
  }

  function cargarModulos(usuarioId) {
    Promise.all([
      fetch(`${APP_URL}/api/modulos`).then((r) => r.json()),
      fetch(
        `${APP_URL}/api/usuarios-permisos/modulos?usuario_id=${usuarioId}`
      ).then((r) => r.json()),
    ])
      .then(([modulosResponse, permisosResponse]) => {
        if (modulosResponse.exito && permisosResponse.exito) {
          const modulos = modulosResponse.data;
          const modulosConPermisos = permisosResponse.data;

          renderizarTablaPermisos(modulos, modulosConPermisos);
        } else {
          mostrarMensaje(
            "error",
            "Error al cargar información de módulos y permisos"
          );
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        tablaPermisosBody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center text-danger">
                        <i class="fas fa-exclamation-triangle mr-2"></i>Error al cargar información
                    </td>
                </tr>
            `;
      });
  }

  function renderizarTablaPermisos(modulos, modulosConPermisos) {
    const permisosMap = {};
    modulosConPermisos.forEach((m) => {
      permisosMap[m.id_modulos] = {
        puede_ver: m.permisos?.puede_ver || 0,
        puede_crear: m.permisos?.puede_crear || 0,
        puede_editar: m.permisos?.puede_editar || 0,
        puede_eliminar: m.permisos?.puede_eliminar || 0,
        puede_exportar_excel: m.permisos?.puede_exportar_excel || 0,
        puede_exportar_pdf: m.permisos?.puede_exportar_pdf || 0,
      };
    });

    const modulosPorPadre = {};
    modulos.forEach((m) => {
      const padreId = m.modulo_padre_id || "root";
      if (!modulosPorPadre[padreId]) {
        modulosPorPadre[padreId] = [];
      }
      modulosPorPadre[padreId].push(m);
    });

    const modulosPrincipales = (modulosPorPadre["root"] || []).sort((a, b) =>
      a.modulos_nombre.localeCompare(b.modulos_nombre)
    );

    tablaPermisosBody.innerHTML = "";

    modulosPrincipales.forEach((modulo) => {
      tablaPermisosBody.appendChild(
        crearFilaModulo(modulo, permisosMap, false)
      );

      const submods = modulosPorPadre[modulo.id_modulos] || [];
      if (submods.length > 0) {
        submods
          .sort((a, b) => a.modulos_nombre.localeCompare(b.modulos_nombre))
          .forEach((submodulo) => {
            tablaPermisosBody.appendChild(
              crearFilaModulo(submodulo, permisosMap, true)
            );
          });
      }
    });
  }

  function crearFilaModulo(modulo, permisosMap, esSubmenu) {
    const permisos = permisosMap[modulo.id_modulos] || {
      puede_ver: 0,
      puede_crear: 0,
      puede_editar: 0,
      puede_eliminar: 0,
      puede_exportar_excel: 0,
      puede_exportar_pdf: 0,
    };

    const tr = document.createElement("tr");

    // Checkbox maestro del módulo
    const tdMaestro = document.createElement("td");
    tdMaestro.className = "text-center";

    const checkboxMaestro = document.createElement("input");
    checkboxMaestro.type = "checkbox";
    checkboxMaestro.className = "checkbox-modulo-maestro";
    checkboxMaestro.dataset.moduloId = modulo.id_modulos;

    checkboxMaestro.addEventListener("change", function () {
      const filaModulo = this.closest("tr");
      const checkboxes = filaModulo.querySelectorAll(".checkbox-permiso");
      checkboxes.forEach((cb) => (cb.checked = this.checked));
    });

    tdMaestro.appendChild(checkboxMaestro);
    tr.appendChild(tdMaestro);

    // Columna de nombre del módulo
    const tdNombre = document.createElement("td");
    if (esSubmenu) {
      tdNombre.style.paddingLeft = "30px";
      tdNombre.innerHTML = `<i class="fas fa-angle-right mr-2"></i>${modulo.modulos_nombre}`;
    } else {
      tdNombre.innerHTML = `<strong>${modulo.modulos_nombre}</strong>`;
    }
    tr.appendChild(tdNombre);

    // Crear checkboxes para cada tipo de permiso
    tr.appendChild(
      crearCheckbox(
        "puede_ver",
        modulo.id_modulos,
        permisos.puede_ver,
        checkboxMaestro
      )
    );
    tr.appendChild(
      crearCheckbox(
        "puede_crear",
        modulo.id_modulos,
        permisos.puede_crear,
        checkboxMaestro
      )
    );
    tr.appendChild(
      crearCheckbox(
        "puede_editar",
        modulo.id_modulos,
        permisos.puede_editar,
        checkboxMaestro
      )
    );
    tr.appendChild(
      crearCheckbox(
        "puede_eliminar",
        modulo.id_modulos,
        permisos.puede_eliminar,
        checkboxMaestro
      )
    );
    tr.appendChild(
      crearCheckbox(
        "puede_exportar_excel",
        modulo.id_modulos,
        permisos.puede_exportar_excel,
        checkboxMaestro
      )
    );
    tr.appendChild(
      crearCheckbox(
        "puede_exportar_pdf",
        modulo.id_modulos,
        permisos.puede_exportar_pdf,
        checkboxMaestro
      )
    );

    tr.classList.add("fila-modulo");
    tr.dataset.moduloId = modulo.id_modulos;

    // Actualizar estado del checkbox maestro
    setTimeout(() => actualizarCheckboxMaestro(checkboxMaestro, tr), 0);

    return tr;
  }

  function crearCheckbox(permiso, moduloId, checked, checkboxMaestro) {
    const td = document.createElement("td");
    td.className = "text-center";

    const label = document.createElement("label");
    label.className = "custom-control custom-checkbox m-0";

    const input = document.createElement("input");
    input.type = "checkbox";
    input.className = "custom-control-input checkbox-permiso";
    input.checked = checked === 1;
    input.dataset.permiso = permiso;
    input.dataset.moduloId = moduloId;

    // Actualizar checkbox maestro cuando cambia un permiso individual
    input.addEventListener("change", function () {
      const fila = this.closest("tr");
      const maestro = fila.querySelector(".checkbox-modulo-maestro");
      actualizarCheckboxMaestro(maestro, fila);
    });

    const span = document.createElement("span");
    span.className = "custom-control-label";

    label.appendChild(input);
    label.appendChild(span);
    td.appendChild(label);

    return td;
  }

  function actualizarCheckboxMaestro(checkboxMaestro, fila) {
    const checkboxes = fila.querySelectorAll(".checkbox-permiso");
    const todosChecked = Array.from(checkboxes).every((cb) => cb.checked);
    const algunoChecked = Array.from(checkboxes).some((cb) => cb.checked);

    checkboxMaestro.checked = todosChecked;
    checkboxMaestro.indeterminate = algunoChecked && !todosChecked;
  }

  function marcarTodos() {
    document.querySelectorAll(".checkbox-permiso").forEach((checkbox) => {
      checkbox.checked = true;
    });
    document
      .querySelectorAll(".checkbox-modulo-maestro")
      .forEach((checkbox) => {
        checkbox.checked = true;
        checkbox.indeterminate = false;
      });
  }

  function desmarcarTodos() {
    document.querySelectorAll(".checkbox-permiso").forEach((checkbox) => {
      checkbox.checked = false;
    });
    document
      .querySelectorAll(".checkbox-modulo-maestro")
      .forEach((checkbox) => {
        checkbox.checked = false;
        checkbox.indeterminate = false;
      });
  }

  function guardarPermisos() {
    const usuarioId = selectUsuario.value;
    if (!usuarioId) {
      mostrarMensaje("error", "Debe seleccionar un usuario");
      return;
    }

    const permisos = [];
    const filas = document.querySelectorAll(".fila-modulo");

    filas.forEach((fila) => {
      const moduloId = fila.dataset.moduloId;
      const permisosModulo = {
        modulo_id: parseInt(moduloId),
        puede_ver: 0,
        puede_crear: 0,
        puede_editar: 0,
        puede_eliminar: 0,
        puede_exportar_excel: 0,
        puede_exportar_pdf: 0,
      };

      const checkboxes = fila.querySelectorAll(".checkbox-permiso");
      checkboxes.forEach((cb) => {
        if (cb.checked) {
          permisosModulo[cb.dataset.permiso] = 1;
        }
      });

      permisos.push(permisosModulo);
    });

    fetch(`${APP_URL}/api/usuarios-permisos/guardar-todos`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        usuario_id: parseInt(usuarioId),
        permisos: permisos,
      }),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.exito) {
          mostrarMensaje("success", "Permisos guardados correctamente");
        } else {
          mostrarMensaje(
            "error",
            data.mensaje || "Error al guardar los permisos"
          );
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        mostrarMensaje("error", "Error al guardar los permisos");
      });
  }

  function mostrarMensaje(tipo, mensaje) {
    Swal.fire({
      icon: tipo,
      title: tipo === "success" ? "Éxito" : "Error",
      text: mensaje,
      timer: tipo === "success" ? 2000 : undefined,
      showConfirmButton: tipo !== "success",
    });
  }
});

// Vista Guiada - Usuarios Permisos
document
  .getElementById("btnVistaGuiada")
  ?.addEventListener("click", function (e) {
    e.preventDefault();

    const steps = [
      {
        intro:
          "¡Bienvenido al módulo de Permisos de Usuarios! Aquí asignas permisos granulares a cada usuario.",
      },
      {
        element: "#selectUsuario",
        intro:
          "Selecciona el usuario al que quieres asignar o modificar permisos.",
        position: "bottom",
      },
      {
        element: "#cardPermisos",
        intro:
          "Una vez seleccionado el usuario, aquí aparecerán todos los módulos del sistema.",
        position: "top",
      },
      {
        element: "#btnMarcarTodos",
        intro: "Haz clic aquí para dar todos los permisos a todos los módulos.",
        position: "left",
      },
      {
        element: "#btnDesmarcarTodos",
        intro:
          "Haz clic aquí para quitar todos los permisos de todos los módulos.",
        position: "left",
      },
      {
        element: "#tablaPermisos thead th:nth-child(1)",
        intro:
          "Checkbox maestro para marcar/desmarcar todos los permisos de un módulo.",
        position: "bottom",
      },
      {
        element: "#tablaPermisos thead th:nth-child(2)",
        intro: "Nombre del módulo del sistema.",
        position: "bottom",
      },
      {
        element: "#tablaPermisos thead th:nth-child(3)",
        intro: "Permiso para VER el módulo.",
        position: "bottom",
      },
      {
        element: "#tablaPermisos thead th:nth-child(4)",
        intro: "Permiso para CREAR registros.",
        position: "bottom",
      },
      {
        element: "#tablaPermisos thead th:nth-child(5)",
        intro: "Permiso para EDITAR registros.",
        position: "bottom",
      },
      {
        element: "#tablaPermisos thead th:nth-child(6)",
        intro: "Permiso para ELIMINAR registros.",
        position: "bottom",
      },
      {
        element: "#tablaPermisos thead th:nth-child(7)",
        intro: "Permiso para EXPORTAR a Excel.",
        position: "bottom",
      },
      {
        element: "#tablaPermisos thead th:nth-child(8)",
        intro: "Permiso para EXPORTAR a PDF.",
        position: "bottom",
      },
      {
        element: "#btnGuardarPermisos",
        intro: "Haz clic aquí para guardar todos los cambios de permisos.",
        position: "left",
      },
      {
        intro:
          "Los módulos con sangría son sub-módulos que dependen de un módulo padre.",
      },
      {
        intro:
          "¡Listo! Controla exactamente qué puede hacer cada usuario en el sistema.",
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
