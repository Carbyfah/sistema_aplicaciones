-- MariaDB Script para Sistema de Gestión de Proyectos
-- Versión: 5.0 LIMPIA (Sistema de permisos granulares como Magic Travel)
-- Fecha: 04 de noviembre de 2025

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

CREATE SCHEMA IF NOT EXISTS `apps` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `apps`;

-- -----------------------------------------------------
-- Table roles_persona
-- -----------------------------------------------------
CREATE TABLE roles_persona (
  id_roles_persona INT NOT NULL AUTO_INCREMENT,
  roles_persona_nombre VARCHAR(45) NOT NULL,
  roles_persona_descripcion VARCHAR(100) NULL,
  roles_persona_situacion SMALLINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id_roles_persona)
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table aplicacion
-- -----------------------------------------------------
CREATE TABLE aplicacion (
  id_aplicacion INT NOT NULL AUTO_INCREMENT,
  aplicacion_nombre VARCHAR(100) NOT NULL,
  aplicacion_desc_corta VARCHAR(100) NULL,
  aplicacion_larga TEXT NULL,
  aplicacion_situacion SMALLINT(1) NOT NULL DEFAULT 1,
  fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  fecha_modificacion DATETIME NULL,
  creado_por INT NULL,
  modificado_por INT NULL,
  PRIMARY KEY (id_aplicacion)
) ENGINE = InnoDB;

ALTER TABLE aplicacion ADD FULLTEXT INDEX idx_aplicacion_busqueda (aplicacion_nombre, aplicacion_desc_corta, aplicacion_larga);

