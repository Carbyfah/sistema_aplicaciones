-- TRIGGERS PARA CAMBIOS AUTOMÁTICOS DE ESTADOS EN ORACLE

-- Cambio automático a "En Proceso" cuando se asigna personal
CREATE OR REPLACE TRIGGER trg_estado_proyecto_asignado
AFTER INSERT ON personal_proyecto
FOR EACH ROW
DECLARE
    v_estado_pendiente NUMBER;
    v_estado_en_proceso NUMBER;
    v_estado_actual NUMBER;
    PRAGMA AUTONOMOUS_TRANSACTION;
BEGIN
    SELECT id_estados INTO v_estado_pendiente
    FROM estados 
    WHERE estados_nombre = 'Pendiente'
    AND ROWNUM = 1;
    
    SELECT id_estados INTO v_estado_en_proceso
    FROM estados 
    WHERE estados_nombre = 'En Proceso'
    AND ROWNUM = 1;
    
    SELECT estados_id_estados INTO v_estado_actual
    FROM ordenes_aplicaciones
    WHERE id_ordenes_aplicaciones = :NEW.ordenes_aplicaciones_id_ordenes_aplicaciones;
    
    IF v_estado_actual = v_estado_pendiente THEN
        UPDATE ordenes_aplicaciones
        SET estados_id_estados = v_estado_en_proceso
        WHERE id_ordenes_aplicaciones = :NEW.ordenes_aplicaciones_id_ordenes_aplicaciones;
        
        COMMIT;
    END IF;
EXCEPTION
    WHEN NO_DATA_FOUND THEN
        NULL;
END;
/

-- Cierre automático de proyecto cuando todos los documentos están aprobados
CREATE OR REPLACE TRIGGER trg_estado_proyecto_documentos_completos
AFTER UPDATE ON documentos
FOR EACH ROW
DECLARE
    v_estado_en_proceso NUMBER;
    v_estado_completado NUMBER;
    v_estado_actual NUMBER;
    v_documentos_pendientes NUMBER;
    PRAGMA AUTONOMOUS_TRANSACTION;
BEGIN
    SELECT id_estados INTO v_estado_en_proceso
    FROM estados 
    WHERE estados_nombre = 'En Proceso'
    AND ROWNUM = 1;
    
    SELECT id_estados INTO v_estado_completado
    FROM estados 
    WHERE estados_nombre = 'Completado'
    AND ROWNUM = 1;
    
    SELECT estados_id_estados INTO v_estado_actual
    FROM ordenes_aplicaciones
    WHERE id_ordenes_aplicaciones = :NEW.ordenes_aplicaciones_id_ordenes_aplicaciones;
    
    IF :NEW.documentos_situacion = 1 
       AND :NEW.documento_original_id IS NULL 
       AND v_estado_actual = v_estado_en_proceso THEN
        
        SELECT COUNT(*) INTO v_documentos_pendientes
        FROM documentos d
        WHERE d.ordenes_aplicaciones_id_ordenes_aplicaciones = :NEW.ordenes_aplicaciones_id_ordenes_aplicaciones
        AND d.documentos_situacion = 1
        AND d.documento_original_id IS NULL;
        
        IF v_documentos_pendientes = 0 THEN
            UPDATE ordenes_aplicaciones
            SET estados_id_estados = v_estado_completado
            WHERE id_ordenes_aplicaciones = :NEW.ordenes_aplicaciones_id_ordenes_aplicaciones;
            
            COMMIT;
        END IF;
    END IF;
EXCEPTION
    WHEN NO_DATA_FOUND THEN
        NULL;
END;
/