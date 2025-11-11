-- TRIGGERS PARA SISTEMA DE AUDITORÍA EN ORACLE
-- Fecha: 07 de noviembre de 2025

-- ===========================================================
-- AUDITORÍA PARA TABLA: aplicacion
-- ===========================================================
CREATE OR REPLACE TRIGGER trg_auditoria_aplicacion_insert
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
        :NEW.id_aplicacion,
        NULL,
        '{"nombre":"' || :NEW.aplicacion_nombre || '","desc_corta":"' || NVL(:NEW.aplicacion_desc_corta, '') || '","situacion":' || :NEW.aplicacion_situacion || '}',
        NULL,
        (SELECT persona_id_persona FROM usuarios WHERE id_usuarios = :NEW.creado_por)
    );
END;
/

CREATE OR REPLACE TRIGGER trg_auditoria_aplicacion_update
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
        :NEW.id_aplicacion,
        '{"nombre":"' || :OLD.aplicacion_nombre || '","desc_corta":"' || NVL(:OLD.aplicacion_desc_corta, '') || '","situacion":' || :OLD.aplicacion_situacion || '}',
        '{"nombre":"' || :NEW.aplicacion_nombre || '","desc_corta":"' || NVL(:NEW.aplicacion_desc_corta, '') || '","situacion":' || :NEW.aplicacion_situacion || '}',
        NULL,
        (SELECT persona_id_persona FROM usuarios WHERE id_usuarios = NVL(:NEW.modificado_por, :OLD.creado_por))
    );
END;
/

-- ===========================================================
-- AUDITORÍA PARA TABLA: usuarios
-- ===========================================================
CREATE OR REPLACE TRIGGER trg_auditoria_usuarios_insert
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
        :NEW.id_usuarios,
        NULL,
        '{"nombre":"' || :NEW.usuarios_nombre || '","situacion":' || :NEW.usuarios_situacion || ',"persona_id":' || :NEW.persona_id_persona || '}',
        NULL,
        :NEW.persona_id_persona
    );
END;
/

CREATE OR REPLACE TRIGGER trg_auditoria_usuarios_update
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
        :NEW.id_usuarios,
        '{"nombre":"' || :OLD.usuarios_nombre || '","situacion":' || :OLD.usuarios_situacion || '}',
        '{"nombre":"' || :NEW.usuarios_nombre || '","situacion":' || :NEW.usuarios_situacion || '}',
        NULL,
        :NEW.persona_id_persona
    );
END;
/

-- ===========================================================
-- AUDITORÍA PARA TABLA: persona
-- ===========================================================
CREATE OR REPLACE TRIGGER trg_auditoria_persona_insert
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
        :NEW.id_persona,
        NULL,
        '{"nombres":"' || :NEW.persona_nombres || '","apellidos":"' || :NEW.persona_apellidos || '","identidad":"' || :NEW.persona_identidad || '","correo":"' || NVL(:NEW.persona_correo, '') || '","rol_id":' || NVL(TO_CHAR(:NEW.roles_persona_id_roles_persona), 'null') || '}',
        NULL,
        :NEW.id_persona
    );
END;
/

CREATE OR REPLACE TRIGGER trg_auditoria_persona_update
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
        :NEW.id_persona,
        '{"nombres":"' || :OLD.persona_nombres || '","apellidos":"' || :OLD.persona_apellidos || '","identidad":"' || :OLD.persona_identidad || '","correo":"' || NVL(:OLD.persona_correo, '') || '","rol_id":' || NVL(TO_CHAR(:OLD.roles_persona_id_roles_persona), 'null') || '}',
        '{"nombres":"' || :NEW.persona_nombres || '","apellidos":"' || :NEW.persona_apellidos || '","identidad":"' || :NEW.persona_identidad || '","correo":"' || NVL(:NEW.persona_correo, '') || '","rol_id":' || NVL(TO_CHAR(:NEW.roles_persona_id_roles_persona), 'null') || '}',
        NULL,
        :NEW.id_persona
    );
END;
/

