-- Oracle Script para Sistema de Gestión de Proyectos
-- Versión: 5.0 LIMPIA PARA ORACLE
-- Fecha: 07 de noviembre de 2025
-- Esquema: apps

-- ORDEN 1: Tablas sin dependencias
CREATE TABLE roles_persona (
  id_roles_persona NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  roles_persona_nombre VARCHAR2(45) NOT NULL,
  roles_persona_descripcion VARCHAR2(100),
  roles_persona_situacion NUMBER(1) DEFAULT 1 NOT NULL
);

CREATE TABLE aplicacion (
  id_aplicacion NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  aplicacion_nombre VARCHAR2(100) NOT NULL,
  aplicacion_desc_corta VARCHAR2(100),
  aplicacion_larga CLOB,
  aplicacion_situacion NUMBER(1) DEFAULT 1 NOT NULL,
  fecha_creacion TIMESTAMP DEFAULT SYSTIMESTAMP NOT NULL,
  fecha_modificacion TIMESTAMP,
  creado_por NUMBER,
  modificado_por NUMBER
);

CREATE INDEX idx_aplicacion_nombre ON aplicacion(aplicacion_nombre);

CREATE TABLE estados (
  id_estados NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  estados_nombre VARCHAR2(45) NOT NULL,
  estados_descripcion VARCHAR2(100),
  estados_color VARCHAR2(7),
  estados_situacion NUMBER(1) DEFAULT 1 NOT NULL
);

CREATE TABLE categorias_documentos (
  id_categorias_documentos NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  categorias_documentos_nombre VARCHAR2(100) NOT NULL,
  categorias_documentos_descripcion VARCHAR2(255),
  categorias_documentos_situacion NUMBER(1) DEFAULT 1 NOT NULL
);

-- ORDEN 2: Tabla persona (depende de roles_persona)
CREATE TABLE persona (
  id_persona NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  persona_nombres VARCHAR2(85) NOT NULL,
  persona_apellidos VARCHAR2(85) NOT NULL,
  persona_identidad VARCHAR2(45) NOT NULL,
  persona_telefono VARCHAR2(45),
  persona_correo VARCHAR2(100),
  persona_situacion NUMBER(1) DEFAULT 1 NOT NULL,
  roles_persona_id_roles_persona NUMBER,
  CONSTRAINT fk_persona_roles FOREIGN KEY (roles_persona_id_roles_persona) REFERENCES roles_persona (id_roles_persona) ON DELETE SET NULL
);

CREATE INDEX idx_persona_roles ON persona(roles_persona_id_roles_persona);

-- ORDEN 3: Tabla usuarios (depende de persona)
CREATE TABLE usuarios (
  id_usuarios NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  usuarios_nombre VARCHAR2(45) NOT NULL UNIQUE,
  usuarios_password VARCHAR2(500) NOT NULL,
  usuarios_situacion NUMBER(1) DEFAULT 1 NOT NULL,
  ultimo_acceso TIMESTAMP,
  token_recuperacion VARCHAR2(255),
  token_expiracion TIMESTAMP,
  persona_id_persona NUMBER NOT NULL,
  CONSTRAINT fk_usuarios_persona FOREIGN KEY (persona_id_persona) REFERENCES persona (id_persona)
);

CREATE INDEX idx_usuarios_persona ON usuarios(persona_id_persona);

-- ORDEN 4: Tabla modulos (auto-referencia)
CREATE TABLE modulos (
    id_modulos NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    modulos_nombre VARCHAR2(50) NOT NULL UNIQUE,
    modulos_descripcion VARCHAR2(200),
    modulo_padre_id NUMBER,
    modulos_situacion NUMBER(1) DEFAULT 1,
    CONSTRAINT fk_modulos_padre FOREIGN KEY (modulo_padre_id) REFERENCES modulos(id_modulos) ON DELETE SET NULL
);

CREATE INDEX idx_modulos_padre ON modulos(modulo_padre_id);

