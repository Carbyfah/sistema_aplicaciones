-- PROCEDIMIENTOS PARA GESTIÓN DE SESIONES

-- Cerrar sesión de usuario
DELIMITER //
CREATE PROCEDURE sp_cerrar_sesion(p_token VARCHAR(255))
BEGIN
    DECLARE v_sesion_id INTEGER;
    
    SELECT sesion_id INTO v_sesion_id
    FROM sesiones_usuarios
    WHERE sesion_token = p_token
    AND sesion_estado = 1;
    
    IF v_sesion_id IS NOT NULL THEN
        UPDATE sesiones_usuarios
        SET sesion_fecha_cierre = CURRENT_TIMESTAMP,
            sesion_estado = 0
        WHERE sesion_id = v_sesion_id;
    END IF;
END //
DELIMITER ;

-- Registrar intento de login
DELIMITER //
CREATE PROCEDURE sp_registrar_intento_login(
    p_usuario_nombre VARCHAR(45),
    p_exitoso SMALLINT,
    p_detalle VARCHAR(255)
)
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
END //
DELIMITER ;