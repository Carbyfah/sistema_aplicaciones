<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Registro de Actividades</h1>
            </div>
            <div class="col-sm-6">
                <div class="btn-group float-right">
                    <button type="button" class="btn btn-info" id="btnRefrescar">
                        <i class="bi bi-arrow-clockwise"></i> Refrescar
                    </button>
                    <button type="button" class="btn btn-secondary" id="btnExportar">
                        <i class="bi bi-download"></i> Exportar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Historial de Actividades del Sistema</h3>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="filtroAccion">Filtrar por Acción</label>
                            <select class="form-control" id="filtroAccion">
                                <option value="">Todas las acciones</option>
                                <option value="INSERT">INSERT</option>
                                <option value="UPDATE">UPDATE</option>
                                <option value="CAMBIO_ESTADO">CAMBIO_ESTADO</option>
                                <option value="PROYECTO_COMPLETADO">PROYECTO_COMPLETADO</option>
                                <option value="TAREA_COMPLETADA">TAREA_COMPLETADA</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="filtroTabla">Filtrar por Tabla</label>
                            <select class="form-control" id="filtroTabla">
                                <option value="">Todas las tablas</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="filtroUsuario">Filtrar por Usuario</label>
                            <select class="form-control" id="filtroUsuario">
                                <option value="">Todos los usuarios</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover" id="tablaLogs">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Usuario</th>
                                <th>Acción</th>
                                <th>Tabla</th>
                                <th>Registro ID</th>
                                <th width="100" class="text-center">Detalles</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="modalDetalles" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalles de la Actividad</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="border-bottom pb-2">Información de la Acción</h6>
                        <p><strong>Acción:</strong> <span id="detalleAccion"></span></p>
                        <p><strong>Tabla:</strong> <span id="detalleTabla"></span></p>
                        <p><strong>Registro ID:</strong> <span id="detalleRegistroId"></span></p>
                        <p><strong>Fecha:</strong> <span id="detalleFecha"></span></p>
                        <p><strong>Usuario:</strong> <span id="detalleUsuario"></span></p>
                        <p><strong>IP:</strong> <span id="detalleIp"></span></p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="border-bottom pb-2">Datos Modificados</h6>
                        <div id="detalleCambios"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script src="<?= asset('build/js/api/logs_actividas.js') ?>"></script>