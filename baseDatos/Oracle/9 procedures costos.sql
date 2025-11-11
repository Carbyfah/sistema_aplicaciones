-- PROCEDIMIENTOS Y FUNCIONES PARA CALCULO AUTOMATICO DE COSTOS EN ORACLE
-- Sistema de Gestion de Aplicaciones
-- Fecha: 27 de octubre de 2025

-- ===========================================================
-- PROCEDIMIENTO: Calcular costo total de una aplicacion
-- ===========================================================
CREATE OR REPLACE PROCEDURE sp_calcular_costo_aplicacion(
    p_id_aplicacion_costos IN NUMBER
)
IS
    v_horas NUMBER(8,2);
    v_tarifa NUMBER(10,2);
    v_factor_complejidad NUMBER(5,2);
    v_factor_seguridad NUMBER(5,2);
    v_costo_base NUMBER(12,2);
    v_costo_final NUMBER(12,2);
BEGIN
    -- Obtener datos del costo
    SELECT 
        costos_horas_estimadas,
        costos_tarifa_hora,
        NVL(co.complejidad_factor, 1.00),
        NVL(so.seguridad_factor, 1.00)
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
    v_costo_base := v_horas * v_tarifa;
    
    -- Aplicar factores de complejidad y seguridad
    v_costo_final := v_costo_base * v_factor_complejidad * v_factor_seguridad;
    
    -- Actualizar el registro
    UPDATE aplicacion_costos 
    SET costos_total = v_costo_final,
        fecha_modificacion = SYSTIMESTAMP
    WHERE id_aplicacion_costos = p_id_aplicacion_costos;
    
    COMMIT;
EXCEPTION
    WHEN NO_DATA_FOUND THEN
        NULL;
END sp_calcular_costo_aplicacion;
/

-- ===========================================================
-- PROCEDIMIENTO: Obtener resumen de costos por aplicacion
-- ===========================================================
CREATE OR REPLACE PROCEDURE sp_resumen_costos_aplicacion(
    p_aplicacion_id IN NUMBER,
    p_cursor OUT SYS_REFCURSOR
)
IS
BEGIN
    OPEN p_cursor FOR
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
        (ac.costos_horas_estimadas * ac.costos_tarifa_hora * NVL(co.complejidad_factor, 1.00)) AS costo_con_complejidad,
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
END sp_resumen_costos_aplicacion;
/

-- ===========================================================
-- PROCEDIMIENTO: Comparar costos entre aplicaciones
-- ===========================================================
CREATE OR REPLACE PROCEDURE sp_comparar_costos_aplicaciones(
    p_situacion IN NUMBER,
    p_cursor OUT SYS_REFCURSOR
)
IS
BEGIN
    OPEN p_cursor FOR
    SELECT 
        a.id_aplicacion,
        a.aplicacion_nombre,
        ac.costos_total,
        ac.costos_moneda,
        ac.costos_horas_estimadas,
        co.complejidad_nombre,
        so.seguridad_nombre,
        -- Costo promedio por hora
        CASE 
            WHEN ac.costos_horas_estimadas = 0 THEN 0
            ELSE (ac.costos_total / ac.costos_horas_estimadas)
        END AS costo_por_hora
    FROM aplicacion a
    INNER JOIN aplicacion_costos ac ON a.id_aplicacion = ac.aplicacion_id_aplicacion
    LEFT JOIN complejidad_opciones co ON ac.complejidad_id = co.id_complejidad
    LEFT JOIN seguridad_opciones so ON ac.seguridad_id = so.id_seguridad
    WHERE a.aplicacion_situacion = p_situacion
    AND ac.costos_situacion = 1
    ORDER BY ac.costos_total DESC;
END sp_comparar_costos_aplicaciones;
/

-- ===========================================================
-- PROCEDIMIENTO: Estadisticas de costos globales
-- ===========================================================
CREATE OR REPLACE PROCEDURE sp_estadisticas_costos_globales(
    p_cursor OUT SYS_REFCURSOR
)
IS
BEGIN
    OPEN p_cursor FOR
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
         WHERE co.complejidad_nombre = 'Básica' AND ac2.costos_situacion = 1) AS total_baja_complejidad,
        (SELECT COUNT(*) FROM aplicacion_costos ac2 
         INNER JOIN complejidad_opciones co ON ac2.complejidad_id = co.id_complejidad
         WHERE co.complejidad_nombre = 'Media' AND ac2.costos_situacion = 1) AS total_media_complejidad,
        (SELECT COUNT(*) FROM aplicacion_costos ac2 
         INNER JOIN complejidad_opciones co ON ac2.complejidad_id = co.id_complejidad
         WHERE co.complejidad_nombre = 'Alta' AND ac2.costos_situacion = 1) AS total_alta_complejidad
    FROM aplicacion_costos ac
    WHERE ac.costos_situacion = 1;
