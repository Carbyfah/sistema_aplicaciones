<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Gestión de Usuarios</h1>
            </div>
            <div class="col-sm-6">
                <button type="button" class="btn btn-primary float-right" id="btnNuevoUsuario">
                    <i class="bi bi-plus-circle"></i> Nuevo Usuario
                </button>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Listado de Usuarios</h3>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="toggleEliminados">
                            <label class="custom-control-label" for="toggleEliminados">
                                Mostrar registros eliminados
                            </label>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover" id="tablaUsuarios">
                        <thead>
                            <tr>
                                <th>Usuario</th>
                                <th>Persona</th>
                                <th>Rol</th>
                                <th>Último Acceso</th>
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

<div class="modal fade" id="modalUsuario" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalUsuarioTitulo">Nuevo Usuario</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="formUsuario">
                <div class="modal-body">
                    <input type="hidden" id="id_usuarios" name="id_usuarios">

                    <div class="form-group">
                        <label for="usuarios_nombre">Nombre de Usuario *</label>
                        <input type="text" class="form-control" id="usuarios_nombre"
                            name="usuarios_nombre" required>
                    </div>

                    <div class="form-group">
                        <label for="usuarios_password">Contraseña <span id="passwordRequired">*</span></label>
                        <input type="password" class="form-control" id="usuarios_password"
                            name="usuarios_password">
                        <small class="form-text text-muted">
                            Dejar en blanco para mantener la contraseña actual (solo en edición)
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="persona_id_persona">Persona Asociada *</label>
                        <select class="form-control" id="persona_id_persona"
                            name="persona_id_persona" required>
                            <option value="">Seleccione una persona</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="<?= asset('build/js/api/usuarios.js') ?>"></script>