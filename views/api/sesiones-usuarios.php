<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Registro de Sesiones</h1>
            </div>
            <div class="col-sm-6">
                <button type="button" class="btn btn-info float-right" id="btnRefrescar">
                    <i class="bi bi-arrow-clockwise"></i> Refrescar
                </button>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Historial de Sesiones de Usuarios</h3>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="filtroUsuario">Filtrar por Usuario</label>
                            <select class="form-control" id="filtroUsuario">
                                <option value="">Todos los usuarios</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="filtroEstado">Estado de Sesión</label>
                            <select class="form-control" id="filtroEstado">
                                <option value="">Todas</option>
                                <option value="activa">Activas</option>
                                <option value="cerrada">Cerradas</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="filtroFecha">Fecha</label>
                            <input type="date" class="form-control" id="filtroFecha">
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover" id="tablaSesiones">
                        <thead>
                            <tr>
                                <th>Usuario</th>
                                <th>IP</th>
                                <th>User Agent</th>
                                <th>Inicio Sesión</th>
                                <th>Fin Sesión</th>
                                <th>Estado</th>
                                <th width="100" class="text-center">Acciones</th>
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

<div class="modal fade" id="modalDetalleSesion" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalle de Sesión</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p><strong>Usuario:</strong> <span id="detalleUsuario"></span></p>
                <p><strong>IP:</strong> <span id="detalleIp"></span></p>
                <p><strong>User Agent:</strong> <span id="detalleUserAgent"></span></p>
                <p><strong>Inicio:</strong> <span id="detalleInicio"></span></p>
                <p><strong>Fin:</strong> <span id="detalleFin"></span></p>
                <p><strong>Token:</strong> <code id="detalleToken"></code></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script src="<?= asset('build/js/api/sesiones-usuarios.js') ?>"></script>