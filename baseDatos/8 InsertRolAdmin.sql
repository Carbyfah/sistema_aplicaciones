INSERT INTO roles_persona (roles_persona_nombre, roles_persona_situacion) 
VALUES ('Administrador', 1);

INSERT INTO estados (estados_nombre, estados_descripcion, estados_color, estados_situacion) VALUES
('Pendiente', 'Proyecto en espera de asignación', '#FFC107', 1),
('En Proceso', 'Proyecto en desarrollo', '#2196F3', 1),
('Completado', 'Proyecto finalizado', '#4CAF50', 1),
('Cancelado', 'Proyecto cancelado', '#F44336', 1);

INSERT INTO categorias_documentos (categorias_documentos_nombre, categorias_documentos_descripcion, categorias_documentos_situacion) VALUES
('Manual Técnico', 'Documentación técnica del sistema incluyendo componentes y especificaciones técnicas', 1),
('Manual de Usuario', 'Guías y documentación orientada al usuario final para el uso del sistema', 1),
('Diccionario de Datos', 'Documentación que describe la estructura, tipos de datos y relaciones de la base de datos', 1),
('Diagrama Entidad Relación', 'Representación gráfica de las entidades y sus relaciones en la base de datos', 1);

INSERT INTO complejidad_opciones (complejidad_nombre, complejidad_descripcion, complejidad_factor, complejidad_situacion) VALUES
('Básica', 'Aplicación con funcionalidades simples y requisitos mínimos', 1.00, 1),
('Media', 'Aplicación con características moderadas y algunos requisitos complejos', 1.20, 1),
('Alta', 'Aplicación con funcionalidades avanzadas y requisitos complejos', 1.50, 1);

INSERT INTO seguridad_opciones (seguridad_nombre, seguridad_descripcion, seguridad_factor, seguridad_situacion) VALUES
('Básica', 'Autenticación simple, sin requisitos especiales de seguridad', 1.00, 1),
('Media', 'Autenticación robusta, validaciones básicas, roles de usuario', 1.20, 1),
('Alta', 'Encriptación avanzada, autenticación multi-factor, auditoría completa', 1.50, 1),
('Crítica', 'Sistemas financieros o médicos con máxima seguridad y cumplimiento normativo', 1.80, 1);

INSERT INTO tipos_tabla (tipos_tabla_nombre, tipos_tabla_descripcion, tipos_tabla_situacion) VALUES
('Maestra', 'Tablas que almacenan datos maestros o catalogos del sistema', 1),
('Transaccional', 'Tablas que registran operaciones y movimientos del negocio', 1),
('Configuración', 'Tablas de parámetros y configuración del sistema', 1),
('Auditoría', 'Tablas para tracking de cambios y logs del sistema', 1),
('Relacional', 'Tablas puente para relaciones muchos-a-muchos', 1),
('Temporal', 'Tablas para datos temporales o de sesión', 1),
('Histórica', 'Tablas que almacenan versiones históricas de datos', 1),
('Sistema', 'Tablas internas del sistema y metadatos', 1);

INSERT INTO tipos_dato (tipos_dato_nombre, tipos_dato_descripcion, tipos_dato_situacion) VALUES
('INT', 'Número entero', 1),
('BIGINT', 'Número entero grande', 1),
('SMALLINT', 'Número entero pequeño', 1),
('TINYINT', 'Número entero muy pequeño', 1),
('VARCHAR', 'Cadena de texto de longitud variable', 1),
('CHAR', 'Cadena de texto de longitud fija', 1),
('TEXT', 'Texto de longitud larga', 1),
('LONGTEXT', 'Texto de longitud muy larga', 1),
('MEDIUMTEXT', 'Texto de longitud media', 1),
('DECIMAL', 'Número decimal con precisión especificada', 1),
('NUMERIC', 'Número decimal (sinónimo de DECIMAL)', 1),
('FLOAT', 'Número de punto flotante de precisión simple', 1),
('DOUBLE', 'Número de punto flotante de doble precisión', 1),
('DATETIME', 'Fecha y hora', 1),
('DATE', 'Solo fecha', 1),
('TIME', 'Solo hora', 1),
('TIMESTAMP', 'Marca de tiempo automática', 1),
('YEAR', 'Solo año', 1),
('BOOLEAN', 'Valor verdadero o falso (alias de TINYINT(1))', 1),
('ENUM', 'Lista de valores predefinidos', 1),
('SET', 'Conjunto de valores predefinidos', 1),
('JSON', 'Datos en formato JSON', 1),
('BLOB', 'Datos binarios grandes', 1),
('LONGBLOB', 'Datos binarios muy grandes', 1),
('MEDIUMBLOB', 'Datos binarios de tamaño medio', 1),
('TINYBLOB', 'Datos binarios pequeños', 1);

INSERT INTO tipos_clave (tipos_clave_nombre, tipos_clave_descripcion, tipos_clave_situacion) VALUES
('PRIMARY KEY', 'Clave primaria que identifica de forma única cada registro', 1),
('FOREIGN KEY', 'Clave foránea que referencia a una clave primaria en otra tabla', 1),
('UNIQUE', 'Restricción que asegura que todos los valores en la columna sean únicos', 1),
('INDEX', 'Índice para mejorar el rendimiento de las consultas', 1),
('FULLTEXT', 'Índice de texto completo para búsquedas de texto', 1),
('SPATIAL', 'Índice espacial para datos geográficos', 1),
('COMPOSITE KEY', 'Clave formada por múltiples columnas', 1);


-- MÓDULOS PRINCIPALES
INSERT INTO modulos (modulos_nombre, modulos_descripcion, modulo_padre_id, modulos_situacion) VALUES
('dashboard', 'Panel Principal', NULL, 1),
('mis-proyectos', 'Mis Proyectos Asignados', NULL, 1),
('estadisticas', 'Estadísticas y Reportes', NULL, 1),
('proyectos', 'Gestión de Proyectos', NULL, 1),
('estados', 'Estados de Proyectos', NULL, 1),
('costos', 'Gestión de Costos', NULL, 1),
('complejidad', 'Niveles de Complejidad', NULL, 1),
('seguridad', 'Niveles de Seguridad', NULL, 1),
('tablas', 'Diseño de Tablas', NULL, 1),
('campos', 'Campos de Tablas', NULL, 1),
('tipos-tabla', 'Tipos de Tabla', NULL, 1),
('tipos-dato', 'Tipos de Dato', NULL, 1),
('tipos-clave', 'Tipos de Clave', NULL, 1),
('documentos', 'Gestión de Documentos', NULL, 1),
('categorias', 'Categorías de Documentos', NULL, 1),
('personal', 'Gestión de Personal', NULL, 1),
('personal-proyecto', 'Asignaciones de Personal', NULL, 1),
('tareas', 'Gestión de Tareas', NULL, 1),
('usuarios', 'Gestión de Usuarios', NULL, 1),
('roles', 'Roles de Usuario', NULL, 1),
('modulos', 'Módulos del Sistema', NULL, 1),
('usuarios-permisos', 'Permisos de Usuarios', NULL, 1),
('sistema', 'Configuración General', NULL, 1),
('logs', 'Logs de Actividad', NULL, 1),
('sesiones', 'Sesiones de Usuario', NULL, 1),
('intentos-login', 'Intentos de Login', NULL, 1),
('notificaciones', 'Notificaciones', NULL, 1),
('proyectos-asignados', 'Proyectos Asignados', NULL, 1);