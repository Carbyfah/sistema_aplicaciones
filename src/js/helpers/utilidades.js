export function formatearFecha(fechaStr) {
  if (!fechaStr) return "";

  const fecha = new Date(fechaStr);
  return new Intl.DateTimeFormat("es-ES", {
    year: "numeric",
    month: "2-digit",
    day: "2-digit",
    hour: "2-digit",
    minute: "2-digit",
  }).format(fecha);
}
