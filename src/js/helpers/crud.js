import Swal from "sweetalert2";
import { initDataTable } from "./tablas.js";
import {
  abrirModal,
  confirmarEliminacion,
  confirmarRecuperacion,
  mostrarExito,
  mostrarError,
} from "./modal.js";

export class CrudManager {
  constructor(config) {
    this.config = config;
    this.datatable = null;
    this.mostrandoEliminados = false;
    this.init();
  }

  init() {
    this.cargarDatos();
    this.eventListeners();
  }

  eventListeners() {
    document
      .getElementById(this.config.btnNuevo)
      ?.addEventListener("click", () => {
        this.nuevo();
      });

    document
      .getElementById(this.config.formId)
      ?.addEventListener("submit", (e) => {
        e.preventDefault();
        this.guardar();
      });

    document
      .getElementById("toggleEliminados")
      ?.addEventListener("change", (e) => {
        this.mostrandoEliminados = e.target.checked;
        this.cargarDatos();
      });
  }

  async cargarDatos() {
    try {
      const situacion = this.mostrandoEliminados ? 0 : 1;
      const response = await fetch(
        `${this.config.apiBase}?situacion=${situacion}`
      );
      const resultado = await response.json();

      if (resultado.exito) {
        this.renderizarTabla(resultado.data || []);
      } else {
        this.renderizarTabla([]);
      }
    } catch (error) {
      console.error(`Error al cargar ${this.config.entidad}:`, error);
      this.renderizarTabla([]);
      Swal.fire({
        position: "top-end",
        icon: "error",
        title: `No se pudieron cargar ${this.config.entidadPlural}`,
        showConfirmButton: false,
        timer: 2000,
        toast: true,
        timerProgressBar: true,
      });
    }
  }

  renderizarTabla(datos) {
    const tbody = document.querySelector(`#${this.config.tablaId} tbody`);

    if (!tbody) return;

    if (this.datatable) {
      this.datatable.destroy();
      this.datatable = null;
    }

    tbody.innerHTML = "";

    if (datos.length === 0) {
      const colspan =
        this.config.mostrarAcciones === false
          ? this.config.columnas.length
          : this.config.columnas.length + 1;

      const mensaje = this.mostrandoEliminados
        ? `No hay ${this.config.entidadPlural} eliminados`
        : `No hay ${this.config.entidadPlural} registrados`;

      tbody.innerHTML = `
        <tr>
          <td colspan="${colspan}" class="text-center text-muted py-5">
            <i class="fas fa-inbox fa-3x mb-3 d-block text-secondary"></i>
            ${mensaje}
          </td>
        </tr>
      `;
      return;
    }

    datos.forEach((item) => {
      const row = document.createElement("tr");
      const columnas = this.config.columnas.map((col) => col(item)).join("");

      if (this.config.mostrarAcciones === false) {
        row.innerHTML = columnas;
      } else {
        const acciones = this.mostrandoEliminados
          ? this.generarAccionesEliminados(item[this.config.idCampo], item)
          : this.generarAccionesActivos(item[this.config.idCampo], item);
        row.innerHTML = `${columnas}<td class="text-center">${acciones}</td>`;
      }

      tbody.appendChild(row);
    });

    if (this.config.mostrarAcciones !== false) {
      tbody.removeEventListener("click", this.handleTableClick);
      this.handleTableClick = (e) => {
        const link = e.target.closest("a[data-accion]");

        // Solo prevenir comportamiento por defecto para links con data-accion
        if (link) {
          e.preventDefault();

          const accion = link.dataset.accion;
          const id = parseInt(link.dataset.id);

          if (accion === "editar") {
            this.editar(id);
          } else if (accion === "eliminar") {
            this.eliminar(id);
          } else if (accion === "ver") {
            this.ver(id);
          } else if (accion === "recuperar") {
            this.recuperar(id);
          } else if (accion === "descargar-comprobante") {
            this.descargarComprobante(id);
          }
        }
        // Los links sin data-accion (como el de permisos) se comportarán normalmente
      };
      tbody.addEventListener("click", this.handleTableClick);
    }

    this.datatable = initDataTable(`#${this.config.tablaId}`, {
      pageLength: 25,
      lengthMenu: [
        [10, 25, 50, 100, -1],
        [10, 25, 50, 100, "Todos"],
      ],
    });
  }