-- ORDEN 5: Tabla usuarios_permisos (depende de usuarios y modulos)
CREATE TABLE usuarios_permisos (
  id_usuarios_permisos NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  usuarios_id_usuarios NUMBER NOT NULL,
  modulos_id_modulos NUMBER NOT NULL,
  puede_ver NUMBER(1) DEFAULT 0,
  puede_crear NUMBER(1) DEFAULT 0,
  puede_editar NUMBER(1) DEFAULT 0,
  puede_eliminar NUMBER(1) DEFAULT 0,
  puede_exportar_excel NUMBER(1) DEFAULT 0,
  puede_exportar_pdf NUMBER(1) DEFAULT 0,
  CONSTRAINT fk_permisos_usuarios FOREIGN KEY (usuarios_id_usuarios) REFERENCES usuarios(id_usuarios) ON DELETE CASCADE,
  CONSTRAINT fk_permisos_modulos FOREIGN KEY (modulos_id_modulos) REFERENCES modulos(id_modulos) ON DELETE CASCADE,
  CONSTRAINT uk_usuario_modulo UNIQUE (usuarios_id_usuarios, modulos_id_modulos)
);

CREATE INDEX idx_permisos_usuarios ON usuarios_permisos(usuarios_id_usuarios);
CREATE INDEX idx_permisos_modulos ON usuarios_permisos(modulos_id_modulos);

-- ORDEN 6: Tabla ordenes_aplicaciones (depende de estados, aplicacion, usuarios)
CREATE TABLE ordenes_aplicaciones (
  id_ordenes_aplicaciones NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  ordenes_aplicaciones_codigo VARCHAR2(55) NOT NULL UNIQUE,
  ordenes_aplicaciones_fecha_asignacion TIMESTAMP NOT NULL,
  ordenes_aplicaciones_fecha_entrega TIMESTAMP NOT NULL,
  ordenes_aplicaciones_notas CLOB,
  ordenes_aplicaciones_situacion NUMBER(1) DEFAULT 1 NOT NULL,
  estados_id_estados NUMBER NOT NULL,
  aplicacion_id_aplicacion NUMBER NOT NULL,
  usuarios_id_usuarios NUMBER NOT NULL,
  CONSTRAINT fk_ordenes_estados FOREIGN KEY (estados_id_estados) REFERENCES estados (id_estados),
  CONSTRAINT fk_ordenes_aplicacion FOREIGN KEY (aplicacion_id_aplicacion) REFERENCES aplicacion (id_aplicacion),
  CONSTRAINT fk_ordenes_usuarios FOREIGN KEY (usuarios_id_usuarios) REFERENCES usuarios (id_usuarios)
);

CREATE INDEX idx_ordenes_estados ON ordenes_aplicaciones(estados_id_estados);
CREATE INDEX idx_ordenes_aplicacion ON ordenes_aplicaciones(aplicacion_id_aplicacion);
CREATE INDEX idx_ordenes_usuarios ON ordenes_aplicaciones(usuarios_id_usuarios);

-- ORDEN 7: Tabla notificaciones (depende de persona)
CREATE TABLE notificaciones (
  id_notificaciones NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  notificaciones_titulo VARCHAR2(100) NOT NULL,
  notificaciones_mensaje CLOB NOT NULL,
  notificaciones_leida NUMBER(1) DEFAULT 0 NOT NULL,
  notificaciones_tipo VARCHAR2(50),
  notificaciones_objeto_id NUMBER,
  notificaciones_objeto_tipo VARCHAR2(50),
  notificaciones_fecha TIMESTAMP DEFAULT SYSTIMESTAMP NOT NULL,
  notificaciones_situacion NUMBER(1) DEFAULT 1 NOT NULL,
  persona_id_persona NUMBER NOT NULL,
  CONSTRAINT fk_notificaciones_persona FOREIGN KEY (persona_id_persona) REFERENCES persona (id_persona)
);

CREATE INDEX idx_notificaciones_persona ON notificaciones(persona_id_persona);
CREATE INDEX idx_notificaciones_leida ON notificaciones(notificaciones_leida, notificaciones_fecha);

-- ORDEN 8: Tabla logs_actividad (depende de persona)
CREATE TABLE logs_actividad (
  id_logs_actividad NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  logs_actividad_fecha TIMESTAMP DEFAULT SYSTIMESTAMP NOT NULL,
  logs_actividad_accion VARCHAR2(50) NOT NULL,
  logs_actividad_tabla VARCHAR2(100) NOT NULL,
  logs_actividad_registro_id NUMBER NOT NULL,
  logs_actividad_datos_antiguos CLOB,
  logs_actividad_datos_nuevos CLOB,
  logs_actividad_ip VARCHAR2(45),
  logs_actividad_situacion NUMBER(1) DEFAULT 1 NOT NULL,
  persona_id_persona NUMBER NOT NULL,
  CONSTRAINT fk_logs_persona FOREIGN KEY (persona_id_persona) REFERENCES persona (id_persona)
);

