<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Sistema Aplicaciones</title>

    <link rel="shortcut icon" href="<?= asset('images/cit.png') ?>" type="image/x-icon">
    <link rel="stylesheet" href="<?= asset('vendor/adminlte/css/adminlte.min.css') ?>">
    <link rel="stylesheet" href="<?= asset('build/css/app.css') ?>">
</head>

<body class="hold-transition login-page">
    <div class="login-box">
        <div class="login-logo">
            <img src="<?= asset('images/cit.png') ?>" alt="CIT Logo" width="80">
            <br>
            <b>Sistema</b> Aplicaciones
        </div>

        <div class="card">
            <div class="card-body login-card-body">

                <!-- FORMULARIO DE LOGIN -->
                <div id="contenedorLogin">
                    <p class="login-box-msg">Inicia sesión para comenzar</p>

                    <form id="formLogin">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" placeholder="Usuario"
                                name="usuarios_nombre" id="usuarios_nombre" required>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-user"></span>
                                </div>
                            </div>
                        </div>
                        <div class="input-group mb-3">
                            <input type="password" class="form-control" placeholder="Contraseña"
                                name="usuarios_password" id="usuarios_password" required>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-lock"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-block">
                                    Iniciar Sesión
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- BOTÓN PARA REGISTRAR PRIMER ADMIN (solo si no existe) -->
                    <div id="opcionRegistrarAdmin" style="display: none;">
                        <div class="text-center mt-3">
                            <p class="text-muted">¿No hay administrador registrado?</p>
                            <button type="button" class="btn btn-success btn-sm" id="btnMostrarRegistro">
                                <i class="fas fa-user-plus"></i> Registrar Primer Administrador
                            </button>
                        </div>
                    </div>
                </div>

                <!-- FORMULARIO DE REGISTRO DE PRIMER ADMIN -->
                <div id="contenedorRegistro" style="display: none;">
                    <p class="login-box-msg">Registrar Primer Administrador</p>

                    <form id="formRegistrarAdmin">
                        <div class="form-group">
                            <input type="text" class="form-control" placeholder="Nombres"
                                name="persona_nombres" id="persona_nombres" required>
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control" placeholder="Apellidos"
                                name="persona_apellidos" id="persona_apellidos" required>
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control" placeholder="Identidad"
                                name="persona_identidad" id="persona_identidad" required>
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control" placeholder="Nombre de Usuario"
                                name="usuarios_nombre_admin" id="usuarios_nombre_admin" required>
                        </div>
                        <div class="form-group">
                            <input type="password" class="form-control" placeholder="Contraseña"
                                name="usuarios_password_admin" id="usuarios_password_admin" required>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <button type="button" class="btn btn-secondary btn-block" id="btnVolverLogin">
                                    Volver
                                </button>
                            </div>
                            <div class="col-6">
                                <button type="submit" class="btn btn-success btn-block">
                                    Registrar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <p class="mt-3 mb-1 text-center">
                    <small class="text-muted">Sistema de Gestión de Aplicaciones v1.0</small>
                </p>
            </div>
        </div>
    </div>

    <script src="<?= asset('build/js/app.js') ?>"></script>
    <script src="<?= asset('build/js/inicio.js') ?>"></script>
</body>

</html>