<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Intentos de Inicio de Sesión</h1>
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
                <h3 class="card-title">Registro de Intentos de Login</h3>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="filtroExitoso">Estado</label>
                            <select class="form-control" id="filtroExitoso">
                                <option value="">Todos</option>
                                <option value="1">Exitosos</option>
                                <option value="0">Fallidos</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="filtroUsuario">Usuario</label>
                            <input type="text" class="form-control" id="filtroUsuario" placeholder="Nombre de usuario">
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
                    <table class="table table-bordered table-striped table-hover" id="tablaIntentos">
                        <thead>
                            <tr>
                                <th>Fecha/Hora</th>
                                <th>Usuario</th>
                                <th>IP</th>
                                <th>Estado</th>
                                <th>Mensaje</th>
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

<div class="modal fade" id="modalDetalleIntento" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalle del Intento</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p><strong>Fecha:</strong> <span id="detalleFecha"></span></p>
                <p><strong>Usuario Intentado:</strong> <span id="detalleUsuario"></span></p>
                <p><strong>IP:</strong> <span id="detalleIp"></span></p>
                <p><strong>User Agent:</strong> <span id="detalleUserAgent"></span></p>
                <p><strong>Estado:</strong> <span id="detalleExitoso"></span></p>
                <p><strong>Mensaje:</strong> <span id="detalleMensaje"></span></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script src="<?= asset('build/js/api/intentos-login.js') ?>"></script>