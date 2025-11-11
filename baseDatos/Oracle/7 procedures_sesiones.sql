-- PROCEDIMIENTOS PARA GESTIÓN DE SESIONES EN ORACLE

-- Cerrar sesión de usuario
CREATE OR REPLACE PROCEDURE sp_cerrar_sesion(
    p_token IN VARCHAR2
)
IS
    v_sesion_id NUMBER;
BEGIN
    -- Buscar sesión activa
    SELECT sesion_id INTO v_sesion_id
    FROM sesiones_usuarios
    WHERE sesion_token = p_token
    AND sesion_estado = 1;
    
    -- Cerrar sesión
    UPDATE sesiones_usuarios
    SET sesion_fecha_cierre = SYSTIMESTAMP,
        sesion_estado = 0
    WHERE sesion_id = v_sesion_id;
    
    COMMIT;
EXCEPTION
    WHEN NO_DATA_FOUND THEN
        NULL; -- Sesión no encontrada o ya cerrada
END sp_cerrar_sesion;
/

-- Registrar intento de login
CREATE OR REPLACE PROCEDURE sp_registrar_intento_login(
    p_usuario_nombre IN VARCHAR2,
    p_exitoso IN NUMBER,
    p_detalle IN VARCHAR2
)
IS
BEGIN
    INSERT INTO intentos_login (
        usuario_nombre,
        intento_exitoso,
        intento_detalle
    )
    VALUES (
        p_usuario_nombre,
        p_exitoso,
        p_detalle
    );
    
    COMMIT;
END sp_registrar_intento_login;
/