-- -----------------------------------------------------
-- Table estados
-- -----------------------------------------------------
CREATE TABLE estados (
  id_estados INT NOT NULL AUTO_INCREMENT,
  estados_nombre VARCHAR(45) NOT NULL,
  estados_descripcion VARCHAR(100) NULL,
  estados_color VARCHAR(7) NULL,
  estados_situacion SMALLINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id_estados)
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table categorias_documentos
-- -----------------------------------------------------
CREATE TABLE categorias_documentos (
  id_categorias_documentos INT NOT NULL AUTO_INCREMENT,
  categorias_documentos_nombre VARCHAR(100) NOT NULL,
  categorias_documentos_descripcion VARCHAR(255) NULL,
  categorias_documentos_situacion SMALLINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id_categorias_documentos)
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table persona
-- -----------------------------------------------------
CREATE TABLE persona (
  id_persona INT NOT NULL AUTO_INCREMENT,
  persona_nombres VARCHAR(85) NOT NULL,
  persona_apellidos VARCHAR(85) NOT NULL,
  persona_identidad VARCHAR(45) NOT NULL,
  persona_telefono VARCHAR(45) NULL,
  persona_correo VARCHAR(100) NULL,
  persona_situacion SMALLINT(1) NOT NULL DEFAULT 1,
  roles_persona_id_roles_persona INT NULL,
  PRIMARY KEY (id_persona),
  INDEX fk_persona_tipos_persona1_idx (roles_persona_id_roles_persona),
  CONSTRAINT fk_persona_tipos_persona1
    FOREIGN KEY (roles_persona_id_roles_persona)
    REFERENCES roles_persona (id_roles_persona)
    ON DELETE SET NULL
    ON UPDATE NO ACTION
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table usuarios
-- -----------------------------------------------------
CREATE TABLE usuarios (
  id_usuarios INT NOT NULL AUTO_INCREMENT,
  usuarios_nombre VARCHAR(45) NOT NULL,
  usuarios_password VARCHAR(500) NOT NULL,
  usuarios_situacion SMALLINT(1) NOT NULL DEFAULT 1,
  ultimo_acceso DATETIME NULL,
  token_recuperacion VARCHAR(255) NULL,
  token_expiracion DATETIME NULL,
  persona_id_persona INT NOT NULL,
  PRIMARY KEY (id_usuarios),
  UNIQUE INDEX usuarios_nombre_UNIQUE (usuarios_nombre),
  INDEX fk_usuarios_persona1_idx (persona_id_persona),
  CONSTRAINT fk_usuarios_persona1
    FOREIGN KEY (persona_id_persona)
    REFERENCES persona (id_persona)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table modulos (Sistema de permisos como Magic Travel)
-- -----------------------------------------------------
CREATE TABLE modulos (
    id_modulos INT PRIMARY KEY AUTO_INCREMENT,
    modulos_nombre VARCHAR(50) NOT NULL UNIQUE,
    modulos_descripcion VARCHAR(200),
    modulo_padre_id INT NULL,
    modulos_situacion SMALLINT(1) DEFAULT 1,
    FOREIGN KEY (modulo_padre_id) REFERENCES modulos(id_modulos) ON DELETE SET NULL
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table usuarios_permisos (Granular como Magic Travel)
-- -----------------------------------------------------
CREATE TABLE usuarios_permisos (
  id_usuarios_permisos INT PRIMARY KEY AUTO_INCREMENT,
  usuarios_id_usuarios INT NOT NULL,
  modulos_id_modulos INT NOT NULL,
  puede_ver BOOLEAN DEFAULT FALSE,
  puede_crear BOOLEAN DEFAULT FALSE,
  puede_editar BOOLEAN DEFAULT FALSE,
  puede_eliminar BOOLEAN DEFAULT FALSE,
  puede_exportar_excel BOOLEAN DEFAULT FALSE,
  puede_exportar_pdf BOOLEAN DEFAULT FALSE,
  FOREIGN KEY (usuarios_id_usuarios) REFERENCES usuarios(id_usuarios) ON DELETE CASCADE,
  FOREIGN KEY (modulos_id_modulos) REFERENCES modulos(id_modulos) ON DELETE CASCADE,
  UNIQUE KEY unique_usuario_modulo (usuarios_id_usuarios, modulos_id_modulos)
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table ordenes_aplicaciones
-- -----------------------------------------------------
CREATE TABLE ordenes_aplicaciones (
  id_ordenes_aplicaciones INT NOT NULL AUTO_INCREMENT,
  ordenes_aplicaciones_codigo VARCHAR(55) NOT NULL,
  ordenes_aplicaciones_fecha_asignacion DATETIME NOT NULL,
  ordenes_aplicaciones_fecha_entrega DATETIME NOT NULL,
  ordenes_aplicaciones_notas TEXT NULL,
  ordenes_aplicaciones_situacion SMALLINT(1) NOT NULL DEFAULT 1,
  estados_id_estados INT NOT NULL,
  aplicacion_id_aplicacion INT NOT NULL,
  usuarios_id_usuarios INT NOT NULL,
  PRIMARY KEY (id_ordenes_aplicaciones),
  UNIQUE INDEX ordenes_aplicaciones_codigo_UNIQUE (ordenes_aplicaciones_codigo),
  INDEX fk_ordenes_aplicaciones_estados_idx (estados_id_estados),
  INDEX fk_ordenes_aplicaciones_aplicacion1_idx (aplicacion_id_aplicacion),
  INDEX fk_ordenes_aplicaciones_usuarios1_idx (usuarios_id_usuarios),
  CONSTRAINT fk_ordenes_aplicaciones_estados
    FOREIGN KEY (estados_id_estados)
    REFERENCES estados (id_estados)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT fk_ordenes_aplicaciones_aplicacion1
    FOREIGN KEY (aplicacion_id_aplicacion)
    REFERENCES aplicacion (id_aplicacion)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT fk_ordenes_aplicaciones_usuarios1
    FOREIGN KEY (usuarios_id_usuarios)
    REFERENCES usuarios (id_usuarios)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE = InnoDB;

ALTER TABLE ordenes_aplicaciones ADD FULLTEXT INDEX idx_ordenes_notas_busqueda (ordenes_aplicaciones_notas);

-- -----------------------------------------------------
-- Table notificaciones
-- -----------------------------------------------------
CREATE TABLE notificaciones (
  id_notificaciones INT NOT NULL AUTO_INCREMENT,
  notificaciones_titulo VARCHAR(100) NOT NULL,
  notificaciones_mensaje TEXT NOT NULL,
  notificaciones_leida SMALLINT(1) NOT NULL DEFAULT 0,
  notificaciones_tipo VARCHAR(50) NULL,
  notificaciones_objeto_id INT NULL,
  notificaciones_objeto_tipo VARCHAR(50) NULL,
  notificaciones_fecha DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  notificaciones_situacion SMALLINT(1) NOT NULL DEFAULT 1,
  persona_id_persona INT NOT NULL,
  PRIMARY KEY (id_notificaciones),
  INDEX fk_notificaciones_persona1_idx (persona_id_persona),
  INDEX idx_notificaciones_leida (notificaciones_leida, notificaciones_fecha),
  CONSTRAINT fk_notificaciones_persona1
    FOREIGN KEY (persona_id_persona)
    REFERENCES persona (id_persona)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table logs_actividad
-- -----------------------------------------------------
CREATE TABLE logs_actividad (
  id_logs_actividad INT NOT NULL AUTO_INCREMENT,
  logs_actividad_fecha DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  logs_actividad_accion VARCHAR(50) NOT NULL,
  logs_actividad_tabla VARCHAR(100) NOT NULL,
  logs_actividad_registro_id INT NOT NULL,
  logs_actividad_datos_antiguos TEXT NULL,
  logs_actividad_datos_nuevos TEXT NULL,
  logs_actividad_ip VARCHAR(45) NULL,
  logs_actividad_situacion SMALLINT(1) NOT NULL DEFAULT 1,
  persona_id_persona INT NOT NULL,
  PRIMARY KEY (id_logs_actividad),
  INDEX fk_logs_actividad_persona1_idx (persona_id_persona),
  INDEX idx_logs_fecha_tabla (logs_actividad_fecha, logs_actividad_tabla),
  CONSTRAINT fk_logs_actividad_persona1
    FOREIGN KEY (persona_id_persona)
    REFERENCES persona (id_persona)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table tareas_aplicaciones
-- -----------------------------------------------------
CREATE TABLE tareas_aplicaciones (
  id_tareas_aplicaciones INT NOT NULL AUTO_INCREMENT,
  tareas_aplicaciones_titulo VARCHAR(255) NOT NULL,
  tareas_aplicaciones_descripcion TEXT NULL,
  tareas_aplicaciones_completada SMALLINT(1) NOT NULL DEFAULT 0,
  tareas_aplicaciones_fecha_limite DATETIME NULL,
  tareas_aplicaciones_fecha_completada DATETIME NULL,
  tareas_aplicaciones_prioridad ENUM('Baja', 'Media', 'Alta') DEFAULT 'Media',
  tareas_aplicaciones_situacion SMALLINT(1) NOT NULL DEFAULT 1,
  ordenes_aplicaciones_id_ordenes_aplicaciones INT NOT NULL,
  usuarios_id_usuarios INT NOT NULL,
  PRIMARY KEY (id_tareas_aplicaciones),
  INDEX fk_tareas_aplicaciones_ordenes1_idx (ordenes_aplicaciones_id_ordenes_aplicaciones),
  INDEX fk_tareas_aplicaciones_usuarios1_idx (usuarios_id_usuarios),
  CONSTRAINT fk_tareas_aplicaciones_ordenes1
    FOREIGN KEY (ordenes_aplicaciones_id_ordenes_aplicaciones)
    REFERENCES ordenes_aplicaciones (id_ordenes_aplicaciones)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT fk_tareas_aplicaciones_usuarios1
    FOREIGN KEY (usuarios_id_usuarios)
    REFERENCES usuarios (id_usuarios)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE = InnoDB;

ALTER TABLE tareas_aplicaciones ADD FULLTEXT INDEX idx_tareas_titulo_descripcion (tareas_aplicaciones_titulo, tareas_aplicaciones_descripcion);

-- -----------------------------------------------------
-- Table personal_proyecto
-- -----------------------------------------------------
CREATE TABLE personal_proyecto (
  id_personal_proyecto INT NOT NULL AUTO_INCREMENT,
  personal_proyecto_rol VARCHAR(100) NULL,
  personal_proyecto_fecha_asignacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  personal_proyecto_situacion SMALLINT(1) NOT NULL DEFAULT 1,
  persona_id_persona INT NOT NULL,
  ordenes_aplicaciones_id_ordenes_aplicaciones INT NOT NULL,
  PRIMARY KEY (id_personal_proyecto),
  INDEX fk_personal_proyecto_persona1_idx (persona_id_persona),
  INDEX fk_personal_proyecto_ordenes_aplicaciones1_idx (ordenes_aplicaciones_id_ordenes_aplicaciones),
  CONSTRAINT fk_personal_proyecto_persona1
    FOREIGN KEY (persona_id_persona)
    REFERENCES persona (id_persona)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT fk_personal_proyecto_ordenes_aplicaciones1
    FOREIGN KEY (ordenes_aplicaciones_id_ordenes_aplicaciones)
    REFERENCES ordenes_aplicaciones (id_ordenes_aplicaciones)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table documentos
-- -----------------------------------------------------
CREATE TABLE documentos (
  id_documentos INT NOT NULL AUTO_INCREMENT,
  documentos_nombre VARCHAR(255) NOT NULL,
  documentos_ruta TEXT NOT NULL,
  documentos_extension VARCHAR(10) NOT NULL,
  documentos_tamanio INT NULL,
  documentos_version INT NOT NULL DEFAULT 1,
  documentos_fecha_subida DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  documentos_situacion SMALLINT(1) NOT NULL DEFAULT 1,
  documento_original_id INT NULL,
  ordenes_aplicaciones_id_ordenes_aplicaciones INT NOT NULL,
  categorias_documentos_id_categorias_documentos INT NOT NULL,
  usuarios_id_usuarios INT NOT NULL,
  PRIMARY KEY (id_documentos),
  INDEX fk_documentos_ordenes1_idx (ordenes_aplicaciones_id_ordenes_aplicaciones),
  INDEX fk_documentos_categorias1_idx (categorias_documentos_id_categorias_documentos),
  INDEX fk_documentos_usuarios1_idx (usuarios_id_usuarios),
  INDEX fk_documentos_original_idx (documento_original_id),
  CONSTRAINT fk_documentos_ordenes1
    FOREIGN KEY (ordenes_aplicaciones_id_ordenes_aplicaciones)
    REFERENCES ordenes_aplicaciones (id_ordenes_aplicaciones)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT fk_documentos_categorias1
    FOREIGN KEY (categorias_documentos_id_categorias_documentos)
    REFERENCES categorias_documentos (id_categorias_documentos)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT fk_documentos_original
    FOREIGN KEY (documento_original_id)
    REFERENCES documentos (id_documentos)
    ON DELETE SET NULL
    ON UPDATE NO ACTION,
  CONSTRAINT fk_documentos_usuarios1
    FOREIGN KEY (usuarios_id_usuarios)
    REFERENCES usuarios (id_usuarios)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE = InnoDB;

ALTER TABLE documentos ADD FULLTEXT INDEX idx_documentos_nombre_busqueda (documentos_nombre);

-- -----------------------------------------------------
-- Table sesiones_usuarios
-- -----------------------------------------------------
CREATE TABLE sesiones_usuarios (
  sesion_id INT NOT NULL AUTO_INCREMENT,
  sesion_token VARCHAR(255) NOT NULL,
  sesion_fecha_inicio DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  sesion_fecha_cierre DATETIME NULL,
  sesion_ip VARCHAR(45) NULL,
  sesion_user_agent VARCHAR(500) NULL,
  sesion_estado SMALLINT(1) NOT NULL DEFAULT 1,
  usuarios_id_usuarios INT NOT NULL,
  PRIMARY KEY (sesion_id),
  UNIQUE INDEX sesion_token_UNIQUE (sesion_token),
  INDEX fk_sesiones_usuarios_idx (usuarios_id_usuarios),
  CONSTRAINT fk_sesiones_usuarios
    FOREIGN KEY (usuarios_id_usuarios)
    REFERENCES usuarios (id_usuarios)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table intentos_login
-- -----------------------------------------------------
CREATE TABLE intentos_login (
  id_intento_login INT NOT NULL AUTO_INCREMENT,
  usuario_nombre VARCHAR(45) NOT NULL,
  intento_exitoso SMALLINT(1) NOT NULL DEFAULT 0,
  intento_detalle VARCHAR(255) NULL,
  intento_ip VARCHAR(45) NULL,
  intento_fecha DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_intento_login),
  INDEX idx_usuario_fecha (usuario_nombre, intento_fecha)
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- TABLAS PARA SISTEMA DE COSTOS
-- -----------------------------------------------------
CREATE TABLE complejidad_opciones (
    id_complejidad INT PRIMARY KEY AUTO_INCREMENT,
    complejidad_nombre VARCHAR(50) NOT NULL,
    complejidad_descripcion TEXT,
    complejidad_factor DECIMAL(5,2) DEFAULT 1.00,
    complejidad_situacion SMALLINT(1) DEFAULT 1
) ENGINE = InnoDB;

CREATE TABLE seguridad_opciones (
    id_seguridad INT PRIMARY KEY AUTO_INCREMENT,
    seguridad_nombre VARCHAR(50) NOT NULL,
    seguridad_descripcion TEXT,
    seguridad_factor DECIMAL(5,2) DEFAULT 1.00,
    seguridad_situacion SMALLINT(1) DEFAULT 1
) ENGINE = InnoDB;

CREATE TABLE aplicacion_costos (
    id_aplicacion_costos INT PRIMARY KEY AUTO_INCREMENT,
    aplicacion_id_aplicacion INT NOT NULL,
    complejidad_id INT,
    seguridad_id INT,
    costos_horas_estimadas DECIMAL(8,2) NOT NULL,
    costos_tarifa_hora DECIMAL(10,2) DEFAULT 0,
    costos_total DECIMAL(12,2) DEFAULT 0,
    costos_moneda VARCHAR(10) DEFAULT 'GTQ',
    costos_notas TEXT,
    costos_situacion SMALLINT(1) DEFAULT 1,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion DATETIME NULL,
    creado_por INT,
    modificado_por INT,
    FOREIGN KEY (aplicacion_id_aplicacion) REFERENCES aplicacion(id_aplicacion),
    FOREIGN KEY (complejidad_id) REFERENCES complejidad_opciones(id_complejidad),
    FOREIGN KEY (seguridad_id) REFERENCES seguridad_opciones(id_seguridad),
    FOREIGN KEY (creado_por) REFERENCES usuarios(id_usuarios),
    FOREIGN KEY (modificado_por) REFERENCES usuarios(id_usuarios)
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- TABLAS PARA SISTEMA DE TABLAS
-- -----------------------------------------------------
CREATE TABLE tipos_tabla (
    id_tipo_tabla INT PRIMARY KEY AUTO_INCREMENT,
    tipos_tabla_nombre VARCHAR(50) NOT NULL,
    tipos_tabla_descripcion TEXT,
    tipos_tabla_situacion SMALLINT(1) DEFAULT 1
) ENGINE = InnoDB;

CREATE TABLE tipos_dato (
    id_tipo_dato INT PRIMARY KEY AUTO_INCREMENT,
    tipos_dato_nombre VARCHAR(50) NOT NULL,
    tipos_dato_descripcion TEXT,
    tipos_dato_situacion SMALLINT(1) DEFAULT 1
) ENGINE = InnoDB;

CREATE TABLE tipos_clave (
    id_tipo_clave INT PRIMARY KEY AUTO_INCREMENT,
    tipos_clave_nombre VARCHAR(50) NOT NULL,
    tipos_clave_descripcion TEXT,
    tipos_clave_situacion SMALLINT(1) DEFAULT 1
) ENGINE = InnoDB;

CREATE TABLE aplicacion_tablas (
    id_aplicacion_tablas INT PRIMARY KEY AUTO_INCREMENT,
    aplicacion_id_aplicacion INT NOT NULL,
    tablas_nombre VARCHAR(100) NOT NULL,
    tablas_descripcion TEXT,
    tipo_tabla_id INT,
    tablas_situacion SMALLINT(1) DEFAULT 1,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion DATETIME NULL,
    creado_por INT,
    modificado_por INT,
    FOREIGN KEY (aplicacion_id_aplicacion) REFERENCES aplicacion(id_aplicacion),
    FOREIGN KEY (tipo_tabla_id) REFERENCES tipos_tabla(id_tipo_tabla),
    FOREIGN KEY (creado_por) REFERENCES usuarios(id_usuarios),
    FOREIGN KEY (modificado_por) REFERENCES usuarios(id_usuarios) 
) ENGINE = InnoDB;

CREATE TABLE aplicacion_campos (
    id_aplicacion_campos INT PRIMARY KEY AUTO_INCREMENT,
    aplicacion_tablas_id INT NOT NULL,
    campos_nombre VARCHAR(100) NOT NULL,
    tipo_dato_id INT NOT NULL,
    campos_longitud INT,
    campos_nulo BOOLEAN DEFAULT false,
    tipo_clave_id INT,
    campos_descripcion TEXT,
    campos_situacion SMALLINT(1) DEFAULT 1,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion DATETIME NULL,
    creado_por INT,
    modificado_por INT,
    FOREIGN KEY (aplicacion_tablas_id) REFERENCES aplicacion_tablas(id_aplicacion_tablas),
    FOREIGN KEY (tipo_dato_id) REFERENCES tipos_dato(id_tipo_dato),
    FOREIGN KEY (tipo_clave_id) REFERENCES tipos_clave(id_tipo_clave),
    FOREIGN KEY (creado_por) REFERENCES usuarios(id_usuarios),
    FOREIGN KEY (modificado_por) REFERENCES usuarios(id_usuarios)
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- ÍNDICES
-- -----------------------------------------------------
CREATE INDEX idx_aplicacion_costos_app ON aplicacion_costos(aplicacion_id_aplicacion);
CREATE INDEX idx_aplicacion_tablas_app ON aplicacion_tablas(aplicacion_id_aplicacion);
CREATE INDEX idx_aplicacion_campos_tabla ON aplicacion_campos(aplicacion_tablas_id);

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

