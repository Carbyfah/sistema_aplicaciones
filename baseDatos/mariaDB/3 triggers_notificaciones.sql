-- TRIGGERS PARA SISTEMA DE AUDITORÍA COMPLETO
-- Fecha: 27 de octubre de 2025

-- ===========================================================
-- AUDITORÍA PARA TABLA: aplicacion
-- ===========================================================
DELIMITER //
CREATE TRIGGER trg_auditoria_aplicacion_insert
AFTER INSERT ON aplicacion
FOR EACH ROW
BEGIN
    INSERT INTO logs_actividad (
        logs_actividad_accion,
        logs_actividad_tabla,
        logs_actividad_registro_id,
        logs_actividad_datos_antiguos,
        logs_actividad_datos_nuevos,
        logs_actividad_ip,
        persona_id_persona
    )
    VALUES (
        'INSERT',
        'aplicacion',
        NEW.id_aplicacion,
        NULL,
        JSON_OBJECT(
            'nombre', NEW.aplicacion_nombre,
            'desc_corta', COALESCE(NEW.aplicacion_desc_corta, ''),
            'situacion', NEW.aplicacion_situacion
        ),
        NULL,
        (SELECT persona_id_persona FROM usuarios WHERE id_usuarios = NEW.creado_por)
    );
END //
DELIMITER ;

DELIMITER //
CREATE TRIGGER trg_auditoria_aplicacion_update
AFTER UPDATE ON aplicacion
FOR EACH ROW
BEGIN
    INSERT INTO logs_actividad (
        logs_actividad_accion,
        logs_actividad_tabla,
        logs_actividad_registro_id,
        logs_actividad_datos_antiguos,
        logs_actividad_datos_nuevos,
        logs_actividad_ip,
        persona_id_persona
    )
    VALUES (
        'UPDATE',
        'aplicacion',
        NEW.id_aplicacion,
        JSON_OBJECT(
            'nombre', OLD.aplicacion_nombre,
            'desc_corta', COALESCE(OLD.aplicacion_desc_corta, ''),
            'situacion', OLD.aplicacion_situacion
        ),
        JSON_OBJECT(
            'nombre', NEW.aplicacion_nombre,
            'desc_corta', COALESCE(NEW.aplicacion_desc_corta, ''),
            'situacion', NEW.aplicacion_situacion
        ),
        NULL,
        (SELECT persona_id_persona FROM usuarios WHERE id_usuarios = COALESCE(NEW.modificado_por, OLD.creado_por))
    );
END //
DELIMITER ;

-- ===========================================================
-- AUDITORÍA PARA TABLA: usuarios
-- ===========================================================
DELIMITER //
CREATE TRIGGER trg_auditoria_usuarios_insert
AFTER INSERT ON usuarios
FOR EACH ROW
BEGIN
    INSERT INTO logs_actividad (
        logs_actividad_accion,
        logs_actividad_tabla,
        logs_actividad_registro_id,
        logs_actividad_datos_antiguos,
        logs_actividad_datos_nuevos,
        logs_actividad_ip,
        persona_id_persona
    )
    VALUES (
        'INSERT',
        'usuarios',
        NEW.id_usuarios,
        NULL,
        JSON_OBJECT(
            'nombre', NEW.usuarios_nombre,
            'situacion', NEW.usuarios_situacion,
            'persona_id', NEW.persona_id_persona
        ),
        NULL,
        NEW.persona_id_persona
    );
END //
DELIMITER ;

DELIMITER //
CREATE TRIGGER trg_auditoria_usuarios_update
AFTER UPDATE ON usuarios
FOR EACH ROW
BEGIN
    INSERT INTO logs_actividad (
        logs_actividad_accion,
        logs_actividad_tabla,
        logs_actividad_registro_id,
        logs_actividad_datos_antiguos,
        logs_actividad_datos_nuevos,
        logs_actividad_ip,
        persona_id_persona
    )
    VALUES (
        'UPDATE',
        'usuarios',
        NEW.id_usuarios,
        JSON_OBJECT(
            'nombre', OLD.usuarios_nombre,
            'situacion', OLD.usuarios_situacion
        ),
        JSON_OBJECT(
            'nombre', NEW.usuarios_nombre,
            'situacion', NEW.usuarios_situacion
        ),
        NULL,
        NEW.persona_id_persona
    );
END //
DELIMITER ;

