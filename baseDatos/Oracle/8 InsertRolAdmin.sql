-- INSERTS DE DATOS INICIALES PARA ORACLE

-- Rol Administrador
INSERT INTO roles_persona (roles_persona_nombre, roles_persona_situacion) 
VALUES ('Administrador', 1);

-- Estados de proyectos
INSERT INTO estados (estados_nombre, estados_descripcion, estados_color, estados_situacion) 
VALUES ('Pendiente', 'Proyecto en espera de asignación', '#FFC107', 1);

INSERT INTO estados (estados_nombre, estados_descripcion, estados_color, estados_situacion) 
VALUES ('En Proceso', 'Proyecto en desarrollo', '#2196F3', 1);

INSERT INTO estados (estados_nombre, estados_descripcion, estados_color, estados_situacion) 
VALUES ('Completado', 'Proyecto finalizado', '#4CAF50', 1);

INSERT INTO estados (estados_nombre, estados_descripcion, estados_color, estados_situacion) 
VALUES ('Cancelado', 'Proyecto cancelado', '#F44336', 1);

-- Categorías de documentos
INSERT INTO categorias_documentos (categorias_documentos_nombre, categorias_documentos_descripcion, categorias_documentos_situacion) 
VALUES ('Manual Técnico', 'Documentación técnica del sistema incluyendo componentes y especificaciones técnicas', 1);

INSERT INTO categorias_documentos (categorias_documentos_nombre, categorias_documentos_descripcion, categorias_documentos_situacion) 
VALUES ('Manual de Usuario', 'Guías y documentación orientada al usuario final para el uso del sistema', 1);

INSERT INTO categorias_documentos (categorias_documentos_nombre, categorias_documentos_descripcion, categorias_documentos_situacion) 
VALUES ('Diccionario de Datos', 'Documentación que describe la estructura, tipos de datos y relaciones de la base de datos', 1);

INSERT INTO categorias_documentos (categorias_documentos_nombre, categorias_documentos_descripcion, categorias_documentos_situacion) 
VALUES ('Diagrama Entidad Relación', 'Representación gráfica de las entidades y sus relaciones en la base de datos', 1);

-- Opciones de complejidad
INSERT INTO complejidad_opciones (complejidad_nombre, complejidad_descripcion, complejidad_factor, complejidad_situacion) 
VALUES ('Básica', 'Aplicación con funcionalidades simples y requisitos mínimos', 1.00, 1);

INSERT INTO complejidad_opciones (complejidad_nombre, complejidad_descripcion, complejidad_factor, complejidad_situacion) 
VALUES ('Media', 'Aplicación con características moderadas y algunos requisitos complejos', 1.20, 1);

INSERT INTO complejidad_opciones (complejidad_nombre, complejidad_descripcion, complejidad_factor, complejidad_situacion) 
VALUES ('Alta', 'Aplicación con funcionalidades avanzadas y requisitos complejos', 1.50, 1);

-- Opciones de seguridad
INSERT INTO seguridad_opciones (seguridad_nombre, seguridad_descripcion, seguridad_factor, seguridad_situacion) 
VALUES ('Básica', 'Autenticación simple, sin requisitos especiales de seguridad', 1.00, 1);

INSERT INTO seguridad_opciones (seguridad_nombre, seguridad_descripcion, seguridad_factor, seguridad_situacion) 
VALUES ('Media', 'Autenticación robusta, validaciones básicas, roles de usuario', 1.20, 1);

INSERT INTO seguridad_opciones (seguridad_nombre, seguridad_descripcion, seguridad_factor, seguridad_situacion) 
VALUES ('Alta', 'Encriptación avanzada, autenticación multi-factor, auditoría completa', 1.50, 1);

INSERT INTO seguridad_opciones (seguridad_nombre, seguridad_descripcion, seguridad_factor, seguridad_situacion) 
VALUES ('Crítica', 'Sistemas financieros o médicos con máxima seguridad y cumplimiento normativo', 1.80, 1);

-- Tipos de tabla
INSERT INTO tipos_tabla (tipos_tabla_nombre, tipos_tabla_descripcion, tipos_tabla_situacion) 
VALUES ('Maestra', 'Tablas que almacenan datos maestros o catalogos del sistema', 1);

INSERT INTO tipos_tabla (tipos_tabla_nombre, tipos_tabla_descripcion, tipos_tabla_situacion) 
VALUES ('Transaccional', 'Tablas que registran operaciones y movimientos del negocio', 1);

INSERT INTO tipos_tabla (tipos_tabla_nombre, tipos_tabla_descripcion, tipos_tabla_situacion) 
VALUES ('Configuración', 'Tablas de parámetros y configuración del sistema', 1);

