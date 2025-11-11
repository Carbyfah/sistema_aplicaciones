-- PROCEDIMIENTOS PARA GENERACIÓN DE ESTADÍSTICAS EN ORACLE

-- Actualizar estadísticas globales
CREATE OR REPLACE PROCEDURE sp_actualizar_estadisticas_globales
IS
    v_total_proyectos NUMBER;
    v_proyectos_activos NUMBER;
    v_proyectos_completados NUMBER;
    v_total_documentos NUMBER;
    v_usuarios_activos NUMBER;
    v_json_datos CLOB;
BEGIN
    -- Eliminar estadísticas globales anteriores
    DELETE FROM estadisticas_cache 
    WHERE estadisticas_cache_ambito = 'global'
    AND estadisticas_cache_referencia_id IS NULL;
    
    -- Calcular valores
    SELECT COUNT(*) INTO v_total_proyectos 
    FROM aplicacion 
    WHERE aplicacion_situacion = 1;
    
    SELECT COUNT(*) INTO v_proyectos_activos 
    FROM ordenes_aplicaciones oa 
    JOIN estados e ON oa.estados_id_estados = e.id_estados
    WHERE e.estados_nombre NOT IN ('Completado', 'Cancelado')
    AND oa.ordenes_aplicaciones_situacion = 1;
    
    SELECT COUNT(*) INTO v_proyectos_completados 
    FROM ordenes_aplicaciones oa 
    JOIN estados e ON oa.estados_id_estados = e.id_estados
    WHERE e.estados_nombre = 'Completado'
    AND oa.ordenes_aplicaciones_situacion = 1;
    
    SELECT COUNT(*) INTO v_total_documentos 
    FROM documentos 
    WHERE documentos_situacion = 1;
    
    SELECT COUNT(*) INTO v_usuarios_activos 
    FROM usuarios 
    WHERE usuarios_situacion = 1;
    
    -- Construir JSON
    v_json_datos := '{"total_proyectos":' || v_total_proyectos || 
                    ',"proyectos_activos":' || v_proyectos_activos || 
                    ',"proyectos_completados":' || v_proyectos_completados || 
                    ',"total_documentos":' || v_total_documentos || 
                    ',"usuarios_activos":' || v_usuarios_activos || '}';
    
    -- Insertar en caché
    INSERT INTO estadisticas_cache (
        estadisticas_cache_tipo,
        estadisticas_cache_datos,
        estadisticas_cache_ambito,
        estadisticas_cache_referencia_id,
        fecha_expiracion
    )
    VALUES (
        'resumen_global',
        v_json_datos,
        'global',
        NULL,
        SYSTIMESTAMP + INTERVAL '1' DAY
    );
    
    COMMIT;
END sp_actualizar_estadisticas_globales;
/

-- Actualizar estadísticas por proyecto
CREATE OR REPLACE PROCEDURE sp_actualizar_estadisticas_proyecto(
    p_proyecto_id IN NUMBER
)
IS
    v_nombre_aplicacion VARCHAR2(100);
    v_total_documentos NUMBER;
    v_personal_asignado NUMBER;
    v_ultima_actividad TIMESTAMP;
    v_json_datos CLOB;
BEGIN
    -- Eliminar estadísticas del proyecto anteriores
    DELETE FROM estadisticas_cache 
    WHERE estadisticas_cache_ambito = 'proyecto'
    AND estadisticas_cache_referencia_id = p_proyecto_id;
    
    -- Obtener nombre del proyecto
    SELECT aplicacion_nombre INTO v_nombre_aplicacion
    FROM aplicacion
    WHERE id_aplicacion = p_proyecto_id;
    
    -- Calcular total de documentos
    SELECT COUNT(*) INTO v_total_documentos
    FROM documentos d 
    JOIN ordenes_aplicaciones oa ON d.ordenes_aplicaciones_id_ordenes_aplicaciones = oa.id_ordenes_aplicaciones
    WHERE oa.aplicacion_id_aplicacion = p_proyecto_id
    AND d.documentos_situacion = 1;
    
    -- Calcular personal asignado
    SELECT COUNT(*) INTO v_personal_asignado
    FROM personal_proyecto pp 
    JOIN ordenes_aplicaciones oa ON pp.ordenes_aplicaciones_id_ordenes_aplicaciones = oa.id_ordenes_aplicaciones
    WHERE oa.aplicacion_id_aplicacion = p_proyecto_id
    AND pp.personal_proyecto_situacion = 1;
    
    -- Obtener última actividad
    BEGIN
        SELECT MAX(logs_actividad_fecha) INTO v_ultima_actividad
        FROM logs_actividad la
        JOIN ordenes_aplicaciones oa ON la.logs_actividad_registro_id = oa.id_ordenes_aplicaciones 
        WHERE la.logs_actividad_tabla = 'ordenes_aplicaciones'
        AND oa.aplicacion_id_aplicacion = p_proyecto_id;
    EXCEPTION
        WHEN NO_DATA_FOUND THEN
            v_ultima_actividad := NULL;
    END;
    
    -- Construir JSON
    v_json_datos := '{"nombre":"' || v_nombre_aplicacion || 
                    '","documentos":' || v_total_documentos || 
                    ',"personal_asignado":' || v_personal_asignado || 
                    ',"ultima_actividad":"' || NVL(TO_CHAR(v_ultima_actividad, 'YYYY-MM-DD HH24:MI:SS'), 'null') || '"}';
    
    -- Insertar en caché
    INSERT INTO estadisticas_cache (
        estadisticas_cache_tipo,
        estadisticas_cache_datos,
        estadisticas_cache_ambito,
        estadisticas_cache_referencia_id,
        fecha_expiracion
    )
    VALUES (
        'resumen_proyecto',
        v_json_datos,
        'proyecto',
        p_proyecto_id,
        SYSTIMESTAMP + INTERVAL '1' DAY
    );
    
    COMMIT;
EXCEPTION
    WHEN NO_DATA_FOUND THEN
        NULL;
END sp_actualizar_estadisticas_proyecto;
/