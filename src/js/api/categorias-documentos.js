import { CrudManager } from "../helpers/crud.js";
import { mostrarError } from "../helpers/modal.js";

const APP_URL = window.location.pathname.includes("sistema_aplicaciones")
  ? "/sistema_aplicaciones"
  : "";

const categoriaManager = new CrudManager({
  entidad: "Categoría",
  entidadPlural: "categorías",
  apiBase: `${APP_URL}/api/categorias`,
  idCampo: "id_categorias_documentos",
  tablaId: "tablaCategorias",
  modalId: "modalCategoria",
  formId: "formCategoria",
  btnNuevo: "btnNuevaCategoria",
  columnas: [
    (item) => `<td>${item.categorias_documentos_nombre}</td>`,
    (item) =>
      `<td>${item.categorias_documentos_descripcion || "Sin descripción"}</td>`,
  ],
  camposVer: (item) => `
        <div class="text-left">
            <p><strong>ID:</strong> ${item.id_categorias_documentos}</p>
            <p><strong>Nombre:</strong> ${item.categorias_documentos_nombre}</p>
            <p><strong>Descripción:</strong> ${
              item.categorias_documentos_descripcion || "Sin descripción"
            }</p>
        </div>
    `,
});

const categoriasBase = [
  "Documentación Técnica",
  "Código Fuente",
  "Manuales",
  "Pruebas",
];

categoriaManager.generarAccionesActivos = function (id, item) {
  if (categoriasBase.includes(item.categorias_documentos_nombre)) {
    return `
            <div class="btn-group">
                <button type="button" class="btn btn-secondary btn-sm" disabled title="Categoría del sistema protegida">
                    <i class="fas fa-lock"></i>
                </button>
            </div>
        `;
  }

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
            </div>
        </div>
    `;
};

const editarOriginal = categoriaManager.editar.bind(categoriaManager);
categoriaManager.editar = function (id) {
  fetch(`${APP_URL}/api/categorias?situacion=1`)
    .then((response) => response.json())
    .then((resultado) => {
      if (resultado.exito) {
        const categoria = resultado.data.find(
          (c) => c.id_categorias_documentos == id
        );
        if (
          categoria &&
          categoriasBase.includes(categoria.categorias_documentos_nombre)
        ) {
          mostrarError("No se pueden editar las categorías base del sistema");
          return;
        }
        editarOriginal(id);
      }
    });
};

const eliminarOriginal = categoriaManager.eliminar.bind(categoriaManager);
categoriaManager.eliminar = function (id) {
  fetch(`${APP_URL}/api/categorias?situacion=1`)
    .then((response) => response.json())
    .then((resultado) => {
      if (resultado.exito) {
        const categoria = resultado.data.find(
          (c) => c.id_categorias_documentos == id
        );
        if (
          categoria &&
          categoriasBase.includes(categoria.categorias_documentos_nombre)
        ) {
          mostrarError("No se pueden eliminar las categorías base del sistema");
          return;
        }
        eliminarOriginal(id);
      }
    });
};

// Vista Guiada - Categorías de Documentos
document
  .getElementById("btnVistaGuiada")
  ?.addEventListener("click", function (e) {
    e.preventDefault();

    const steps = [
      {
        intro:
          "¡Bienvenido al módulo de Categorías de Documentos! Aquí organizas las categorías para clasificar tus documentos.",
      },
      {
        element: "#btnNuevaCategoria",
        intro: "Haz clic aquí para crear una nueva categoría personalizada.",
        position: "left",
      },
      {
        element: "#tablaCategorias",
        intro: "Lista de todas las categorías disponibles en el sistema.",
        position: "top",
      },
      {
        element: "#tablaCategorias thead th:nth-child(1)",
        intro: "Nombre de la categoría.",
        position: "bottom",
      },
      {
        element: "#tablaCategorias thead th:nth-child(2)",
        intro: "Descripción de la categoría.",
        position: "bottom",
      },
      {
        element: "#tablaCategorias thead th:nth-child(3)",
        intro: "Acciones para gestionar cada categoría.",
        position: "bottom",
      },
      {
        element: "#tablaCategorias tbody tr:first-child td:last-child",
        intro: "Usa este menú para ver, editar o eliminar categorías.",
        position: "left",
      },
      {
        intro:
          "Categorías base del sistema: Documentación Técnica, Código Fuente, Manuales, Pruebas.",
      },
      {
        intro:
          "Las categorías base están protegidas y no se pueden editar ni eliminar (tienen un candado).",
      },
      {
        intro:
          "¡Listo! Organiza tus documentos en categorías para encontrarlos fácilmente.",
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