-- ===========================================================
-- AUDITORÍA PARA TABLA: ordenes_aplicaciones
-- ===========================================================
CREATE OR REPLACE TRIGGER trg_auditoria_ordenes_insert
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
        :NEW.id_ordenes_aplicaciones,
        NULL,
        '{"codigo":"' || :NEW.ordenes_aplicaciones_codigo || '","aplicacion_id":' || :NEW.aplicacion_id_aplicacion || ',"estado_id":' || :NEW.estados_id_estados || ',"usuario_id":' || :NEW.usuarios_id_usuarios || ',"fecha_entrega":"' || TO_CHAR(:NEW.ordenes_aplicaciones_fecha_entrega, 'YYYY-MM-DD') || '"}',
        NULL,
        (SELECT persona_id_persona FROM usuarios WHERE id_usuarios = :NEW.usuarios_id_usuarios)
    );
END;
/

CREATE OR REPLACE TRIGGER trg_auditoria_ordenes_update
AFTER UPDATE ON ordenes_aplicaciones
FOR EACH ROW
BEGIN
    IF :OLD.estados_id_estados != :NEW.estados_id_estados THEN
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
            :NEW.id_ordenes_aplicaciones,
            '{"estado_id":' || :OLD.estados_id_estados || '}',
            '{"estado_id":' || :NEW.estados_id_estados || '}',
            NULL,
            (SELECT persona_id_persona FROM usuarios WHERE id_usuarios = :NEW.usuarios_id_usuarios)
        );
    END IF;
    
    IF :OLD.usuarios_id_usuarios != :NEW.usuarios_id_usuarios THEN
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
            :NEW.id_ordenes_aplicaciones,
            '{"usuario_id":' || :OLD.usuarios_id_usuarios || '}',
            '{"usuario_id":' || :NEW.usuarios_id_usuarios || '}',
            NULL,
            (SELECT persona_id_persona FROM usuarios WHERE id_usuarios = :NEW.usuarios_id_usuarios)
        );
    END IF;
END;
/

-- ===========================================================
-- AUDITORÍA PARA TABLA: documentos
-- ===========================================================
CREATE OR REPLACE TRIGGER trg_auditoria_documentos_insert
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
        :NEW.id_documentos,
        NULL,
        '{"nombre":"' || :NEW.documentos_nombre || '","orden_id":' || :NEW.ordenes_aplicaciones_id_ordenes_aplicaciones || ',"version":' || :NEW.documentos_version || ',"categoria_id":' || :NEW.categorias_documentos_id_categorias_documentos || '}',
        NULL,
        (SELECT persona_id_persona FROM usuarios WHERE id_usuarios = :NEW.usuarios_id_usuarios)
    );
END;
/

CREATE OR REPLACE TRIGGER trg_auditoria_documentos_update
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
        :NEW.id_documentos,
        '{"nombre":"' || :OLD.documentos_nombre || '","version":' || :OLD.documentos_version || ',"situacion":' || :OLD.documentos_situacion || '}',
        '{"nombre":"' || :NEW.documentos_nombre || '","version":' || :NEW.documentos_version || ',"situacion":' || :NEW.documentos_situacion || '}',
        NULL,
        (SELECT persona_id_persona FROM usuarios WHERE id_usuarios = :NEW.usuarios_id_usuarios)
    );
END;
/

-- ===========================================================
-- AUDITORÍA PARA TABLA: personal_proyecto
-- ===========================================================
CREATE OR REPLACE TRIGGER trg_auditoria_personal_insert
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
        :NEW.id_personal_proyecto,
        NULL,
        '{"persona_id":' || :NEW.persona_id_persona || ',"orden_id":' || :NEW.ordenes_aplicaciones_id_ordenes_aplicaciones || ',"rol":"' || NVL(:NEW.personal_proyecto_rol, '') || '"}',
        NULL,
        :NEW.persona_id_persona
    );
END;
/

CREATE OR REPLACE TRIGGER trg_auditoria_personal_delete
AFTER UPDATE ON personal_proyecto
FOR EACH ROW
BEGIN
    IF :OLD.personal_proyecto_situacion = 1 AND :NEW.personal_proyecto_situacion = 0 THEN
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
            :NEW.id_personal_proyecto,
            '{"situacion":1}',
            '{"situacion":0}',
            NULL,
            :NEW.persona_id_persona
        );
    END IF;
END;
/