-- ===========================================================
-- AUDITORÍA PARA TABLA: persona
-- ===========================================================
DELIMITER //
CREATE TRIGGER trg_auditoria_persona_insert
AFTER INSERT ON persona
FOR EACH ROW
BEGIN
    INSERT INTO logs_actividad (
        logs_actividad_accion,
        logs_actividad_tabla,
        logs_actividad_registro_id,
        logs_actividad_datos_antiguos,
        logs_actividad_datos_nuevos,
        logs_actividad_ip,
        persona_id_persona
    )
    VALUES (
        'INSERT',
        'persona',
        NEW.id_persona,
        NULL,
        JSON_OBJECT(
            'nombres', NEW.persona_nombres,
            'apellidos', NEW.persona_apellidos,
            'identidad', NEW.persona_identidad,
            'correo', COALESCE(NEW.persona_correo, ''),
            'rol_id', NEW.roles_persona_id_roles_persona
        ),
        NULL,
        NEW.id_persona
    );
END //
DELIMITER ;

DELIMITER //
CREATE TRIGGER trg_auditoria_persona_update
AFTER UPDATE ON persona
FOR EACH ROW
BEGIN
    INSERT INTO logs_actividad (
        logs_actividad_accion,
        logs_actividad_tabla,
        logs_actividad_registro_id,
        logs_actividad_datos_antiguos,
        logs_actividad_datos_nuevos,
        logs_actividad_ip,
        persona_id_persona
    )
    VALUES (
        'UPDATE',
        'persona',
        NEW.id_persona,
        JSON_OBJECT(
            'nombres', OLD.persona_nombres,
            'apellidos', OLD.persona_apellidos,
            'identidad', OLD.persona_identidad,
            'correo', COALESCE(OLD.persona_correo, ''),
            'rol_id', OLD.roles_persona_id_roles_persona
        ),
        JSON_OBJECT(
            'nombres', NEW.persona_nombres,
            'apellidos', NEW.persona_apellidos,
            'identidad', NEW.persona_identidad,
            'correo', COALESCE(NEW.persona_correo, ''),
            'rol_id', NEW.roles_persona_id_roles_persona
        ),
        NULL,
        NEW.id_persona
    );
END //
DELIMITER ;

-- ===========================================================
-- AUDITORÍA PARA TABLA: ordenes_aplicaciones
-- ===========================================================
DELIMITER //
CREATE TRIGGER trg_auditoria_ordenes_insert
AFTER INSERT ON ordenes_aplicaciones
FOR EACH ROW
BEGIN
    INSERT INTO logs_actividad (
        logs_actividad_accion,
        logs_actividad_tabla,
        logs_actividad_registro_id,
        logs_actividad_datos_antiguos,
        logs_actividad_datos_nuevos,
        logs_actividad_ip,
        persona_id_persona
    )
    VALUES (
        'INSERT',
        'ordenes_aplicaciones',
        NEW.id_ordenes_aplicaciones,
        NULL,
        JSON_OBJECT(
            'codigo', NEW.ordenes_aplicaciones_codigo,
            'aplicacion_id', NEW.aplicacion_id_aplicacion,
            'estado_id', NEW.estados_id_estados,
            'usuario_id', NEW.usuarios_id_usuarios,
            'fecha_entrega', NEW.ordenes_aplicaciones_fecha_entrega
        ),
        NULL,
        (SELECT persona_id_persona FROM usuarios WHERE id_usuarios = NEW.usuarios_id_usuarios)
    );
END //
DELIMITER ;

