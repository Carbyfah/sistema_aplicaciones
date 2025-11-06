// Configuración por defecto de DataTables
export const initDataTable = (selector, options = {}) => {
  const defaultOptions = {
    responsive: true,
    autoWidth: false,
    pageLength: 25,
    lengthMenu: [
      [10, 25, 50, 100, -1],
      [10, 25, 50, 100, "Todos"],
    ],
    order: [[0, "desc"]],
    language: {
      url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json",
    },
  };

  // Merge profundo para no perder la configuración de idioma
  const mergedOptions = {
    ...defaultOptions,
    ...options,
    language: {
      ...defaultOptions.language,
      ...(options.language || {}),
    },
  };

  return $(selector).DataTable(mergedOptions);
};
