<?php
// C:\docker\sistema_aplicaciones\views\api\estadisticas.php
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Estadísticas del Sistema</h1>
            </div>
            <div class="col-sm-6">
                <button type="button" class="btn btn-info float-right" id="btnActualizarEstadisticas">
                    <i class="fas fa-sync-alt"></i> Actualizar
                </button>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Filtros -->
        <div class="row mb-3">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="filtroAmbito">Ámbito</label>
                    <select class="form-control" id="filtroAmbito">
                        <option value="global">Global</option>
                        <option value="proyecto">Por Proyecto</option>
                        <option value="usuario">Por Usuario</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4" id="filtroProyectoContainer" style="display: none;">
                <div class="form-group">
                    <label for="filtroProyecto">Proyecto</label>
                    <select class="form-control" id="filtroProyecto">
                        <option value="">Seleccione un proyecto</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4" id="filtroUsuarioContainer" style="display: none;">
                <div class="form-group">
                    <label for="filtroUsuario">Usuario</label>
                    <select class="form-control" id="filtroUsuario">
                        <option value="">Seleccione un usuario</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Tarjetas de Resumen -->
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3 id="totalProyectos">0</h3>
                        <p>Total Proyectos</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-project-diagram"></i>
                    </div>
                    <a href="/<?= $_ENV['APP_NAME'] ?>/proyectos" class="small-box-footer">
                        Más info <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3 id="totalDocumentos">0</h3>
                        <p>Total Documentos</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-file-pdf"></i>
                    </div>
                    <a href="/<?= $_ENV['APP_NAME'] ?>/documentos" class="small-box-footer">
                        Más info <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3 id="totalPersonal">0</h3>
                        <p>Personal Activo</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <a href="/<?= $_ENV['APP_NAME'] ?>/personal" class="small-box-footer">
                        Más info <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3 id="tareasPendientes">0</h3>
                        <p>Tareas Pendientes</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <a href="/<?= $_ENV['APP_NAME'] ?>/tareas" class="small-box-footer">
                        Más info <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Gráficas -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Proyectos por Estado</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="chartProyectosEstado" height="250"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Documentos por Categoría</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="chartDocumentosCategoria" height="250"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Distribución de Tareas</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="chartTareas" height="250"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Top Programadores</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="chartTopProgramadores" height="250"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="<?= asset('build/js/api/estadisticas.js') ?>"></script>