INSERT INTO tipos_tabla (tipos_tabla_nombre, tipos_tabla_descripcion, tipos_tabla_situacion) 
VALUES ('Auditoría', 'Tablas para tracking de cambios y logs del sistema', 1);

INSERT INTO tipos_tabla (tipos_tabla_nombre, tipos_tabla_descripcion, tipos_tabla_situacion) 
VALUES ('Relacional', 'Tablas puente para relaciones muchos-a-muchos', 1);

INSERT INTO tipos_tabla (tipos_tabla_nombre, tipos_tabla_descripcion, tipos_tabla_situacion) 
VALUES ('Temporal', 'Tablas para datos temporales o de sesión', 1);

INSERT INTO tipos_tabla (tipos_tabla_nombre, tipos_tabla_descripcion, tipos_tabla_situacion) 
VALUES ('Histórica', 'Tablas que almacenan versiones históricas de datos', 1);

INSERT INTO tipos_tabla (tipos_tabla_nombre, tipos_tabla_descripcion, tipos_tabla_situacion) 
VALUES ('Sistema', 'Tablas internas del sistema y metadatos', 1);

-- Tipos de dato
INSERT INTO tipos_dato (tipos_dato_nombre, tipos_dato_descripcion, tipos_dato_situacion) 
VALUES ('NUMBER', 'Número entero o decimal', 1);

INSERT INTO tipos_dato (tipos_dato_nombre, tipos_dato_descripcion, tipos_dato_situacion) 
VALUES ('INTEGER', 'Número entero', 1);

INSERT INTO tipos_dato (tipos_dato_nombre, tipos_dato_descripcion, tipos_dato_situacion) 
VALUES ('VARCHAR2', 'Cadena de texto de longitud variable', 1);

INSERT INTO tipos_dato (tipos_dato_nombre, tipos_dato_descripcion, tipos_dato_situacion) 
VALUES ('CHAR', 'Cadena de texto de longitud fija', 1);

INSERT INTO tipos_dato (tipos_dato_nombre, tipos_dato_descripcion, tipos_dato_situacion) 
VALUES ('CLOB', 'Texto de longitud muy larga', 1);

INSERT INTO tipos_dato (tipos_dato_nombre, tipos_dato_descripcion, tipos_dato_situacion) 
VALUES ('DATE', 'Fecha y hora', 1);

INSERT INTO tipos_dato (tipos_dato_nombre, tipos_dato_descripcion, tipos_dato_situacion) 
VALUES ('TIMESTAMP', 'Marca de tiempo con precisión de fracciones de segundo', 1);

INSERT INTO tipos_dato (tipos_dato_nombre, tipos_dato_descripcion, tipos_dato_situacion) 
VALUES ('BLOB', 'Datos binarios grandes', 1);

INSERT INTO tipos_dato (tipos_dato_nombre, tipos_dato_descripcion, tipos_dato_situacion) 
VALUES ('RAW', 'Datos binarios de longitud variable', 1);

INSERT INTO tipos_dato (tipos_dato_nombre, tipos_dato_descripcion, tipos_dato_situacion) 
VALUES ('LONG', 'Texto largo (obsoleto, usar CLOB)', 1);

-- Tipos de clave
INSERT INTO tipos_clave (tipos_clave_nombre, tipos_clave_descripcion, tipos_clave_situacion) 
VALUES ('PRIMARY KEY', 'Clave primaria que identifica de forma única cada registro', 1);

INSERT INTO tipos_clave (tipos_clave_nombre, tipos_clave_descripcion, tipos_clave_situacion) 
VALUES ('FOREIGN KEY', 'Clave foránea que referencia a una clave primaria en otra tabla', 1);

INSERT INTO tipos_clave (tipos_clave_nombre, tipos_clave_descripcion, tipos_clave_situacion) 
VALUES ('UNIQUE', 'Restricción que asegura que todos los valores en la columna sean únicos', 1);

INSERT INTO tipos_clave (tipos_clave_nombre, tipos_clave_descripcion, tipos_clave_situacion) 
VALUES ('INDEX', 'Índice para mejorar el rendimiento de las consultas', 1);

INSERT INTO tipos_clave (tipos_clave_nombre, tipos_clave_descripcion, tipos_clave_situacion) 
VALUES ('COMPOSITE KEY', 'Clave formada por múltiples columnas', 1);

-- Módulos del sistema
INSERT INTO modulos (modulos_nombre, modulos_descripcion, modulo_padre_id, modulos_situacion) 
VALUES ('dashboard', 'Panel Principal', NULL, 1);

INSERT INTO modulos (modulos_nombre, modulos_descripcion, modulo_padre_id, modulos_situacion) 
VALUES ('mis-proyectos', 'Mis Proyectos Asignados', NULL, 1);

INSERT INTO modulos (modulos_nombre, modulos_descripcion, modulo_padre_id, modulos_situacion) 
VALUES ('estadisticas', 'Estadísticas y Reportes', NULL, 1);