CREATE INDEX idx_logs_persona ON logs_actividad(persona_id_persona);
CREATE INDEX idx_logs_fecha_tabla ON logs_actividad(logs_actividad_fecha, logs_actividad_tabla);

-- ORDEN 9: Tabla tareas_aplicaciones (depende de ordenes_aplicaciones, usuarios)
CREATE TABLE tareas_aplicaciones (
  id_tareas_aplicaciones NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  tareas_aplicaciones_titulo VARCHAR2(255) NOT NULL,
  tareas_aplicaciones_descripcion CLOB,
  tareas_aplicaciones_completada NUMBER(1) DEFAULT 0 NOT NULL,
  tareas_aplicaciones_fecha_limite TIMESTAMP,
  tareas_aplicaciones_fecha_completada TIMESTAMP,
  tareas_aplicaciones_prioridad VARCHAR2(10) DEFAULT 'Media',
  tareas_aplicaciones_situacion NUMBER(1) DEFAULT 1 NOT NULL,
  ordenes_aplicaciones_id_ordenes_aplicaciones NUMBER NOT NULL,
  usuarios_id_usuarios NUMBER NOT NULL,
  CONSTRAINT fk_tareas_ordenes FOREIGN KEY (ordenes_aplicaciones_id_ordenes_aplicaciones) REFERENCES ordenes_aplicaciones (id_ordenes_aplicaciones),
  CONSTRAINT fk_tareas_usuarios FOREIGN KEY (usuarios_id_usuarios) REFERENCES usuarios (id_usuarios),
  CONSTRAINT chk_prioridad CHECK (tareas_aplicaciones_prioridad IN ('Baja', 'Media', 'Alta'))
);

CREATE INDEX idx_tareas_ordenes ON tareas_aplicaciones(ordenes_aplicaciones_id_ordenes_aplicaciones);
CREATE INDEX idx_tareas_usuarios ON tareas_aplicaciones(usuarios_id_usuarios);

-- ORDEN 10: Tabla personal_proyecto (depende de persona, ordenes_aplicaciones)
CREATE TABLE personal_proyecto (
  id_personal_proyecto NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  personal_proyecto_rol VARCHAR2(100),
  personal_proyecto_fecha_asignacion TIMESTAMP DEFAULT SYSTIMESTAMP NOT NULL,
  personal_proyecto_situacion NUMBER(1) DEFAULT 1 NOT NULL,
  persona_id_persona NUMBER NOT NULL,
  ordenes_aplicaciones_id_ordenes_aplicaciones NUMBER NOT NULL,
  CONSTRAINT fk_personal_persona FOREIGN KEY (persona_id_persona) REFERENCES persona (id_persona),
  CONSTRAINT fk_personal_ordenes FOREIGN KEY (ordenes_aplicaciones_id_ordenes_aplicaciones) REFERENCES ordenes_aplicaciones (id_ordenes_aplicaciones)
);

CREATE INDEX idx_personal_persona ON personal_proyecto(persona_id_persona);
CREATE INDEX idx_personal_ordenes ON personal_proyecto(ordenes_aplicaciones_id_ordenes_aplicaciones);

-- ORDEN 11: Tabla documentos (auto-referencia y múltiples FK)
CREATE TABLE documentos (
  id_documentos NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  documentos_nombre VARCHAR2(255) NOT NULL,
  documentos_ruta CLOB NOT NULL,
  documentos_extension VARCHAR2(10) NOT NULL,
  documentos_tamanio NUMBER,
  documentos_version NUMBER DEFAULT 1 NOT NULL,
  documentos_fecha_subida TIMESTAMP DEFAULT SYSTIMESTAMP NOT NULL,
  documentos_situacion NUMBER(1) DEFAULT 1 NOT NULL,
  documento_original_id NUMBER,
  ordenes_aplicaciones_id_ordenes_aplicaciones NUMBER NOT NULL,
  categorias_documentos_id_categorias_documentos NUMBER NOT NULL,
  usuarios_id_usuarios NUMBER NOT NULL,
  CONSTRAINT fk_documentos_ordenes FOREIGN KEY (ordenes_aplicaciones_id_ordenes_aplicaciones) REFERENCES ordenes_aplicaciones (id_ordenes_aplicaciones),
  CONSTRAINT fk_documentos_categorias FOREIGN KEY (categorias_documentos_id_categorias_documentos) REFERENCES categorias_documentos (id_categorias_documentos),
  CONSTRAINT fk_documentos_original FOREIGN KEY (documento_original_id) REFERENCES documentos (id_documentos) ON DELETE SET NULL,
  CONSTRAINT fk_documentos_usuarios FOREIGN KEY (usuarios_id_usuarios) REFERENCES usuarios (id_usuarios)
);

