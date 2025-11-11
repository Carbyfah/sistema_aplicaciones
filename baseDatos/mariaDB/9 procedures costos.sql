-- PROCEDIMIENTOS Y TRIGGERS PARA CALCULO AUTOMATICO DE COSTOS
-- Sistema de Gestion de Aplicaciones
-- Fecha: 27 de octubre de 2025

-- ===========================================================
-- PROCEDIMIENTO: Calcular costo total de una aplicacion
-- ===========================================================
DELIMITER //
CREATE PROCEDURE sp_calcular_costo_aplicacion(
    IN p_id_aplicacion_costos INT
)
BEGIN
    DECLARE v_horas DECIMAL(8,2);
    DECLARE v_tarifa DECIMAL(10,2);
    DECLARE v_factor_complejidad DECIMAL(5,2);
    DECLARE v_factor_seguridad DECIMAL(5,2);
    DECLARE v_costo_base DECIMAL(12,2);
    DECLARE v_costo_final DECIMAL(12,2);
    
    -- Obtener datos del costo
    SELECT 
        costos_horas_estimadas,
        costos_tarifa_hora,
        COALESCE(co.complejidad_factor, 1.00),
        COALESCE(so.seguridad_factor, 1.00)
    INTO 
        v_horas,
        v_tarifa,
        v_factor_complejidad,
        v_factor_seguridad
    FROM aplicacion_costos ac
    LEFT JOIN complejidad_opciones co ON ac.complejidad_id = co.id_complejidad
    LEFT JOIN seguridad_opciones so ON ac.seguridad_id = so.id_seguridad
    WHERE ac.id_aplicacion_costos = p_id_aplicacion_costos;
    
    -- Calcular costo base (horas x tarifa)
    SET v_costo_base = v_horas * v_tarifa;
    
    -- Aplicar factores de complejidad y seguridad
    SET v_costo_final = v_costo_base * v_factor_complejidad * v_factor_seguridad;
    
    -- Actualizar el registro
    UPDATE aplicacion_costos 
    SET costos_total = v_costo_final,
        fecha_modificacion = CURRENT_TIMESTAMP
    WHERE id_aplicacion_costos = p_id_aplicacion_costos;
END //
DELIMITER ;


-- ===========================================================
-- PROCEDIMIENTO: Obtener resumen de costos por aplicacion
-- ===========================================================
DELIMITER //
CREATE PROCEDURE sp_resumen_costos_aplicacion(
    IN p_aplicacion_id INT
)
BEGIN
    SELECT 
        a.id_aplicacion,
        a.aplicacion_nombre,
        ac.costos_horas_estimadas,
        ac.costos_tarifa_hora,
        ac.costos_total,
        ac.costos_moneda,
        co.complejidad_nombre,
        co.complejidad_factor,
        so.seguridad_nombre,
        so.seguridad_factor,
        -- Desglose del calculo
        (ac.costos_horas_estimadas * ac.costos_tarifa_hora) AS costo_base,
        (ac.costos_horas_estimadas * ac.costos_tarifa_hora * COALESCE(co.complejidad_factor, 1.00)) AS costo_con_complejidad,
        ac.costos_total AS costo_final,
        -- Metadata
        ac.costos_notas,
        ac.fecha_creacion,
        ac.fecha_modificacion
    FROM aplicacion a
    LEFT JOIN aplicacion_costos ac ON a.id_aplicacion = ac.aplicacion_id_aplicacion
    LEFT JOIN complejidad_opciones co ON ac.complejidad_id = co.id_complejidad
    LEFT JOIN seguridad_opciones so ON ac.seguridad_id = so.id_seguridad
    WHERE a.id_aplicacion = p_aplicacion_id
    AND ac.costos_situacion = 1;
END //
DELIMITER ;

-- ===========================================================
-- PROCEDIMIENTO: Comparar costos entre aplicaciones
-- ===========================================================
DELIMITER //
CREATE PROCEDURE sp_comparar_costos_aplicaciones(
    IN p_situacion SMALLINT
)
BEGIN
    SELECT 
        a.id_aplicacion,
        a.aplicacion_nombre,
        ac.costos_total,
        ac.costos_moneda,
        ac.costos_horas_estimadas,
        co.complejidad_nombre,
        so.seguridad_nombre,
        -- Costo promedio por hora
        (ac.costos_total / NULLIF(ac.costos_horas_estimadas, 0)) AS costo_por_hora
    FROM aplicacion a
    INNER JOIN aplicacion_costos ac ON a.id_aplicacion = ac.aplicacion_id_aplicacion
    LEFT JOIN complejidad_opciones co ON ac.complejidad_id = co.id_complejidad
    LEFT JOIN seguridad_opciones so ON ac.seguridad_id = so.id_seguridad
    WHERE a.aplicacion_situacion = p_situacion
    AND ac.costos_situacion = 1
    ORDER BY ac.costos_total DESC;
