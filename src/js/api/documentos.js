import { CrudManager } from "../helpers/crud.js";
import { cargarSelect } from "../helpers/selects.js";
import { mostrarError } from "../helpers/modal.js";

const APP_URL = window.location.pathname.includes("sistema_aplicaciones")
  ? "/sistema_aplicaciones"
  : "";

const configuracion = {
  apiBase: `${APP_URL}/api/documentos`,
  entidad: "Documento",
  entidadPlural: "Documentos",
  idCampo: "id_documentos",
  tablaId: "tablaDocumentos",
  modalId: "modalDocumento",
  formId: "formDocumento",
  btnNuevo: "btnNuevoDocumento",
  columnas: [
    (item) => `<td>${item.documentos_nombre || ""}</td>`,
    (item) => `<td>${item.categoria_nombre || "Sin categoría"}</td>`,
    (item) => `<td>${item.proyecto_nombre || "Sin proyecto"}</td>`,
    (item) => {
      const tipo = item.documentos_ruta
        ? "Archivo"
        : item.documentos_url
        ? "URL"
        : "N/A";
      return `<td>${tipo}</td>`;
    },
    (item) => `<td>${item.documentos_fecha_subida || ""}</td>`,
  ],
  accionesPersonalizadas: (item) => {
    if (item.documentos_ruta) {
      return `<a class="dropdown-item" href="${APP_URL}${item.documentos_ruta}" target="_blank" onclick="event.stopPropagation()">
          <i class="fas fa-download text-success"></i> Descargar
      </a>`;
    }
    if (item.documentos_url) {
      return `<a class="dropdown-item" href="${item.documentos_url}" target="_blank">
        <i class="fas fa-external-link-alt text-info"></i> Abrir URL
      </a>`;
    }
    return "";
  },
  camposVer: (item) => {
    const tipo = item.documentos_ruta
      ? "Archivo"
      : item.documentos_url
      ? "URL"
      : "N/A";
    const enlace = item.documentos_ruta
      ? `<a href="${APP_URL}${item.documentos_ruta}" target="_blank">Descargar archivo</a>`
      : item.documentos_url
      ? `<a href="${item.documentos_url}" target="_blank">Abrir URL</a>`
      : "Sin enlace";

    return `
      <p><strong>Título:</strong> ${item.documentos_nombre || ""}</p>
      <p><strong>Categoría:</strong> ${
        item.categoria_nombre || "Sin categoría"
      }</p>
      <p><strong>Proyecto:</strong> ${
        item.proyecto_nombre || "Sin proyecto"
      }</p>
      <p><strong>Tipo:</strong> ${tipo}</p>
      <p><strong>Enlace:</strong> ${enlace}</p>
      <p><strong>Tamaño:</strong> ${formatearTamano(
        item.documentos_tamanio
      )}</p>
      <p><strong>Extensión:</strong> ${item.documentos_extension || "N/A"}</p>
      <p><strong>Fecha:</strong> ${item.documentos_fecha_subida || ""}</p>
    `;
  },
  onModalShow: async () => {
    await cargarSelect(
      `${APP_URL}/api/categorias?situacion=1`,
      "categorias_documentos_id_categorias_documentos",
      "id_categorias_documentos",
      "categorias_documentos_nombre"
    );

    await cargarSelect(
      `${APP_URL}/api/proyectos-asignados?situacion=1`,
      "ordenes_aplicaciones_id_ordenes_aplicaciones",
      "id_ordenes_aplicaciones",
      (item) =>
        `${item.ordenes_aplicaciones_codigo} - ${item.aplicacion_nombre}`
    );
  },
  beforeSave: async (datos) => {
    const archivoInput = document.getElementById("documentos_archivo");
    const archivo = archivoInput?.files?.[0];
    const url = datos.documentos_url;

    console.log("Archivo seleccionado:", archivo);
    console.log("URL proporcionada:", url);

    if (!archivo && !url) {
      throw new Error("Debe proporcionar un archivo o una URL");
    }

    if (archivo) {
      console.log("Subiendo archivo:", archivo.name);

      const formData = new FormData();
      formData.append("archivo", archivo);

      try {
        const response = await fetch(`${APP_URL}/api/upload`, {
          method: "POST",
          body: formData,
        });

        console.log("Response status:", response.status);

        const resultado = await response.json();
        console.log("Resultado upload:", resultado);

        if (resultado.exito) {
          datos.documentos_ruta = resultado.ruta;
          datos.documentos_extension = resultado.extension;
          datos.documentos_tamanio = resultado.tamano;
        } else {
          throw new Error(resultado.mensaje || "Error al subir el archivo");
        }
      } catch (error) {
        console.error("Error en upload:", error);
        throw error;
      }
    }

    const authResponse = await fetch(`${APP_URL}/api/auth/check`);
    const authData = await authResponse.json();

    if (authData.autenticado) {
      datos.usuarios_id_usuarios = authData.usuario.id;
    } else {
      throw new Error("Debe estar autenticado");
    }

    delete datos.documentos_archivo;

    return datos;
  },
};

