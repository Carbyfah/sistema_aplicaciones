-- TRIGGERS PARA CONTROL DE VERSIONES DE DOCUMENTOS
-- IMPORTANTE: El versionamiento debe manejarse desde PHP antes del INSERT

-- Trigger solo para registrar en logs cuando se detecta una nueva versión
DELIMITER //
CREATE TRIGGER trg_documento_version_log
AFTER INSERT ON documentos
FOR EACH ROW
BEGIN
    IF NEW.documento_original_id IS NOT NULL THEN
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
            'VERSION',
            'documentos',
            NEW.id_documentos,
            JSON_OBJECT(
                'documento_original', NEW.documento_original_id
            ),
            JSON_OBJECT(
                'nombre', NEW.documentos_nombre,
                'version', NEW.documentos_version
            ),
            NULL,
            (SELECT persona_id_persona FROM usuarios WHERE id_usuarios = NEW.usuarios_id_usuarios)
        );
    END IF;
END //
DELIMITER ;