-- PROCEDIMIENTOS PARA GENERACIÓN DE ESTADÍSTICAS

-- Actualizar estadísticas globales
DELIMITER //
CREATE PROCEDURE sp_actualizar_estadisticas_globales()
BEGIN
    DELETE FROM estadisticas_cache 
    WHERE estadisticas_cache_ambito = 'global'
    AND estadisticas_cache_referencia_id IS NULL;
    
    INSERT INTO estadisticas_cache (
        estadisticas_cache_tipo,
        estadisticas_cache_datos,
        estadisticas_cache_ambito,
        estadisticas_cache_referencia_id,
        fecha_expiracion
    )
    VALUES (
        'resumen_global',
        (
            SELECT JSON_OBJECT(
                'total_proyectos', (SELECT COUNT(*) FROM aplicacion WHERE aplicacion_situacion = 1),
                'proyectos_activos', (
                    SELECT COUNT(*) FROM ordenes_aplicaciones oa 
                    JOIN estados e ON oa.estados_id_estados = e.id_estados
                    WHERE e.estados_nombre NOT IN ('Completado', 'Cancelado')
                    AND oa.ordenes_aplicaciones_situacion = 1
                ),
                'proyectos_completados', (
                    SELECT COUNT(*) FROM ordenes_aplicaciones oa 
                    JOIN estados e ON oa.estados_id_estados = e.id_estados
                    WHERE e.estados_nombre = 'Completado'
                    AND oa.ordenes_aplicaciones_situacion = 1
                ),
                'total_documentos', (SELECT COUNT(*) FROM documentos WHERE documentos_situacion = 1),
                'usuarios_activos', (SELECT COUNT(*) FROM usuarios WHERE usuarios_situacion = 1)
            )
        ),
        'global',
        NULL,
        DATE_ADD(CURRENT_TIMESTAMP, INTERVAL 1 DAY)
    );
END //
DELIMITER ;

-- Actualizar estadísticas por proyecto
DELIMITER //
CREATE PROCEDURE sp_actualizar_estadisticas_proyecto(p_proyecto_id INTEGER)
BEGIN
    DELETE FROM estadisticas_cache 
    WHERE estadisticas_cache_ambito = 'proyecto'
    AND estadisticas_cache_referencia_id = p_proyecto_id;
    
    INSERT INTO estadisticas_cache (
        estadisticas_cache_tipo,
        estadisticas_cache_datos,
        estadisticas_cache_ambito,
        estadisticas_cache_referencia_id,
        fecha_expiracion
    )
    VALUES (
        'resumen_proyecto',
        (
            SELECT JSON_OBJECT(
                'nombre', a.aplicacion_nombre,
                'documentos', (
                    SELECT COUNT(*) FROM documentos d 
                    JOIN ordenes_aplicaciones oa ON d.ordenes_aplicaciones_id_ordenes_aplicaciones = oa.id_ordenes_aplicaciones
                    WHERE oa.aplicacion_id_aplicacion = p_proyecto_id
                    AND d.documentos_situacion = 1
                ),
                'personal_asignado', (
                    SELECT COUNT(*) FROM personal_proyecto pp 
                    JOIN ordenes_aplicaciones oa ON pp.ordenes_aplicaciones_id_ordenes_aplicaciones = oa.id_ordenes_aplicaciones
                    WHERE oa.aplicacion_id_aplicacion = p_proyecto_id
                    AND pp.personal_proyecto_situacion = 1
                ),
                'ultima_actividad', (
                    SELECT MAX(logs_actividad_fecha) FROM logs_actividad la
                    JOIN ordenes_aplicaciones oa ON la.logs_actividad_registro_id = oa.id_ordenes_aplicaciones AND la.logs_actividad_tabla = 'ordenes_aplicaciones'
                    WHERE oa.aplicacion_id_aplicacion = p_proyecto_id
                )
            )
            FROM aplicacion a
            WHERE a.id_aplicacion = p_proyecto_id
        ),
        'proyecto',
        p_proyecto_id,
        DATE_ADD(CURRENT_TIMESTAMP, INTERVAL 1 DAY)
    );
END //
DELIMITER ;