CREATE INDEX idx_documentos_ordenes ON documentos(ordenes_aplicaciones_id_ordenes_aplicaciones);
CREATE INDEX idx_documentos_categorias ON documentos(categorias_documentos_id_categorias_documentos);
CREATE INDEX idx_documentos_usuarios ON documentos(usuarios_id_usuarios);
CREATE INDEX idx_documentos_original ON documentos(documento_original_id);
CREATE INDEX idx_documentos_nombre ON documentos(documentos_nombre);

-- ORDEN 12: Tabla sesiones_usuarios (depende de usuarios)
CREATE TABLE sesiones_usuarios (
  sesion_id NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  sesion_token VARCHAR2(255) NOT NULL UNIQUE,
  sesion_fecha_inicio TIMESTAMP DEFAULT SYSTIMESTAMP NOT NULL,
  sesion_fecha_cierre TIMESTAMP,
  sesion_ip VARCHAR2(45),
  sesion_user_agent VARCHAR2(500),
  sesion_estado NUMBER(1) DEFAULT 1 NOT NULL,
  usuarios_id_usuarios NUMBER NOT NULL,
  CONSTRAINT fk_sesiones_usuarios FOREIGN KEY (usuarios_id_usuarios) REFERENCES usuarios (id_usuarios)
);

CREATE INDEX idx_sesiones_usuarios ON sesiones_usuarios(usuarios_id_usuarios);

-- ORDEN 13: Tabla intentos_login (sin dependencias)
CREATE TABLE intentos_login (
  id_intento_login NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
  usuario_nombre VARCHAR2(45) NOT NULL,
  intento_exitoso NUMBER(1) DEFAULT 0 NOT NULL,
  intento_detalle VARCHAR2(255),
  intento_ip VARCHAR2(45),
  intento_fecha TIMESTAMP DEFAULT SYSTIMESTAMP NOT NULL
);

CREATE INDEX idx_intentos_usuario_fecha ON intentos_login(usuario_nombre, intento_fecha);

-- ORDEN 14: Tablas de costos
CREATE TABLE complejidad_opciones (
    id_complejidad NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    complejidad_nombre VARCHAR2(50) NOT NULL,
    complejidad_descripcion CLOB,
    complejidad_factor NUMBER(5,2) DEFAULT 1.00,
    complejidad_situacion NUMBER(1) DEFAULT 1
);

CREATE TABLE seguridad_opciones (
    id_seguridad NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    seguridad_nombre VARCHAR2(50) NOT NULL,
    seguridad_descripcion CLOB,
    seguridad_factor NUMBER(5,2) DEFAULT 1.00,
    seguridad_situacion NUMBER(1) DEFAULT 1
);

CREATE TABLE aplicacion_costos (
    id_aplicacion_costos NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    aplicacion_id_aplicacion NUMBER NOT NULL,
    complejidad_id NUMBER,
    seguridad_id NUMBER,
    costos_horas_estimadas NUMBER(8,2) NOT NULL,
    costos_tarifa_hora NUMBER(10,2) DEFAULT 0,
    costos_total NUMBER(12,2) DEFAULT 0,
    costos_moneda VARCHAR2(10) DEFAULT 'GTQ',
    costos_notas CLOB,
    costos_situacion NUMBER(1) DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT SYSTIMESTAMP,
    fecha_modificacion TIMESTAMP,
    creado_por NUMBER,
    modificado_por NUMBER,
    CONSTRAINT fk_app_costos_aplicacion FOREIGN KEY (aplicacion_id_aplicacion) REFERENCES aplicacion(id_aplicacion),
    CONSTRAINT fk_app_costos_complejidad FOREIGN KEY (complejidad_id) REFERENCES complejidad_opciones(id_complejidad),
    CONSTRAINT fk_app_costos_seguridad FOREIGN KEY (seguridad_id) REFERENCES seguridad_opciones(id_seguridad),
    CONSTRAINT fk_app_costos_creado FOREIGN KEY (creado_por) REFERENCES usuarios(id_usuarios),
    CONSTRAINT fk_app_costos_modificado FOREIGN KEY (modificado_por) REFERENCES usuarios(id_usuarios)
);