-- ===========================================================
-- AUDITORÍA PARA TABLA: aplicacion_costos
-- ===========================================================
CREATE OR REPLACE TRIGGER trg_auditoria_costos_insert
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
        :NEW.id_aplicacion_costos,
        NULL,
        '{"aplicacion_id":' || :NEW.aplicacion_id_aplicacion || ',"horas_estimadas":' || :NEW.costos_horas_estimadas || ',"tarifa_hora":' || :NEW.costos_tarifa_hora || ',"total":' || :NEW.costos_total || ',"complejidad_id":' || NVL(TO_CHAR(:NEW.complejidad_id), 'null') || ',"seguridad_id":' || NVL(TO_CHAR(:NEW.seguridad_id), 'null') || '}',
        NULL,
        (SELECT persona_id_persona FROM usuarios WHERE id_usuarios = :NEW.creado_por)
    );
END;
/

CREATE OR REPLACE TRIGGER trg_auditoria_costos_update
AFTER UPDATE ON aplicacion_costos
FOR EACH ROW
BEGIN
    IF :OLD.costos_total != :NEW.costos_total THEN
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
            :NEW.id_aplicacion_costos,
            '{"horas":' || :OLD.costos_horas_estimadas || ',"tarifa":' || :OLD.costos_tarifa_hora || ',"total":' || :OLD.costos_total || '}',
            '{"horas":' || :NEW.costos_horas_estimadas || ',"tarifa":' || :NEW.costos_tarifa_hora || ',"total":' || :NEW.costos_total || '}',
            NULL,
            (SELECT persona_id_persona FROM usuarios WHERE id_usuarios = NVL(:NEW.modificado_por, :OLD.creado_por))
        );
    END IF;
END;
/

-- ===========================================================
-- AUDITORÍA PARA ELIMINACIONES LÓGICAS
-- ===========================================================
CREATE OR REPLACE TRIGGER trg_auditoria_eliminacion_logica_aplicacion
AFTER UPDATE ON aplicacion
FOR EACH ROW
BEGIN
    IF :OLD.aplicacion_situacion = 1 AND :NEW.aplicacion_situacion = 0 THEN
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
            :NEW.id_aplicacion,
            '{"situacion":1}',
            '{"situacion":0}',
            NULL,
            (SELECT persona_id_persona FROM usuarios WHERE id_usuarios = :NEW.modificado_por)
        );
    END IF;
END;
/

CREATE OR REPLACE TRIGGER trg_auditoria_eliminacion_logica_usuarios
AFTER UPDATE ON usuarios
FOR EACH ROW
BEGIN
    IF :OLD.usuarios_situacion = 1 AND :NEW.usuarios_situacion = 0 THEN
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
            :NEW.id_usuarios,
            '{"situacion":1}',
            '{"situacion":0}',
            NULL,
            :NEW.persona_id_persona
        );
    END IF;
END;
/

-- ===========================================================
-- AUDITORÍA PARA TABLA: tareas_aplicaciones
-- ===========================================================
CREATE OR REPLACE TRIGGER trg_auditoria_tareas_insert
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
        :NEW.id_tareas_aplicaciones,
        NULL,
        '{"titulo":"' || :NEW.tareas_aplicaciones_titulo || '","orden_id":' || :NEW.ordenes_aplicaciones_id_ordenes_aplicaciones || ',"usuario_id":' || :NEW.usuarios_id_usuarios || ',"prioridad":"' || :NEW.tareas_aplicaciones_prioridad || '","fecha_limite":"' || NVL(TO_CHAR(:NEW.tareas_aplicaciones_fecha_limite, 'YYYY-MM-DD'), '') || '"}',
        NULL,
        (SELECT persona_id_persona FROM usuarios WHERE id_usuarios = :NEW.usuarios_id_usuarios)
    );
END;
/

CREATE OR REPLACE TRIGGER trg_auditoria_tareas_completadas
AFTER UPDATE ON tareas_aplicaciones
FOR EACH ROW
BEGIN
    IF :OLD.tareas_aplicaciones_completada = 0 AND :NEW.tareas_aplicaciones_completada = 1 THEN
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
            :NEW.id_tareas_aplicaciones,
            '{"completada":0}',
            '{"completada":1}',
            NULL,
            (SELECT persona_id_persona FROM usuarios WHERE id_usuarios = :NEW.usuarios_id_usuarios)
        );
    END IF;
END;
/