DELIMITER //
CREATE TRIGGER trg_auditoria_ordenes_update
AFTER UPDATE ON ordenes_aplicaciones
FOR EACH ROW
BEGIN
    IF OLD.estados_id_estados != NEW.estados_id_estados THEN
        INSERT INTO logs_actividad (
            logs_actividad_accion,
            logs_actividad_tabla,
            logs_actividad_registro_id,
            logs_actividad_datos_antiguos,
            logs_actividad_datos_nuevos,
            logs_actividad_ip,
            persona_id_persona
        )
        VALUES (
            'CAMBIO_ESTADO',
            'ordenes_aplicaciones',
            NEW.id_ordenes_aplicaciones,
            JSON_OBJECT('estado_id', OLD.estados_id_estados),
            JSON_OBJECT('estado_id', NEW.estados_id_estados),
            NULL,
            (SELECT persona_id_persona FROM usuarios WHERE id_usuarios = NEW.usuarios_id_usuarios)
        );
    END IF;
    
    IF OLD.usuarios_id_usuarios != NEW.usuarios_id_usuarios THEN
        INSERT INTO logs_actividad (
            logs_actividad_accion,
            logs_actividad_tabla,
            logs_actividad_registro_id,
            logs_actividad_datos_antiguos,
            logs_actividad_datos_nuevos,
            logs_actividad_ip,
            persona_id_persona
        )
        VALUES (
            'REASIGNACION',
            'ordenes_aplicaciones',
            NEW.id_ordenes_aplicaciones,
            JSON_OBJECT('usuario_id', OLD.usuarios_id_usuarios),
            JSON_OBJECT('usuario_id', NEW.usuarios_id_usuarios),
            NULL,
            (SELECT persona_id_persona FROM usuarios WHERE id_usuarios = NEW.usuarios_id_usuarios)
        );
    END IF;
END //
DELIMITER ;

-- ===========================================================
-- AUDITORÍA PARA TABLA: documentos
-- ===========================================================
DELIMITER //
CREATE TRIGGER trg_auditoria_documentos_insert
AFTER INSERT ON documentos
FOR EACH ROW
BEGIN
    INSERT INTO logs_actividad (
        logs_actividad_accion,
        logs_actividad_tabla,
        logs_actividad_registro_id,
        logs_actividad_datos_antiguos,
        logs_actividad_datos_nuevos,
        logs_actividad_ip,
        persona_id_persona
    )
    VALUES (
        'INSERT',
        'documentos',
        NEW.id_documentos,
        NULL,
        JSON_OBJECT(
            'nombre', NEW.documentos_nombre,
            'orden_id', NEW.ordenes_aplicaciones_id_ordenes_aplicaciones,
            'version', NEW.documentos_version,
            'categoria_id', NEW.categorias_documentos_id_categorias_documentos
        ),
        NULL,
        (SELECT persona_id_persona FROM usuarios WHERE id_usuarios = NEW.usuarios_id_usuarios)
    );
END //
DELIMITER ;

DELIMITER //
CREATE TRIGGER trg_auditoria_documentos_update
AFTER UPDATE ON documentos
FOR EACH ROW
BEGIN
    INSERT INTO logs_actividad (
        logs_actividad_accion,
        logs_actividad_tabla,
        logs_actividad_registro_id,
        logs_actividad_datos_antiguos,
        logs_actividad_datos_nuevos,
        logs_actividad_ip,
        persona_id_persona
    )
    VALUES (
        'UPDATE',
        'documentos',
        NEW.id_documentos,
        JSON_OBJECT(
            'nombre', OLD.documentos_nombre,
            'version', OLD.documentos_version,
            'situacion', OLD.documentos_situacion
        ),
        JSON_OBJECT(
            'nombre', NEW.documentos_nombre,
            'version', NEW.documentos_version,
            'situacion', NEW.documentos_situacion
        ),
        NULL,
        (SELECT persona_id_persona FROM usuarios WHERE id_usuarios = NEW.usuarios_id_usuarios)
    );
END //
DELIMITER ;

-- ===========================================================
-- AUDITORÍA PARA TABLA: personal_proyecto
-- ===========================================================
DELIMITER //
CREATE TRIGGER trg_auditoria_personal_insert
AFTER INSERT ON personal_proyecto
FOR EACH ROW
BEGIN
    INSERT INTO logs_actividad (
        logs_actividad_accion,
        logs_actividad_tabla,
        logs_actividad_registro_id,
        logs_actividad_datos_antiguos,
        logs_actividad_datos_nuevos,
        logs_actividad_ip,
        persona_id_persona
    )
    VALUES (
        'ASIGNACION_PERSONAL',
        'personal_proyecto',
        NEW.id_personal_proyecto,
        NULL,
        JSON_OBJECT(
            'persona_id', NEW.persona_id_persona,
            'orden_id', NEW.ordenes_aplicaciones_id_ordenes_aplicaciones,
            'rol', COALESCE(NEW.personal_proyecto_rol, '')
        ),
        NULL,
        NEW.persona_id_persona
    );
END //
DELIMITER ;