INSERT INTO modulos (modulos_nombre, modulos_descripcion, modulo_padre_id, modulos_situacion) 
VALUES ('proyectos', 'Gestión de Proyectos', NULL, 1);

INSERT INTO modulos (modulos_nombre, modulos_descripcion, modulo_padre_id, modulos_situacion) 
VALUES ('estados', 'Estados de Proyectos', NULL, 1);

INSERT INTO modulos (modulos_nombre, modulos_descripcion, modulo_padre_id, modulos_situacion) 
VALUES ('costos', 'Gestión de Costos', NULL, 1);

INSERT INTO modulos (modulos_nombre, modulos_descripcion, modulo_padre_id, modulos_situacion) 
VALUES ('complejidad', 'Niveles de Complejidad', NULL, 1);

INSERT INTO modulos (modulos_nombre, modulos_descripcion, modulo_padre_id, modulos_situacion) 
VALUES ('seguridad', 'Niveles de Seguridad', NULL, 1);

INSERT INTO modulos (modulos_nombre, modulos_descripcion, modulo_padre_id, modulos_situacion) 
VALUES ('tablas', 'Diseño de Tablas', NULL, 1);

INSERT INTO modulos (modulos_nombre, modulos_descripcion, modulo_padre_id, modulos_situacion) 
VALUES ('campos', 'Campos de Tablas', NULL, 1);

INSERT INTO modulos (modulos_nombre, modulos_descripcion, modulo_padre_id, modulos_situacion) 
VALUES ('tipos-tabla', 'Tipos de Tabla', NULL, 1);

INSERT INTO modulos (modulos_nombre, modulos_descripcion, modulo_padre_id, modulos_situacion) 
VALUES ('tipos-dato', 'Tipos de Dato', NULL, 1);

INSERT INTO modulos (modulos_nombre, modulos_descripcion, modulo_padre_id, modulos_situacion) 
VALUES ('tipos-clave', 'Tipos de Clave', NULL, 1);

INSERT INTO modulos (modulos_nombre, modulos_descripcion, modulo_padre_id, modulos_situacion) 
VALUES ('documentos', 'Gestión de Documentos', NULL, 1);

INSERT INTO modulos (modulos_nombre, modulos_descripcion, modulo_padre_id, modulos_situacion) 
VALUES ('categorias', 'Categorías de Documentos', NULL, 1);

INSERT INTO modulos (modulos_nombre, modulos_descripcion, modulo_padre_id, modulos_situacion) 
VALUES ('personal', 'Gestión de Personal', NULL, 1);

INSERT INTO modulos (modulos_nombre, modulos_descripcion, modulo_padre_id, modulos_situacion) 
VALUES ('personal-proyecto', 'Asignaciones de Personal', NULL, 1);

INSERT INTO modulos (modulos_nombre, modulos_descripcion, modulo_padre_id, modulos_situacion) 
VALUES ('tareas', 'Gestión de Tareas', NULL, 1);

INSERT INTO modulos (modulos_nombre, modulos_descripcion, modulo_padre_id, modulos_situacion) 
VALUES ('usuarios', 'Gestión de Usuarios', NULL, 1);

INSERT INTO modulos (modulos_nombre, modulos_descripcion, modulo_padre_id, modulos_situacion) 
VALUES ('roles', 'Roles de Usuario', NULL, 1);

INSERT INTO modulos (modulos_nombre, modulos_descripcion, modulo_padre_id, modulos_situacion) 
VALUES ('modulos', 'Módulos del Sistema', NULL, 1);

INSERT INTO modulos (modulos_nombre, modulos_descripcion, modulo_padre_id, modulos_situacion) 
VALUES ('usuarios-permisos', 'Permisos de Usuarios', NULL, 1);

INSERT INTO modulos (modulos_nombre, modulos_descripcion, modulo_padre_id, modulos_situacion) 
VALUES ('sistema', 'Configuración General', NULL, 1);

INSERT INTO modulos (modulos_nombre, modulos_descripcion, modulo_padre_id, modulos_situacion) 
VALUES ('logs', 'Logs de Actividad', NULL, 1);

INSERT INTO modulos (modulos_nombre, modulos_descripcion, modulo_padre_id, modulos_situacion) 
VALUES ('sesiones', 'Sesiones de Usuario', NULL, 1);

INSERT INTO modulos (modulos_nombre, modulos_descripcion, modulo_padre_id, modulos_situacion) 
VALUES ('notificaciones', 'Notificaciones', NULL, 1);

INSERT INTO modulos (modulos_nombre, modulos_descripcion, modulo_padre_id, modulos_situacion) 
VALUES ('proyectos-asignados', 'Proyectos Asignados', NULL, 1);

COMMIT;