const crudManager = new CrudManager(configuracion);

document
  .getElementById("filtroCategoria")
  ?.addEventListener("change", async (e) => {
    const categoriaId = e.target.value;
    if (categoriaId) {
      const response = await fetch(
        `${configuracion.apiBase}?situacion=1&categoria_id=${categoriaId}`
      );
      const resultado = await response.json();
      if (resultado.exito) {
        crudManager.renderizarTabla(resultado.data);
      }
    } else {
      crudManager.cargarDatos();
    }
  });

cargarSelect(
  `${APP_URL}/api/categorias?situacion=1`,
  "filtroCategoria",
  "id_categorias_documentos",
  "categorias_documentos_nombre"
);

function formatearTamano(bytes) {
  if (!bytes) return "N/A";
  if (bytes < 1024) return bytes + " B";
  if (bytes < 1048576) return (bytes / 1024).toFixed(2) + " KB";
  return (bytes / 1048576).toFixed(2) + " MB";
}

// Vista Guiada - Documentos
document
  .getElementById("btnVistaGuiada")
  ?.addEventListener("click", function (e) {
    e.preventDefault();

    const steps = [
      {
        intro:
          "¡Bienvenido al módulo de Documentos! Aquí gestionas todos los documentos y archivos del sistema.",
      },
      {
        element: "#btnNuevoDocumento",
        intro:
          "Haz clic aquí para subir un nuevo documento o agregar un enlace.",
        position: "left",
      },
      {
        element: "#filtroCategoria",
        intro:
          "Filtra los documentos por categoría para encontrar lo que necesitas más rápido.",
        position: "bottom",
      },
      {
        element: "#tablaDocumentos",
        intro: "Lista de todos los documentos registrados en el sistema.",
        position: "top",
      },
      {
        element: "#tablaDocumentos thead th:nth-child(1)",
        intro: "Nombre del documento o título.",
        position: "bottom",
      },
      {
        element: "#tablaDocumentos thead th:nth-child(2)",
        intro: "Categoría a la que pertenece el documento.",
        position: "bottom",
      },
      {
        element: "#tablaDocumentos thead th:nth-child(3)",
        intro: "Proyecto asociado al documento.",
        position: "bottom",
      },
      {
        element: "#tablaDocumentos thead th:nth-child(4)",
        intro: "Tipo: Archivo (subido) o URL (enlace externo).",
        position: "bottom",
      },
      {
        element: "#tablaDocumentos thead th:nth-child(5)",
        intro: "Fecha de subida o registro.",
        position: "bottom",
      },
      {
        element: "#tablaDocumentos thead th:nth-child(6)",
        intro: "Acciones para gestionar cada documento.",
        position: "bottom",
      },
      {
        element: "#tablaDocumentos tbody tr:first-child td:last-child",
        intro:
          "Usa este menú para ver detalles, descargar archivos o abrir enlaces.",
        position: "left",
      },
      {
        intro:
          "Puedes subir archivos (PDF) o agregar enlaces a recursos externos.",
      },
      {
        intro:
          "¡Listo! Mantén toda la documentación de tus proyectos organizada y accesible.",
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