DELIMITER //
CREATE TRIGGER trg_auditoria_personal_delete
AFTER UPDATE ON personal_proyecto
FOR EACH ROW
BEGIN
    IF OLD.personal_proyecto_situacion = 1 AND NEW.personal_proyecto_situacion = 0 THEN
        INSERT INTO logs_actividad (
            logs_actividad_accion,
            logs_actividad_tabla,
            logs_actividad_registro_id,
            logs_actividad_datos_antiguos,
            logs_actividad_datos_nuevos,
            logs_actividad_ip,
            persona_id_persona
        )
        VALUES (
            'REMOCION_PERSONAL',
            'personal_proyecto',
            NEW.id_personal_proyecto,
            JSON_OBJECT('situacion', 1),
            JSON_OBJECT('situacion', 0),
            NULL,
            NEW.persona_id_persona
        );
    END IF;
END //
DELIMITER ;

-- ===========================================================
-- AUDITORÍA PARA TABLA: aplicacion_costos
-- ===========================================================
DELIMITER //
CREATE TRIGGER trg_auditoria_costos_insert
AFTER INSERT ON aplicacion_costos
FOR EACH ROW
BEGIN
    INSERT INTO logs_actividad (
        logs_actividad_accion,
        logs_actividad_tabla,
        logs_actividad_registro_id,
        logs_actividad_datos_antiguos,
        logs_actividad_datos_nuevos,
        logs_actividad_ip,
        persona_id_persona
    )
    VALUES (
        'INSERT',
        'aplicacion_costos',
        NEW.id_aplicacion_costos,
        NULL,
        JSON_OBJECT(
            'aplicacion_id', NEW.aplicacion_id_aplicacion,
            'horas_estimadas', NEW.costos_horas_estimadas,
            'tarifa_hora', NEW.costos_tarifa_hora,
            'total', NEW.costos_total,
            'complejidad_id', NEW.complejidad_id,
            'seguridad_id', NEW.seguridad_id
        ),
        NULL,
        (SELECT persona_id_persona FROM usuarios WHERE id_usuarios = NEW.creado_por)
    );
END //
DELIMITER ;

DELIMITER //
CREATE TRIGGER trg_auditoria_costos_update
AFTER UPDATE ON aplicacion_costos
FOR EACH ROW
BEGIN
    IF OLD.costos_total != NEW.costos_total THEN
        INSERT INTO logs_actividad (
            logs_actividad_accion,
            logs_actividad_tabla,
            logs_actividad_registro_id,
            logs_actividad_datos_antiguos,
            logs_actividad_datos_nuevos,
            logs_actividad_ip,
            persona_id_persona
        )
        VALUES (
            'ACTUALIZACION_COSTO',
            'aplicacion_costos',
            NEW.id_aplicacion_costos,
            JSON_OBJECT(
                'horas', OLD.costos_horas_estimadas,
                'tarifa', OLD.costos_tarifa_hora,
                'total', OLD.costos_total
            ),
            JSON_OBJECT(
                'horas', NEW.costos_horas_estimadas,
                'tarifa', NEW.costos_tarifa_hora,
                'total', NEW.costos_total
            ),
            NULL,
            (SELECT persona_id_persona FROM usuarios WHERE id_usuarios = COALESCE(NEW.modificado_por, OLD.creado_por))
        );
    END IF;
END //
DELIMITER ;

-- ===========================================================
-- AUDITORÍA PARA ELIMINACIONES LÓGICAS (GENÉRICO)
-- ===========================================================
DELIMITER //
CREATE TRIGGER trg_auditoria_eliminacion_logica_aplicacion
AFTER UPDATE ON aplicacion
FOR EACH ROW
BEGIN
    IF OLD.aplicacion_situacion = 1 AND NEW.aplicacion_situacion = 0 THEN
        INSERT INTO logs_actividad (
            logs_actividad_accion,
            logs_actividad_tabla,
            logs_actividad_registro_id,
            logs_actividad_datos_antiguos,
            logs_actividad_datos_nuevos,
            logs_actividad_ip,
            persona_id_persona
        )
        VALUES (
            'DELETE_LOGICO',
            'aplicacion',
            NEW.id_aplicacion,
            JSON_OBJECT('situacion', 1),
            JSON_OBJECT('situacion', 0),
            NULL,
            (SELECT persona_id_persona FROM usuarios WHERE id_usuarios = NEW.modificado_por)
        );
    END IF;
END //
DELIMITER ;

