import { CrudManager } from "../helpers/crud.js";
import { formatearFecha } from "../helpers/utilidades.js";
import { mostrarError } from "../helpers/modal.js";

const APP_URL = window.location.pathname.includes("sistema_aplicaciones")
  ? "/sistema_aplicaciones"
  : "";

const usuarioManager = new CrudManager({
  entidad: "Usuario",
  entidadPlural: "usuarios",
  apiBase: `${APP_URL}/api/usuarios`,
  idCampo: "id_usuarios",
  tablaId: "tablaUsuarios",
  modalId: "modalUsuario",
  formId: "formUsuario",
  btnNuevo: "btnNuevoUsuario",
  columnas: [
    (item) => `<td>${item.usuarios_nombre}</td>`,
    (item) => `<td>${item.persona_nombres} ${item.persona_apellidos}</td>`,
    (item) => `<td>${item.rol_nombre || ""}</td>`,
    (item) =>
      `<td>${
        item.ultimo_acceso ? formatearFecha(item.ultimo_acceso) : "Nunca"
      }</td>`,
  ],
  camposVer: (item) => `
        <div class="text-left">
            <p><strong>ID:</strong> ${item.id_usuarios}</p>
            <p><strong>Nombre de Usuario:</strong> ${item.usuarios_nombre}</p>
            <p><strong>Persona:</strong> ${item.persona_nombres} ${
    item.persona_apellidos
  }</p>
            <p><strong>Rol:</strong> ${item.rol_nombre || ""}</p>
            <p><strong>Último Acceso:</strong> ${
              item.ultimo_acceso ? formatearFecha(item.ultimo_acceso) : "Nunca"
            }</p>
        </div>
    `,
  onModalShow: async () => {
    const idInput = document.getElementById("id_usuarios");
    const passwordRequired = document.getElementById("passwordRequired");

    if (idInput.value) {
      passwordRequired.style.display = "none";
      document.getElementById("usuarios_password").required = false;
    } else {
      passwordRequired.style.display = "inline";
      document.getElementById("usuarios_password").required = true;
    }

    const selectPersona = document.getElementById("persona_id_persona");

    if ($.fn.select2 && $(selectPersona).data("select2")) {
      $(selectPersona).select2("destroy");
    }

    selectPersona.innerHTML =
      '<option value="">Seleccione una persona</option>';

    try {
      const response = await fetch(`${APP_URL}/api/personal?situacion=1`);
      const resultado = await response.json();

      if (resultado.exito && resultado.data) {
        resultado.data.forEach((item) => {
          const option = document.createElement("option");
          option.value = item.id_persona;
          option.textContent = `${item.persona_nombres} ${item.persona_apellidos} (${item.persona_identidad})`;
          selectPersona.appendChild(option);
        });
      }
    } catch (error) {
      console.error("Error al cargar personas:", error);
    }

    $(selectPersona).select2({
      theme: "bootstrap4",
      width: "100%",
      placeholder: "Seleccione una persona",
    });

    const urlParams = new URLSearchParams(window.location.search);
    const personaId = urlParams.get("persona_id");

    if (personaId && !idInput.value) {
      $(selectPersona).val(personaId).trigger("change");
    }
  },
  accionesPersonalizadas: (item) => `
    <a class="dropdown-item" href="${APP_URL}/usuarios/permisos?id=${item.id_usuarios}">
        <i class="bi bi-shield-check text-success"></i> Asignar Permisos
    </a>
  `,
});

usuarioManager.generarAccionesActivos = function (id, item) {
  if (id == 1) {
    return `
      <div class="btn-group">
        <button type="button" class="btn btn-secondary btn-sm" disabled title="Usuario administrador principal protegido">
          <i class="fas fa-lock"></i>
        </button>
      </div>
    `;
  }

  const accionesPersonalizadas = usuarioManager.config.accionesPersonalizadas
    ? usuarioManager.config.accionesPersonalizadas(item)
    : "";

  return `
    <div class="btn-group">
      <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown">
        <i class="fas fa-bars"></i>
      </button>
      <div class="dropdown-menu dropdown-menu-right">
        <a class="dropdown-item" href="#" data-accion="ver" data-id="${id}">
          <i class="bi bi-eye text-primary"></i> Ver
        </a>
        <a class="dropdown-item" href="#" data-accion="editar" data-id="${id}">
          <i class="bi bi-pencil text-info"></i> Editar
        </a>
        <a class="dropdown-item" href="#" data-accion="eliminar" data-id="${id}">
          <i class="bi bi-trash text-danger"></i> Eliminar
        </a>
        ${accionesPersonalizadas}
      </div>
    </div>
  `;
};

const editarOriginal = usuarioManager.editar.bind(usuarioManager);
usuarioManager.editar = function (id) {
  if (id == 1) {
    mostrarError(
      "No se puede editar el usuario administrador principal del sistema"
    );
    return;
  }
  editarOriginal(id);
};

const eliminarOriginal = usuarioManager.eliminar.bind(usuarioManager);
usuarioManager.eliminar = function (id) {
  if (id == 1) {
    mostrarError(
      "No se puede eliminar el usuario administrador principal del sistema"
    );
    return;
  }
  eliminarOriginal(id);
};

document.addEventListener("DOMContentLoaded", () => {
  const urlParams = new URLSearchParams(window.location.search);
  const personaId = urlParams.get("persona_id");

  if (personaId) {
    setTimeout(() => {
      const btnNuevo = document.getElementById("btnNuevoUsuario");
      if (btnNuevo) {
        btnNuevo.click();
      }
    }, 500);
  }
});

// Vista Guiada - Usuarios
document
  .getElementById("btnVistaGuiada")
  ?.addEventListener("click", function (e) {
    e.preventDefault();

    const steps = [
      {
        intro:
          "¡Bienvenido al módulo de Usuarios! Aquí gestionas las cuentas de usuario del sistema.",
      },
      {
        element: "#btnNuevoUsuario",
        intro: "Haz clic aquí para crear un nuevo usuario.",
        position: "left",
      },
      {
        element: "#tablaUsuarios",
        intro: "Lista de todos los usuarios registrados en el sistema.",
        position: "top",
      },
      {
        element: "#tablaUsuarios thead th:nth-child(1)",
        intro: "Nombre de usuario para iniciar sesión.",
        position: "bottom",
      },
      {
        element: "#tablaUsuarios thead th:nth-child(2)",
        intro: "Persona asociada a este usuario.",
        position: "bottom",
      },
      {
        element: "#tablaUsuarios thead th:nth-child(3)",
        intro: "Rol o cargo del usuario en el sistema.",
        position: "bottom",
      },
      {
        element: "#tablaUsuarios thead th:nth-child(4)",
        intro: "Fecha del último acceso al sistema.",
        position: "bottom",
      },
      {
        element: "#tablaUsuarios thead th:nth-child(5)",
        intro: "Acciones para gestionar cada usuario.",
        position: "left",
      },
      {
        element: "#tablaUsuarios tbody tr:first-child td:last-child",
        intro: "Usa este menú para ver, editar, eliminar o asignar permisos.",
        position: "left",
      },
      {
        intro:
          "El usuario administrador principal está protegido y no se puede editar ni eliminar.",
      },
      {
        intro:
          "Para crear un usuario, primero debe existir la persona en el módulo 'Personal'.",
      },
      {
        intro:
          "¡Listo! Gestiona quién puede acceder al sistema y qué permisos tiene cada usuario.",
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
