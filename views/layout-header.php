<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo ?? 'Sistema Aplicaciones' ?></title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= asset('vendor/fontawesome-free/css/all.min.css') ?>">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- AdminLTE -->
    <link rel="stylesheet" href="<?= asset('vendor/adminlte/css/adminlte.min.css') ?>">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="<?= asset('vendor/overlayScrollbars/css/OverlayScrollbars.min.css') ?>">
    <!-- App CSS -->
    <link rel="stylesheet" href="<?= asset('build/css/app.css') ?>">

    <link rel="shortcut icon" href="<?= asset('images/cit.png') ?>" type="image/x-icon">
    <link rel="stylesheet" href="<?= asset('node_modules/intro.js/introjs.css') ?>">

</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-dark navbar-primary">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="/<?= $_ENV['APP_NAME'] ?>/dashboard" class="nav-link">Inicio</a>
                </li>
            </ul>

            <ul class="navbar-nav ml-auto">
                <?php if (tienePermiso('notificaciones.ver')): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link" data-toggle="dropdown" href="#" id="btnNotificaciones">
                            <i class="far fa-bell"></i>
                            <span class="badge badge-warning navbar-badge" id="contadorNotificaciones" style="display: none;">0</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                            <span class="dropdown-item dropdown-header" id="headerNotificaciones">Sin notificaciones sin leer</span>
                            <div class="dropdown-divider"></div>
                            <div id="listaNotificaciones">
                                <a href="#" class="dropdown-item">
                                    <i class="fas fa-spinner fa-spin mr-2"></i> Cargando...
                                </a>
                            </div>
                            <div class="dropdown-divider"></div>
                            <a href="/<?= $_ENV['APP_NAME'] ?>/notificaciones" class="dropdown-item dropdown-footer">Ver todas</a>
                        </div>
                    </li>
                <?php endif; ?>

                <li class="nav-item dropdown">
                    <a class="nav-link" data-toggle="dropdown" href="#" id="btnUsuario">
                        <i class="far fa-user"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <?php
                        // Ocultar Vista Guiada en páginas específicas
                        $rutaActual = $_SERVER['REQUEST_URI'];
                        $ocultarVistaGuiada = strpos($rutaActual, '/roles') !== false ||
                            strpos($rutaActual, '/manuales') !== false;

                        if (!$ocultarVistaGuiada):
                        ?>
                            <a href="#" class="dropdown-item" id="btnVistaGuiada">
                                <i class="fas fa-route mr-2"></i> Vista Guiada
                            </a>
                        <?php endif; ?>

                        <a href="/<?= $_ENV['APP_NAME'] ?>/manuales" class="dropdown-item">
                            <i class="fas fa-book mr-2"></i> Ver Manuales
                        </a>

                        <div class="dropdown-divider"></div>

                        <form method="POST" action="/<?= $_ENV['APP_NAME'] ?>/logout" id="formLogout">
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="fas fa-sign-out-alt mr-2"></i> Cerrar Sesión
                            </button>
                        </form>
                    </div>
                </li>
            </ul>
        </nav>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="/<?= $_ENV['APP_NAME'] ?>/dashboard" class="brand-link">
                <img src="<?= asset('images/cit.png') ?>" alt="Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
                <span class="brand-text font-weight-light">Aplicaciones</span>
            </a>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

                        <?php if (tienePermiso('dashboard.ver')): ?>
                            <li class="nav-item">
                                <a href="/<?= $_ENV['APP_NAME'] ?>/dashboard" class="nav-link">
                                    <i class="nav-icon fas fa-tachometer-alt"></i>
                                    <p>Dashboard</p>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if (tienePermiso('mis-proyectos.ver')): ?>
                            <li class="nav-item">
                                <a href="/<?= $_ENV['APP_NAME'] ?>/mis-proyectos" class="nav-link">
                                    <i class="nav-icon fas fa-tasks"></i>
                                    <p>Mis Proyectos</p>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if (tienePermiso('estadisticas.ver')): ?>
                            <li class="nav-item">
                                <a href="/<?= $_ENV['APP_NAME'] ?>/estadisticas" class="nav-link">
                                    <i class="nav-icon fas fa-chart-bar"></i>
                                    <p>Estadísticas</p>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if (tieneAlgunPermiso(['proyectos.ver', 'proyectos-asignados.ver', 'estados.ver'])): ?>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon fas fa-project-diagram"></i>
                                    <p>
                                        Proyectos
                                        <i class="right fas fa-angle-left"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <?php if (tienePermiso('proyectos.ver')): ?>
                                        <li class="nav-item">
                                            <a href="/<?= $_ENV['APP_NAME'] ?>/proyectos" class="nav-link">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>Catálogo</p>
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <?php if (tienePermiso('proyectos-asignados.ver')): ?>
                                        <li class="nav-item">
                                            <a href="/<?= $_ENV['APP_NAME'] ?>/proyectos-asignados" class="nav-link">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>Asignados</p>
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <?php if (tienePermiso('estados.ver')): ?>
                                        <li class="nav-item">
                                            <a href="/<?= $_ENV['APP_NAME'] ?>/estados" class="nav-link">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>Estados</p>
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <?php if (tienePermiso('tareas.ver')): ?>
                                        <li class="nav-item">
                                            <a href="/<?= $_ENV['APP_NAME'] ?>/tareas" class="nav-link">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>Tareas</p>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </li>
                        <?php endif; ?>

                        <?php if (tieneAlgunPermiso(['complejidad.ver', 'seguridad.ver', 'costos.ver'])): ?>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon fas fa-dollar-sign"></i>
                                    <p>
                                        Costos
                                        <i class="right fas fa-angle-left"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <?php if (tienePermiso('complejidad.ver')): ?>
                                        <li class="nav-item">
                                            <a href="/<?= $_ENV['APP_NAME'] ?>/complejidad" class="nav-link">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>Complejidad</p>
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <?php if (tienePermiso('seguridad.ver')): ?>
                                        <li class="nav-item">
                                            <a href="/<?= $_ENV['APP_NAME'] ?>/seguridad" class="nav-link">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>Seguridad</p>
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <?php if (tienePermiso('costos.ver')): ?>
                                        <li class="nav-item">
                                            <a href="/<?= $_ENV['APP_NAME'] ?>/costos" class="nav-link">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>Gestión de Costos</p>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </li>
                        <?php endif; ?>

                        <?php if (tieneAlgunPermiso(['tipos-tabla.ver', 'tipos-clave.ver', 'tipos-dato.ver', 'tablas.ver', 'campos.ver'])): ?>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon fas fa-database"></i>
                                    <p>
                                        Base de Datos
                                        <i class="right fas fa-angle-left"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <?php if (tienePermiso('tipos-tabla.ver')): ?>
                                        <li class="nav-item">
                                            <a href="/<?= $_ENV['APP_NAME'] ?>/tipos-tabla" class="nav-link">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>Tipos de Tabla</p>
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <?php if (tienePermiso('tipos-clave.ver')): ?>
                                        <li class="nav-item">
                                            <a href="/<?= $_ENV['APP_NAME'] ?>/tipos-clave" class="nav-link">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>Tipos de Clave</p>
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <?php if (tienePermiso('tipos-dato.ver')): ?>
                                        <li class="nav-item">
                                            <a href="/<?= $_ENV['APP_NAME'] ?>/tipos-dato" class="nav-link">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>Tipos de Dato</p>
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <?php if (tienePermiso('tablas.ver')): ?>
                                        <li class="nav-item">
                                            <a href="/<?= $_ENV['APP_NAME'] ?>/tablas" class="nav-link">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>Tablas</p>
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <?php if (tienePermiso('campos.ver')): ?>
                                        <li class="nav-item">
                                            <a href="/<?= $_ENV['APP_NAME'] ?>/campos" class="nav-link">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>Campos</p>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </li>
                        <?php endif; ?>

                        <?php if (tieneAlgunPermiso(['documentos.ver', 'categorias.ver'])): ?>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon fas fa-folder-open"></i>
                                    <p>
                                        Documentos
                                        <i class="right fas fa-angle-left"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <?php if (tienePermiso('documentos.ver')): ?>
                                        <li class="nav-item">
                                            <a href="/<?= $_ENV['APP_NAME'] ?>/documentos" class="nav-link">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>Todos</p>
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <?php if (tienePermiso('categorias.ver')): ?>
                                        <li class="nav-item">
                                            <a href="/<?= $_ENV['APP_NAME'] ?>/categorias" class="nav-link">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>Categorías</p>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </li>
                        <?php endif; ?>

                        <?php if (tieneAlgunPermiso(['personal.ver', 'personal-proyecto.ver', 'tareas.ver'])): ?>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon fas fa-users"></i>
                                    <p>
                                        Personal
                                        <i class="right fas fa-angle-left"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <?php if (tienePermiso('personal.ver')): ?>
                                        <li class="nav-item">
                                            <a href="/<?= $_ENV['APP_NAME'] ?>/personal" class="nav-link">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>Gestión</p>
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <?php if (tienePermiso('personal-proyecto.ver')): ?>
                                        <li class="nav-item">
                                            <a href="/<?= $_ENV['APP_NAME'] ?>/personal-proyecto" class="nav-link">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>Asignaciones</p>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </li>
                        <?php endif; ?>

                        <?php if (tieneAlgunPermiso(['usuarios.ver', 'roles.ver', 'modulos.ver', 'usuarios-permisos.ver', 'sistema.ver'])): ?>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon fas fa-cog"></i>
                                    <p>
                                        Configuración
                                        <i class="right fas fa-angle-left"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <?php if (tienePermiso('usuarios.ver')): ?>
                                        <li class="nav-item">
                                            <a href="/<?= $_ENV['APP_NAME'] ?>/usuarios" class="nav-link">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>Usuarios</p>
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <?php if (tienePermiso('roles.ver')): ?>
                                        <li class="nav-item">
                                            <a href="/<?= $_ENV['APP_NAME'] ?>/roles" class="nav-link">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>Roles</p>
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <?php if (tienePermiso('modulos.ver')): ?>
                                        <li class="nav-item">
                                            <a href="/<?= $_ENV['APP_NAME'] ?>/modulos" class="nav-link">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>Módulos</p>
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <?php if (tienePermiso('usuarios-permisos.ver')): ?>
                                        <li class="nav-item">
                                            <a href="/<?= $_ENV['APP_NAME'] ?>/usuarios-permisos" class="nav-link">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>Permisos de Usuarios</p>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </li>
                        <?php endif; ?>

                        <?php if (tieneAlgunPermiso(['logs.ver', 'sesiones.ver', 'intentos-login.ver'])): ?>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon fas fa-clipboard-list"></i>
                                    <p>
                                        Auditoría
                                        <i class="right fas fa-angle-left"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <?php if (tienePermiso('logs.ver')): ?>
                                        <li class="nav-item">
                                            <a href="/<?= $_ENV['APP_NAME'] ?>/logs" class="nav-link">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>Logs</p>
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <?php if (tienePermiso('sesiones.ver')): ?>
                                        <li class="nav-item">
                                            <a href="/<?= $_ENV['APP_NAME'] ?>/sesiones" class="nav-link">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>Sesiones</p>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </li>
                        <?php endif; ?>

                    </ul>
                </nav>
                <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
        </aside>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">