END //
DELIMITER ;

-- ===========================================================
-- PROCEDIMIENTO: Estadisticas de costos globales
-- ===========================================================
DELIMITER //
CREATE PROCEDURE sp_estadisticas_costos_globales()
BEGIN
    SELECT 
        COUNT(DISTINCT ac.aplicacion_id_aplicacion) AS total_aplicaciones_con_costo,
        SUM(ac.costos_total) AS suma_total_costos,
        AVG(ac.costos_total) AS promedio_costo_aplicacion,
        MIN(ac.costos_total) AS costo_minimo,
        MAX(ac.costos_total) AS costo_maximo,
        SUM(ac.costos_horas_estimadas) AS total_horas_estimadas,
        AVG(ac.costos_tarifa_hora) AS tarifa_promedio_hora,
        -- Por complejidad
        (SELECT COUNT(*) FROM aplicacion_costos ac2 
         INNER JOIN complejidad_opciones co ON ac2.complejidad_id = co.id_complejidad
         WHERE co.complejidad_nombre = 'Baja' AND ac2.costos_situacion = 1) AS total_baja_complejidad,
        (SELECT COUNT(*) FROM aplicacion_costos ac2 
         INNER JOIN complejidad_opciones co ON ac2.complejidad_id = co.id_complejidad
         WHERE co.complejidad_nombre = 'Media' AND ac2.costos_situacion = 1) AS total_media_complejidad,
        (SELECT COUNT(*) FROM aplicacion_costos ac2 
         INNER JOIN complejidad_opciones co ON ac2.complejidad_id = co.id_complejidad
         WHERE co.complejidad_nombre = 'Alta' AND ac2.costos_situacion = 1) AS total_alta_complejidad
    FROM aplicacion_costos ac
    WHERE ac.costos_situacion = 1;
END //
DELIMITER ;

-- ===========================================================
-- PROCEDIMIENTO: Proyeccion de costos con cambios
-- ===========================================================
DELIMITER //
CREATE PROCEDURE sp_proyectar_costo_aplicacion(
    IN p_id_aplicacion_costos INT,
    IN p_nuevas_horas DECIMAL(8,2),
    IN p_nueva_complejidad_id INT,
    IN p_nueva_seguridad_id INT
)
BEGIN
    DECLARE v_tarifa DECIMAL(10,2);
    DECLARE v_factor_complejidad DECIMAL(5,2);
    DECLARE v_factor_seguridad DECIMAL(5,2);
    DECLARE v_costo_proyectado DECIMAL(12,2);
    
    -- Obtener tarifa actual
    SELECT costos_tarifa_hora INTO v_tarifa
    FROM aplicacion_costos
    WHERE id_aplicacion_costos = p_id_aplicacion_costos;
    
    -- Obtener factores
    SELECT COALESCE(complejidad_factor, 1.00) INTO v_factor_complejidad
    FROM complejidad_opciones
    WHERE id_complejidad = p_nueva_complejidad_id;
    
    SELECT COALESCE(seguridad_factor, 1.00) INTO v_factor_seguridad
    FROM seguridad_opciones
    WHERE id_seguridad = p_nueva_seguridad_id;
    
    -- Calcular proyeccion
    SET v_costo_proyectado = p_nuevas_horas * v_tarifa * 
                             COALESCE(v_factor_complejidad, 1.00) * 
                             COALESCE(v_factor_seguridad, 1.00);
    
    -- Devolver resultado comparativo
    SELECT 
        ac.costos_total AS costo_actual,
        v_costo_proyectado AS costo_proyectado,
        (v_costo_proyectado - ac.costos_total) AS diferencia,
        ROUND(((v_costo_proyectado - ac.costos_total) / NULLIF(ac.costos_total, 0) * 100), 2) AS porcentaje_cambio,
        ac.costos_horas_estimadas AS horas_actuales,
        p_nuevas_horas AS horas_proyectadas,
        co_actual.complejidad_nombre AS complejidad_actual,
        co_nueva.complejidad_nombre AS complejidad_proyectada,
        so_actual.seguridad_nombre AS seguridad_actual,
        so_nueva.seguridad_nombre AS seguridad_proyectada
    FROM aplicacion_costos ac
    LEFT JOIN complejidad_opciones co_actual ON ac.complejidad_id = co_actual.id_complejidad
    LEFT JOIN seguridad_opciones so_actual ON ac.seguridad_id = so_actual.id_seguridad
    LEFT JOIN complejidad_opciones co_nueva ON p_nueva_complejidad_id = co_nueva.id_complejidad
    LEFT JOIN seguridad_opciones so_nueva ON p_nueva_seguridad_id = so_nueva.id_seguridad
    WHERE ac.id_aplicacion_costos = p_id_aplicacion_costos;