CREATE INDEX idx_app_costos_aplicacion ON aplicacion_costos(aplicacion_id_aplicacion);

-- ORDEN 15: Tablas de sistema de tablas
CREATE TABLE tipos_tabla (
    id_tipo_tabla NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    tipos_tabla_nombre VARCHAR2(50) NOT NULL,
    tipos_tabla_descripcion CLOB,
    tipos_tabla_situacion NUMBER(1) DEFAULT 1
);

CREATE TABLE tipos_dato (
    id_tipo_dato NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    tipos_dato_nombre VARCHAR2(50) NOT NULL,
    tipos_dato_descripcion CLOB,
    tipos_dato_situacion NUMBER(1) DEFAULT 1
);

CREATE TABLE tipos_clave (
    id_tipo_clave NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    tipos_clave_nombre VARCHAR2(50) NOT NULL,
    tipos_clave_descripcion CLOB,
    tipos_clave_situacion NUMBER(1) DEFAULT 1
);

CREATE TABLE aplicacion_tablas (
    id_aplicacion_tablas NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    aplicacion_id_aplicacion NUMBER NOT NULL,
    tablas_nombre VARCHAR2(100) NOT NULL,
    tablas_descripcion CLOB,
    tipo_tabla_id NUMBER,
    tablas_situacion NUMBER(1) DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT SYSTIMESTAMP,
    fecha_modificacion TIMESTAMP,
    creado_por NUMBER,
    modificado_por NUMBER,
    CONSTRAINT fk_app_tablas_aplicacion FOREIGN KEY (aplicacion_id_aplicacion) REFERENCES aplicacion(id_aplicacion),
    CONSTRAINT fk_app_tablas_tipo FOREIGN KEY (tipo_tabla_id) REFERENCES tipos_tabla(id_tipo_tabla),
    CONSTRAINT fk_app_tablas_creado FOREIGN KEY (creado_por) REFERENCES usuarios(id_usuarios),
    CONSTRAINT fk_app_tablas_modificado FOREIGN KEY (modificado_por) REFERENCES usuarios(id_usuarios)
);

CREATE INDEX idx_app_tablas_aplicacion ON aplicacion_tablas(aplicacion_id_aplicacion);

CREATE TABLE aplicacion_campos (
    id_aplicacion_campos NUMBER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    aplicacion_tablas_id NUMBER NOT NULL,
    campos_nombre VARCHAR2(100) NOT NULL,
    tipo_dato_id NUMBER NOT NULL,
    campos_longitud NUMBER,
    campos_nulo NUMBER(1) DEFAULT 0,
    tipo_clave_id NUMBER,
    campos_descripcion CLOB,
    campos_situacion NUMBER(1) DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT SYSTIMESTAMP,
    fecha_modificacion TIMESTAMP,
    creado_por NUMBER,
    modificado_por NUMBER,
    CONSTRAINT fk_app_campos_tabla FOREIGN KEY (aplicacion_tablas_id) REFERENCES aplicacion_tablas(id_aplicacion_tablas),
    CONSTRAINT fk_app_campos_tipo_dato FOREIGN KEY (tipo_dato_id) REFERENCES tipos_dato(id_tipo_dato),
    CONSTRAINT fk_app_campos_tipo_clave FOREIGN KEY (tipo_clave_id) REFERENCES tipos_clave(id_tipo_clave),
    CONSTRAINT fk_app_campos_creado FOREIGN KEY (creado_por) REFERENCES usuarios(id_usuarios),
    CONSTRAINT fk_app_campos_modificado FOREIGN KEY (modificado_por) REFERENCES usuarios(id_usuarios)
);

CREATE INDEX idx_app_campos_tabla ON aplicacion_campos(aplicacion_tablas_id);

COMMIT;