-- ===========================================================
-- TRIGGERS PARA NOTIFICACIONES
-- ===========================================================
CREATE OR REPLACE TRIGGER trg_notif_asignacion_programador
AFTER UPDATE ON ordenes_aplicaciones
FOR EACH ROW
DECLARE
    v_nombre_aplicacion VARCHAR2(100);
BEGIN
    IF :OLD.usuarios_id_usuarios != :NEW.usuarios_id_usuarios THEN
        SELECT aplicacion_nombre INTO v_nombre_aplicacion 
        FROM aplicacion 
        WHERE id_aplicacion = :NEW.aplicacion_id_aplicacion;
        
        sp_notif_asignacion_proyecto(
            :NEW.id_ordenes_aplicaciones, 
            :NEW.usuarios_id_usuarios, 
            v_nombre_aplicacion
        );
        
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
            :NEW.id_ordenes_aplicaciones,
            '{"usuario_anterior_id":' || :OLD.usuarios_id_usuarios || '}',
            '{"usuario_nuevo_id":' || :NEW.usuarios_id_usuarios || '}',
            NULL,
            (SELECT persona_id_persona FROM usuarios WHERE id_usuarios = :NEW.usuarios_id_usuarios)
        );
    END IF;
END;
/

CREATE OR REPLACE TRIGGER trg_notif_documento_nuevo
AFTER INSERT ON documentos
FOR EACH ROW
BEGIN
    sp_notif_documento_nuevo(
        :NEW.id_documentos, 
        :NEW.documentos_nombre, 
        :NEW.ordenes_aplicaciones_id_ordenes_aplicaciones, 
        :NEW.usuarios_id_usuarios
    );
END;
/

CREATE OR REPLACE TRIGGER trg_notif_proyecto_completado
AFTER UPDATE ON ordenes_aplicaciones
FOR EACH ROW
DECLARE
    v_estado_completado NUMBER;
    v_nombre_aplicacion VARCHAR2(100);
BEGIN
    SELECT id_estados INTO v_estado_completado
    FROM estados 
    WHERE estados_nombre = 'Completado'
    AND ROWNUM = 1;
    
    IF :NEW.estados_id_estados = v_estado_completado 
       AND :OLD.estados_id_estados != v_estado_completado THEN
        
        SELECT aplicacion_nombre INTO v_nombre_aplicacion 
        FROM aplicacion 
        WHERE id_aplicacion = :NEW.aplicacion_id_aplicacion;
        
        sp_notif_proyecto_completado(
            :NEW.id_ordenes_aplicaciones,
            v_nombre_aplicacion
        );
        
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
            :NEW.id_ordenes_aplicaciones,
            '{"estado_anterior_id":' || :OLD.estados_id_estados || '}',
            '{"estado_nuevo_id":' || :NEW.estados_id_estados || '}',
            NULL,
            (SELECT persona_id_persona FROM usuarios WHERE id_usuarios = :NEW.usuarios_id_usuarios)
        );
    END IF;
EXCEPTION
    WHEN NO_DATA_FOUND THEN
        NULL;
END;
/

CREATE OR REPLACE TRIGGER trg_notif_personal_asignado
AFTER INSERT ON personal_proyecto
FOR EACH ROW
BEGIN
    sp_notif_personal_asignado(
        :NEW.persona_id_persona, 
        :NEW.ordenes_aplicaciones_id_ordenes_aplicaciones
    );
END;
/

CREATE OR REPLACE TRIGGER trg_auditoria_estados_criticos
AFTER UPDATE ON ordenes_aplicaciones
FOR EACH ROW
DECLARE
    v_estado_cancelado NUMBER;
BEGIN
    SELECT id_estados INTO v_estado_cancelado
    FROM estados 
    WHERE estados_nombre = 'Cancelado'
    AND ROWNUM = 1;
    
    IF :NEW.estados_id_estados = v_estado_cancelado 
       AND :OLD.estados_id_estados != v_estado_cancelado THEN
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
            :NEW.id_ordenes_aplicaciones,
            '{"estado_anterior_id":' || :OLD.estados_id_estados || '}',
            '{"estado_nuevo_id":' || :NEW.estados_id_estados || '}',
            NULL,
            (SELECT persona_id_persona FROM usuarios WHERE id_usuarios = :NEW.usuarios_id_usuarios)
        );
    END IF;
EXCEPTION
    WHEN NO_DATA_FOUND THEN
        NULL;
END;
/