END //
DELIMITER ;

-- ===========================================================
-- PROCEDIMIENTO: Analisis de rentabilidad
-- ===========================================================
DELIMITER //
CREATE PROCEDURE sp_analisis_rentabilidad(
    IN p_aplicacion_id INT,
    IN p_precio_venta DECIMAL(12,2)
)
BEGIN
    SELECT 
        a.aplicacion_nombre,
        ac.costos_total AS costo_desarrollo,
        p_precio_venta AS precio_venta,
        (p_precio_venta - ac.costos_total) AS ganancia,
        ROUND(((p_precio_venta - ac.costos_total) / NULLIF(ac.costos_total, 0) * 100), 2) AS margen_porcentual,
        CASE 
            WHEN p_precio_venta > ac.costos_total THEN 'Rentable'
            WHEN p_precio_venta = ac.costos_total THEN 'Punto de equilibrio'
            ELSE 'No rentable'
        END AS estado_rentabilidad,
        ac.costos_horas_estimadas AS horas_invertidas,
        (p_precio_venta / NULLIF(ac.costos_horas_estimadas, 0)) AS ganancia_por_hora
    FROM aplicacion a
    INNER JOIN aplicacion_costos ac ON a.id_aplicacion = ac.aplicacion_id_aplicacion
    WHERE a.id_aplicacion = p_aplicacion_id
    AND ac.costos_situacion = 1;
END //
DELIMITER ;

-- ===========================================================
-- FUNCION: Obtener costo total de aplicacion
-- ===========================================================
DELIMITER //
CREATE FUNCTION fn_obtener_costo_aplicacion(
    p_aplicacion_id INT
) RETURNS DECIMAL(12,2)
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE v_costo DECIMAL(12,2);
    
    SELECT COALESCE(costos_total, 0) INTO v_costo
    FROM aplicacion_costos
    WHERE aplicacion_id_aplicacion = p_aplicacion_id
    AND costos_situacion = 1
    LIMIT 1;
    
    RETURN v_costo;
END //
DELIMITER ;

-- ===========================================================
-- VISTA: Resumen de costos con informacion completa
-- ===========================================================
CREATE OR REPLACE VIEW v_costos_aplicaciones AS
SELECT 
    a.id_aplicacion,
    a.aplicacion_nombre,
    a.aplicacion_desc_corta,
    ac.costos_horas_estimadas,
    ac.costos_tarifa_hora,
    ac.costos_total,
    ac.costos_moneda,
    co.complejidad_nombre,
    co.complejidad_factor,
    so.seguridad_nombre,
    so.seguridad_factor,
    (ac.costos_horas_estimadas * ac.costos_tarifa_hora) AS costo_base,
    ac.costos_notas,
    u_creador.usuarios_nombre AS creado_por_usuario,
    ac.fecha_creacion,
    ac.fecha_modificacion
FROM aplicacion a
LEFT JOIN aplicacion_costos ac ON a.id_aplicacion = ac.aplicacion_id_aplicacion
LEFT JOIN complejidad_opciones co ON ac.complejidad_id = co.id_complejidad
LEFT JOIN seguridad_opciones so ON ac.seguridad_id = so.id_seguridad
LEFT JOIN usuarios u_creador ON ac.creado_por = u_creador.id_usuarios
WHERE a.aplicacion_situacion = 1
AND (ac.costos_situacion = 1 OR ac.costos_situacion IS NULL);