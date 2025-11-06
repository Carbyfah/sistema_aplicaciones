<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Permisos de Usuario</h1>
            </div>
            <div class="col-sm-6">
                <a href="<?= $_ENV['APP_NAME'] ? '/' . $_ENV['APP_NAME'] : '' ?>/usuarios" class="btn btn-secondary float-right">
                    <i class="fas fa-arrow-left"></i> Volver a Usuarios
                </a>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Seleccionar Usuario</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label for="selectUsuario">Usuario:</label>
                    <select class="form-control" id="selectUsuario">
                        <option value="">-- Seleccione un usuario --</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="card" id="cardPermisos" style="display: none;">
            <div class="card-header">
                <h3 class="card-title">Permisos por Módulo</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-sm btn-success" id="btnMarcarTodos">
                        <i class="fas fa-check-double"></i> Marcar Todos
                    </button>
                    <button type="button" class="btn btn-sm btn-warning" id="btnDesmarcarTodos">
                        <i class="fas fa-times"></i> Desmarcar Todos
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Marca los permisos específicos que tendrá este usuario en cada módulo del sistema.
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-sm">
                        <thead class="thead-dark">
                            <tr>
                                <th width="50" class="text-center">
                                    <i class="fas fa-check-double" title="Marcar/Desmarcar Módulo"></i>
                                </th>
                                <th width="200">Módulo</th>
                                <th width="80" class="text-center">Ver</th>
                                <th width="80" class="text-center">Crear</th>
                                <th width="80" class="text-center">Editar</th>
                                <th width="80" class="text-center">Eliminar</th>
                                <th width="100" class="text-center">Excel</th>
                                <th width="100" class="text-center">PDF</th>
                            </tr>
                        </thead>
                        <tbody id="tablaPermisosBody">
                            <tr>
                                <td colspan="8" class="text-center">
                                    <i class="fas fa-spinner fa-spin"></i> Cargando módulos...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <button type="button" class="btn btn-primary" id="btnGuardarPermisos">
                    <i class="fas fa-save"></i> Guardar Permisos
                </button>
                <a href="<?= $_ENV['APP_NAME'] ? '/' . $_ENV['APP_NAME'] : '' ?>/usuarios" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </div>
        </div>
    </div>
</section>

<script src="<?= asset('build/js/api/usuarios-permisos.js') ?>"></script>