DELIMITER //
CREATE TRIGGER trg_auditoria_eliminacion_logica_usuarios
AFTER UPDATE ON usuarios
FOR EACH ROW
BEGIN
    IF OLD.usuarios_situacion = 1 AND NEW.usuarios_situacion = 0 THEN
        INSERT INTO logs_actividad (
            logs_actividad_accion,
            logs_actividad_tabla,
            logs_actividad_registro_id,
            logs_actividad_datos_antiguos,
            logs_actividad_datos_nuevos,
            logs_actividad_ip,
            persona_id_persona
        )
        VALUES (
            'DELETE_LOGICO',
            'usuarios',
            NEW.id_usuarios,
            JSON_OBJECT('situacion', 1),
            JSON_OBJECT('situacion', 0),
            NULL,
            NEW.persona_id_persona
        );
    END IF;
END //
DELIMITER ;

-- ===========================================================
-- AUDITORÍA PARA TABLA: tareas_aplicaciones
-- ===========================================================
DELIMITER //
CREATE TRIGGER trg_auditoria_tareas_insert
AFTER INSERT ON tareas_aplicaciones
FOR EACH ROW
BEGIN
    INSERT INTO logs_actividad (
        logs_actividad_accion,
        logs_actividad_tabla,
        logs_actividad_registro_id,
        logs_actividad_datos_antiguos,
        logs_actividad_datos_nuevos,
        logs_actividad_ip,
        persona_id_persona
    )
    VALUES (
        'INSERT',
        'tareas_aplicaciones',
        NEW.id_tareas_aplicaciones,
        NULL,
        JSON_OBJECT(
            'titulo', NEW.tareas_aplicaciones_titulo,
            'orden_id', NEW.ordenes_aplicaciones_id_ordenes_aplicaciones,
            'usuario_id', NEW.usuarios_id_usuarios,
            'prioridad', NEW.tareas_aplicaciones_prioridad,
            'fecha_limite', COALESCE(NEW.tareas_aplicaciones_fecha_limite, '')
        ),
        NULL,
        (SELECT persona_id_persona FROM usuarios WHERE id_usuarios = NEW.usuarios_id_usuarios)
    );
END //
DELIMITER ;

DELIMITER //
CREATE TRIGGER trg_auditoria_tareas_completadas
AFTER UPDATE ON tareas_aplicaciones
FOR EACH ROW
BEGIN
    IF OLD.tareas_aplicaciones_completada = 0 AND NEW.tareas_aplicaciones_completada = 1 THEN
        INSERT INTO logs_actividad (
            logs_actividad_accion,
            logs_actividad_tabla,
            logs_actividad_registro_id,
            logs_actividad_datos_antiguos,
            logs_actividad_datos_nuevos,
            logs_actividad_ip,
            persona_id_persona
        )
        VALUES (
            'TAREA_COMPLETADA',
            'tareas_aplicaciones',
            NEW.id_tareas_aplicaciones,
            JSON_OBJECT('completada', 0),
            JSON_OBJECT('completada', 1),
            NULL,
            (SELECT persona_id_persona FROM usuarios WHERE id_usuarios = NEW.usuarios_id_usuarios)
        );
    END IF;
END //
DELIMITER ;


-- TRIGGERS PARA SISTEMA DE NOTIFICACIONES Y AUDITORÍA RELACIONADA
-- Versión corregida para MySQL/MariaDB

-- Notificar cuando se asigna un proyecto a un programador responsable
DELIMITER //
CREATE TRIGGER trg_notif_asignacion_programador
AFTER UPDATE ON ordenes_aplicaciones
FOR EACH ROW
BEGIN
    IF OLD.usuarios_id_usuarios != NEW.usuarios_id_usuarios THEN
        CALL sp_notif_asignacion_proyecto(
            NEW.id_ordenes_aplicaciones, 
            NEW.usuarios_id_usuarios, 
            (SELECT aplicacion_nombre FROM aplicacion WHERE id_aplicacion = NEW.aplicacion_id_aplicacion)
        );
        
        -- AUDITORÍA ADICIONAL
        INSERT INTO logs_actividad (
            logs_actividad_accion,
            logs_actividad_tabla,
            logs_actividad_registro_id,
            logs_actividad_datos_antiguos,
            logs_actividad_datos_nuevos,
            logs_actividad_ip,
            persona_id_persona
        )
        VALUES (
            'REASIGNACION_PROYECTO',
            'ordenes_aplicaciones',
            NEW.id_ordenes_aplicaciones,
            JSON_OBJECT('usuario_anterior_id', OLD.usuarios_id_usuarios),
            JSON_OBJECT('usuario_nuevo_id', NEW.usuarios_id_usuarios),
            NULL,
            (SELECT persona_id_persona FROM usuarios WHERE id_usuarios = NEW.usuarios_id_usuarios)
        );
    END IF;
