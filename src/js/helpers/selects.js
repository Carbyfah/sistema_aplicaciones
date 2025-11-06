export const initSelect2 = (selector, options = {}) => {
  const defaultOptions = {
    theme: "bootstrap4",
    language: "es",
    dropdownParent: $(selector).closest(".modal").length
      ? $(selector).closest(".modal")
      : $(document.body),
    ...options,
  };

  return $(selector).select2(defaultOptions);
};

export async function cargarSelect(
  apiUrl,
  selectId,
  valueField,
  textField,
  filter = null
) {
  try {
    console.log("Cargando select:", selectId, "desde:", apiUrl);
    const response = await fetch(apiUrl);
    const resultado = await response.json();
    console.log("Resultado:", resultado);

    if (resultado.exito) {
      const select = document.getElementById(selectId);
      if (!select) {
        console.error("Select no encontrado:", selectId);
        return;
      }

      let datos = resultado.data;
      console.log("Datos sin filtrar:", datos);

      if (filter) {
        datos = datos.filter(filter);
        console.log("Datos filtrados:", datos);
      }

      select.innerHTML = `<option value="">Seleccione una opción</option>`;

      datos.forEach((item) => {
        const option = document.createElement("option");
        option.value = item[valueField];

        if (typeof textField === "function") {
          option.textContent = textField(item);
        } else {
          option.textContent = item[textField];
        }

        select.appendChild(option);
      });

      console.log("Opciones agregadas, total:", select.options.length);

      if (window.$) {
        if (window.$(`#${selectId}`).hasClass("select2-hidden-accessible")) {
          window.$(`#${selectId}`).select2("destroy");
        }
        initSelect2(`#${selectId}`);
        console.log("Select2 reinicializado para:", selectId);
      }
    }
  } catch (error) {
    console.error("Error al cargar select:", error);
  }
}
