-- PROCEDIMIENTOS PARA SISTEMA DE NOTIFICACIONES

-- Notificación de asignación de proyecto a programador
DELIMITER //
CREATE PROCEDURE sp_notif_asignacion_proyecto(
    p_orden_id INTEGER,
    p_usuario_id INTEGER,
    p_proyecto_nombre VARCHAR(100)
)
BEGIN
    INSERT INTO notificaciones (
        persona_id_persona,
        notificaciones_tipo,
        notificaciones_titulo,
        notificaciones_mensaje,
        notificaciones_objeto_tipo,
        notificaciones_objeto_id
    )
    SELECT 
        u.persona_id_persona,
        'PROYECTO',
        'Proyecto Asignado',
        CONCAT('Se te ha asignado el proyecto: ', p_proyecto_nombre),
        'ordenes_aplicaciones',
        p_orden_id
    FROM usuarios u
    WHERE u.id_usuarios = p_usuario_id;
END //
DELIMITER ;

-- Notificación de nuevo documento
DELIMITER //
CREATE PROCEDURE sp_notif_documento_nuevo(
    p_documento_id INTEGER,
    p_documento_nombre VARCHAR(255),
    p_orden_id INTEGER,
    p_usuario_id INTEGER
)
BEGIN
    INSERT INTO notificaciones (
        persona_id_persona,
        notificaciones_tipo,
        notificaciones_titulo,
        notificaciones_mensaje,
        notificaciones_objeto_tipo,
        notificaciones_objeto_id
    )
    SELECT 
        u.persona_id_persona,
        'DOCUMENTO',
        'Nuevo Documento',
        CONCAT('Se ha subido el documento: ', p_documento_nombre),
        'documentos',
        p_documento_id
    FROM usuarios u
    WHERE u.id_usuarios = (
        SELECT usuarios_id_usuarios 
        FROM ordenes_aplicaciones 
        WHERE id_ordenes_aplicaciones = p_orden_id
    )
    AND u.id_usuarios != p_usuario_id;
END //
DELIMITER ;

-- Notificación de proyecto completado
DELIMITER //
CREATE PROCEDURE sp_notif_proyecto_completado(
    p_orden_id INTEGER,
    p_proyecto_nombre VARCHAR(100)
)
BEGIN
    INSERT INTO notificaciones (
        persona_id_persona,
        notificaciones_tipo,
        notificaciones_titulo,
        notificaciones_mensaje,
        notificaciones_objeto_tipo,
        notificaciones_objeto_id
    )
    SELECT DISTINCT
        p.id_persona,
        'EXITO',
        'Proyecto Completado',
        CONCAT('El proyecto ', p_proyecto_nombre, ' ha sido completado exitosamente'),
        'ordenes_aplicaciones',
        p_orden_id
    FROM personal_proyecto pp
    JOIN persona p ON pp.persona_id_persona = p.id_persona
    WHERE pp.ordenes_aplicaciones_id_ordenes_aplicaciones = p_orden_id
    AND pp.personal_proyecto_situacion = 1;
END //
DELIMITER ;

-- Notificación de personal asignado a proyecto (faltaba en el original)
DELIMITER //
CREATE PROCEDURE sp_notif_personal_asignado(
    p_persona_id INTEGER,
    p_orden_id INTEGER
)
BEGIN
    INSERT INTO notificaciones (
        persona_id_persona,
        notificaciones_tipo,
        notificaciones_titulo,
        notificaciones_mensaje,
        notificaciones_objeto_tipo,
        notificaciones_objeto_id
    )
    SELECT 
        p_persona_id,
        'ASIGNACION',
        'Asignación a Proyecto',
        CONCAT('Has sido asignado al proyecto: ', a.aplicacion_nombre),
        'ordenes_aplicaciones',
        p_orden_id
    FROM ordenes_aplicaciones oa
    JOIN aplicacion a ON oa.aplicacion_id_aplicacion = a.id_aplicacion
    WHERE oa.id_ordenes_aplicaciones = p_orden_id;
END //
DELIMITER ;