  generarAccionesActivos(id, item) {
    if (this.config.mostrarAcciones === false) {
      return `
      <button type="button" class="btn btn-info btn-sm" data-accion="ver" data-id="${id}">
        <i class="fas fa-eye"></i>
      </button>
    `;
    }

    let accionesPersonalizadas = "";
    if (this.config.accionesPersonalizadas) {
      accionesPersonalizadas = this.config.accionesPersonalizadas(item);
    }

    return `
    <div class="dropdown">
      <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fas fa-bars"></i>
      </button>
      <div class="dropdown-menu dropdown-menu-right">
        <a class="dropdown-item" href="#" data-accion="ver" data-id="${id}">
          <i class="fas fa-eye text-primary"></i> Ver
        </a>
        <a class="dropdown-item" href="#" data-accion="editar" data-id="${id}">
          <i class="fas fa-edit text-info"></i> Editar
        </a>
        <a class="dropdown-item" href="#" data-accion="eliminar" data-id="${id}">
          <i class="fas fa-trash text-danger"></i> Eliminar
        </a>
        ${accionesPersonalizadas}
      </div>
    </div>
  `;
  }

  generarAccionesEliminados(id, item) {
    let accionesPersonalizadas = "";
    if (this.config.accionesPersonalizadas) {
      accionesPersonalizadas = this.config.accionesPersonalizadas(item);
    }

    return `
    <div class="dropdown">
      <button type="button" class="btn btn-secondary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fas fa-bars"></i>
      </button>
      <div class="dropdown-menu dropdown-menu-right">
        <a class="dropdown-item" href="#" data-accion="ver" data-id="${id}">
          <i class="fas fa-eye text-primary"></i> Ver
        </a>
        <a class="dropdown-item" href="#" data-accion="recuperar" data-id="${id}">
          <i class="fas fa-undo text-success"></i> Recuperar
        </a>
        ${accionesPersonalizadas}
      </div>
    </div>
  `;
  }

  descargarComprobante(id) {
    window.open(
      `${this.config.apiBase}/comprobante-entrega?id=${id}`,
      "_blank"
    );
  }

  async nuevo() {
    const form = document.getElementById(this.config.formId);

    abrirModal(
      this.config.modalId,
      `Nuevo ${this.config.entidad}`,
      null,
      this.config.formId
    );

    if (form) {
      form.reset();

      const idInput = form.querySelector(`[name="${this.config.idCampo}"]`);
      if (idInput) {
        idInput.value = "";
      }

      const selects = form.querySelectorAll("select");
      selects.forEach((select) => {
        if (window.$(select).data("select2")) {
          window.$(select).val(null).trigger("change");
        }
      });
    }

    if (this.config.onModalShow) {
      setTimeout(async () => {
        await this.config.onModalShow();

        if (form) {
          const idInput = form.querySelector(`[name="${this.config.idCampo}"]`);
          if (idInput) {
            idInput.value = "";
          }
        }
      }, 300);
    }
  }

  async editar(id) {
    try {
      const response = await fetch(`${this.config.apiBase}?situacion=1`);
      const resultado = await response.json();

      if (resultado.exito) {
        const item = resultado.data.find((i) => i[this.config.idCampo] == id);
        if (item) {
          abrirModal(
            this.config.modalId,
            `Editar ${this.config.entidad}`,
            item,
            this.config.formId
          );

          if (this.config.onModalShow) {
            setTimeout(async () => {
              await this.config.onModalShow();

              Object.keys(item).forEach((key) => {
                const input = document.querySelector(`[name="${key}"]`);
                if (input) {
                  if (input.tagName === "SELECT") {
                    window.$(`#${input.id}`).val(item[key]).trigger("change");
                  } else {
                    input.value = item[key] || "";
                  }
                }
              });
            }, 300);
          }
        }
      }
    } catch (error) {
      console.error(`Error al cargar ${this.config.entidad}:`, error);
      Swal.fire({
        position: "top-end",
        icon: "error",
        title: `No se pudo cargar ${this.config.entidad}`,
        showConfirmButton: false,
        timer: 2000,
        toast: true,
        timerProgressBar: true,
      });
    }
  }