END //
DELIMITER ;

-- Notificar cuando se sube un nuevo documento
DELIMITER //
CREATE TRIGGER trg_notif_documento_nuevo
AFTER INSERT ON documentos
FOR EACH ROW
BEGIN
    CALL sp_notif_documento_nuevo(
        NEW.id_documentos, 
        NEW.documentos_nombre, 
        NEW.ordenes_aplicaciones_id_ordenes_aplicaciones, 
        NEW.usuarios_id_usuarios
    );
END //
DELIMITER ;

-- Notificar cuando un proyecto cambia a estado completado
DELIMITER //
CREATE TRIGGER trg_notif_proyecto_completado
AFTER UPDATE ON ordenes_aplicaciones
FOR EACH ROW
BEGIN
    DECLARE v_estado_completado INT;
    
    SELECT id_estados INTO v_estado_completado
    FROM estados 
    WHERE estados_nombre = 'Completado'
    LIMIT 1;
    
    IF NEW.estados_id_estados = v_estado_completado 
       AND OLD.estados_id_estados != v_estado_completado THEN
        CALL sp_notif_proyecto_completado(
            NEW.id_ordenes_aplicaciones,
            (SELECT aplicacion_nombre FROM aplicacion WHERE id_aplicacion = NEW.aplicacion_id_aplicacion)
        );
        
        -- AUDITORÍA ADICIONAL
        INSERT INTO logs_actividad (
            logs_actividad_accion,
            logs_actividad_tabla,
            logs_actividad_registro_id,
            logs_actividad_datos_antiguos,
            logs_actividad_datos_nuevos,
            logs_actividad_ip,
            persona_id_persona
        )
        VALUES (
            'PROYECTO_COMPLETADO',
            'ordenes_aplicaciones',
            NEW.id_ordenes_aplicaciones,
            JSON_OBJECT('estado_anterior_id', OLD.estados_id_estados),
            JSON_OBJECT('estado_nuevo_id', NEW.estados_id_estados),
            NULL,
            (SELECT persona_id_persona FROM usuarios WHERE id_usuarios = NEW.usuarios_id_usuarios)
        );
    END IF;
END //
DELIMITER ;

-- Notificar cuando un miembro es añadido a un proyecto
DELIMITER //
CREATE TRIGGER trg_notif_personal_asignado
AFTER INSERT ON personal_proyecto
FOR EACH ROW
BEGIN
    CALL sp_notif_personal_asignado(
        NEW.persona_id_persona, 
        NEW.ordenes_aplicaciones_id_ordenes_aplicaciones
    );
END //
DELIMITER ;

-- AUDITORÍA ADICIONAL PARA CAMBIOS DE ESTADO CRÍTICOS
DELIMITER //
CREATE TRIGGER trg_auditoria_estados_criticos
AFTER UPDATE ON ordenes_aplicaciones
FOR EACH ROW
BEGIN
    DECLARE v_estado_cancelado INT;
    
    SELECT id_estados INTO v_estado_cancelado
    FROM estados 
    WHERE estados_nombre = 'Cancelado'
    LIMIT 1;
    
    -- Auditoría para proyecto cancelado
    IF NEW.estados_id_estados = v_estado_cancelado 
       AND OLD.estados_id_estados != v_estado_cancelado THEN
        INSERT INTO logs_actividad (
            logs_actividad_accion,
            logs_actividad_tabla,
            logs_actividad_registro_id,
            logs_actividad_datos_antiguos,
            logs_actividad_datos_nuevos,
            logs_actividad_ip,
            persona_id_persona
        )
        VALUES (
            'PROYECTO_CANCELADO',
            'ordenes_aplicaciones',
            NEW.id_ordenes_aplicaciones,
            JSON_OBJECT('estado_anterior_id', OLD.estados_id_estados),
            JSON_OBJECT('estado_nuevo_id', NEW.estados_id_estados),
            NULL,
            (SELECT persona_id_persona FROM usuarios WHERE id_usuarios = NEW.usuarios_id_usuarios)
        );
    END IF;
END //
DELIMITER ;