import * as XLSX from "xlsx";

export function exportarAExcel(datos, columnas, nombreArchivo) {
  const headers = columnas.map((col) => col.header);

  const rows = datos.map((item) => {
    return columnas.map((col) => {
      let valor = col.obtenerValor(item);

      if (valor === null || valor === undefined) {
        return "";
      }

      return valor;
    });
  });

  const worksheet = XLSX.utils.aoa_to_sheet([headers, ...rows]);

  const colWidths = headers.map((_, i) => {
    const maxLength = Math.max(
      headers[i].length,
      ...rows.map((row) => String(row[i] || "").length)
    );
    return { wch: Math.min(maxLength + 2, 50) };
  });

  worksheet["!cols"] = colWidths;

  const workbook = XLSX.utils.book_new();
  XLSX.utils.book_append_sheet(workbook, worksheet, "Datos");

  XLSX.writeFile(workbook, nombreArchivo);
}

export function exportarACSV(datos, columnas, nombreArchivo) {
  const headers = columnas.map((col) => col.header);

  const rows = datos.map((item) => {
    return columnas
      .map((col) => {
        let valor = col.obtenerValor(item);

        if (valor === null || valor === undefined) {
          valor = "";
        }

        const valorString = String(valor);
        return `"${valorString.replace(/"/g, '""')}"`;
      })
      .join(",");
  });

  const headerRow = headers.map((h) => `"${h}"`).join(",");
  const csvContent = "\uFEFF" + headerRow + "\n" + rows.join("\n");

  descargarArchivo(csvContent, nombreArchivo, "text/csv;charset=utf-8;");
}

function descargarArchivo(contenido, nombreArchivo, tipo) {
  const blob = new Blob([contenido], { type: tipo });
  const link = document.createElement("a");
  const url = URL.createObjectURL(blob);

  link.setAttribute("href", url);
  link.setAttribute("download", nombreArchivo);
  link.style.visibility = "hidden";

  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
  URL.revokeObjectURL(url);
}