  async ver(id) {
    try {
      const situacion = this.mostrandoEliminados ? 0 : 1;
      const response = await fetch(
        `${this.config.apiBase}?situacion=${situacion}`
      );
      const resultado = await response.json();

      if (resultado.exito) {
        const item = resultado.data.find((i) => i[this.config.idCampo] == id);
        if (item) {
          const mensaje = this.config.camposVer(item);
          Swal.fire({
            title: `Detalle de ${this.config.entidad}`,
            html: mensaje,
            icon: "info",
            confirmButtonText: "Cerrar",
          });
        }
      }
    } catch (error) {
      console.error(`Error al cargar ${this.config.entidad}:`, error);
      Swal.fire({
        position: "top-end",
        icon: "error",
        title: `No se pudo cargar ${this.config.entidad}`,
        showConfirmButton: false,
        timer: 2000,
        toast: true,
        timerProgressBar: true,
      });
    }
  }

  async guardar() {
    const form = document.getElementById(this.config.formId);
    const formData = new FormData(form);

    const datos = {};
    for (let [key, value] of formData.entries()) {
      if (value !== "" || key === "usuario_password") {
        datos[key] = value;
      }
    }

    const idCampo = this.config.idCampo;
    if (
      datos[idCampo] === "" ||
      datos[idCampo] === null ||
      datos[idCampo] === undefined
    ) {
      delete datos[idCampo];
    }

    if (this.config.beforeSave) {
      try {
        const datosModificados = await this.config.beforeSave(datos);
        Object.assign(datos, datosModificados);
      } catch (error) {
        mostrarError(error.message);
        return;
      }
    }

    try {
      const response = await fetch(`${this.config.apiBase}`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(datos),
      });

      const resultado = await response.json();

      if (resultado.exito) {
        await mostrarExito(resultado.mensaje);
        window.$(`#${this.config.modalId}`).modal("hide");
        this.cargarDatos();
      } else {
        Swal.fire({
          position: "top-end",
          icon: "error",
          title: resultado.mensaje,
          showConfirmButton: false,
          timer: 2000,
          toast: true,
          timerProgressBar: true,
        });
      }
    } catch (error) {
      console.error("Error al guardar:", error);
      Swal.fire({
        position: "top-end",
        icon: "error",
        title: `No se pudo guardar ${this.config.entidad}`,
        showConfirmButton: false,
        timer: 2000,
        toast: true,
        timerProgressBar: true,
      });
    }
  }

  async eliminar(id) {
    const confirmado = await confirmarEliminacion();
    if (!confirmado) return;

    try {
      const response = await fetch(`${this.config.apiBase}/eliminar`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ id: id }),
      });

      const resultado = await response.json();

      if (resultado.exito) {
        await mostrarExito(resultado.mensaje);
        this.cargarDatos();
      } else {
        Swal.fire({
          position: "top-end",
          icon: "error",
          title: resultado.mensaje,
          showConfirmButton: false,
          timer: 2000,
          toast: true,
          timerProgressBar: true,
        });
      }
    } catch (error) {
      console.error("Error al eliminar:", error);
      Swal.fire({
        position: "top-end",
        icon: "error",
        title: `No se pudo eliminar ${this.config.entidad}`,
        showConfirmButton: false,
        timer: 2000,
        toast: true,
        timerProgressBar: true,
      });
    }
  }

  async recuperar(id) {
    const confirmado = await confirmarRecuperacion();
    if (!confirmado) return;

    try {
      const response = await fetch(`${this.config.apiBase}/recuperar`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ id: id }),
      });

      const resultado = await response.json();

      if (resultado.exito) {
        await mostrarExito(resultado.mensaje);
        this.cargarDatos();
      } else {
        Swal.fire({
          position: "top-end",
          icon: "error",
          title: resultado.mensaje,
          showConfirmButton: false,
          timer: 2000,
          toast: true,
          timerProgressBar: true,
        });
      }
    } catch (error) {
      console.error("Error al recuperar:", error);
      Swal.fire({
        position: "top-end",
        icon: "error",
        title: `No se pudo recuperar ${this.config.entidad}`,
        showConfirmButton: false,
        timer: 2000,
        toast: true,
        timerProgressBar: true,
      });
    }
  }
}