END sp_estadisticas_costos_globales;
/

-- ===========================================================
-- PROCEDIMIENTO: Proyeccion de costos con cambios
-- ===========================================================
CREATE OR REPLACE PROCEDURE sp_proyectar_costo_aplicacion(
    p_id_aplicacion_costos IN NUMBER,
    p_nuevas_horas IN NUMBER,
    p_nueva_complejidad_id IN NUMBER,
    p_nueva_seguridad_id IN NUMBER,
    p_cursor OUT SYS_REFCURSOR
)
IS
    v_tarifa NUMBER(10,2);
    v_factor_complejidad NUMBER(5,2);
    v_factor_seguridad NUMBER(5,2);
    v_costo_proyectado NUMBER(12,2);
BEGIN
    -- Obtener tarifa actual
    SELECT costos_tarifa_hora INTO v_tarifa
    FROM aplicacion_costos
    WHERE id_aplicacion_costos = p_id_aplicacion_costos;
    
    -- Obtener factores
    SELECT NVL(complejidad_factor, 1.00) INTO v_factor_complejidad
    FROM complejidad_opciones
    WHERE id_complejidad = p_nueva_complejidad_id;
    
    SELECT NVL(seguridad_factor, 1.00) INTO v_factor_seguridad
    FROM seguridad_opciones
    WHERE id_seguridad = p_nueva_seguridad_id;
    
    -- Calcular proyeccion
    v_costo_proyectado := p_nuevas_horas * v_tarifa * 
                         NVL(v_factor_complejidad, 1.00) * 
                         NVL(v_factor_seguridad, 1.00);
    
    -- Devolver resultado comparativo
    OPEN p_cursor FOR
    SELECT 
        ac.costos_total AS costo_actual,
        v_costo_proyectado AS costo_proyectado,
        (v_costo_proyectado - ac.costos_total) AS diferencia,
        ROUND(
            CASE 
                WHEN ac.costos_total = 0 THEN 0
                ELSE ((v_costo_proyectado - ac.costos_total) / ac.costos_total * 100)
            END, 2
        ) AS porcentaje_cambio,
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
EXCEPTION
    WHEN NO_DATA_FOUND THEN
        NULL;
END sp_proyectar_costo_aplicacion;
/

-- ===========================================================
-- PROCEDIMIENTO: Analisis de rentabilidad
-- ===========================================================
CREATE OR REPLACE PROCEDURE sp_analisis_rentabilidad(
    p_aplicacion_id IN NUMBER,
    p_precio_venta IN NUMBER,
    p_cursor OUT SYS_REFCURSOR
)
IS
BEGIN
    OPEN p_cursor FOR
    SELECT 
        a.aplicacion_nombre,
        ac.costos_total AS costo_desarrollo,
        p_precio_venta AS precio_venta,
        (p_precio_venta - ac.costos_total) AS ganancia,
        ROUND(
            CASE 
                WHEN ac.costos_total = 0 THEN 0
                ELSE ((p_precio_venta - ac.costos_total) / ac.costos_total * 100)
            END, 2
        ) AS margen_porcentual,
        CASE 
            WHEN p_precio_venta > ac.costos_total THEN 'Rentable'
            WHEN p_precio_venta = ac.costos_total THEN 'Punto de equilibrio'
            ELSE 'No rentable'
        END AS estado_rentabilidad,
        ac.costos_horas_estimadas AS horas_invertidas,
        CASE 
            WHEN ac.costos_horas_estimadas = 0 THEN 0
            ELSE (p_precio_venta / ac.costos_horas_estimadas)
        END AS ganancia_por_hora
    FROM aplicacion a
    INNER JOIN aplicacion_costos ac ON a.id_aplicacion = ac.aplicacion_id_aplicacion
    WHERE a.id_aplicacion = p_aplicacion_id
    AND ac.costos_situacion = 1;
END sp_analisis_rentabilidad;
/

-- ===========================================================
-- FUNCION: Obtener costo total de aplicacion
-- ===========================================================
CREATE OR REPLACE FUNCTION fn_obtener_costo_aplicacion(
    p_aplicacion_id IN NUMBER
) RETURN NUMBER
IS
    v_costo NUMBER(12,2);
BEGIN
    SELECT NVL(costos_total, 0) INTO v_costo
    FROM aplicacion_costos
    WHERE aplicacion_id_aplicacion = p_aplicacion_id
    AND costos_situacion = 1
    AND ROWNUM = 1;
    
    RETURN v_costo;
EXCEPTION
    WHEN NO_DATA_FOUND THEN
        RETURN 0;
END fn_obtener_costo_aplicacion;
/

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