<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Gestión de Personal</h1>
            </div>
            <div class="col-sm-6">
                <button type="button" class="btn btn-primary float-right" id="btnNuevaPersona">
                    <i class="bi bi-plus-circle"></i> Nueva Persona
                </button>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Listado de Personal</h3>
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
                    <table class="table table-bordered table-striped table-hover" id="tablaPersonal">
                        <thead>
                            <tr>
                                <th>Nombre Completo</th>
                                <th>Identidad</th>
                                <th>Teléfono</th>
                                <th>Correo</th>
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

<div class="modal fade" id="modalPersona" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPersonaTitulo">Nueva Persona</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="formPersona">
                <div class="modal-body">
                    <input type="hidden" id="id_persona" name="id_persona">

                    <div class="form-group">
                        <label for="persona_nombres">Nombres *</label>
                        <input type="text" class="form-control" id="persona_nombres"
                            name="persona_nombres" required>
                    </div>

                    <div class="form-group">
                        <label for="persona_apellidos">Apellidos *</label>
                        <input type="text" class="form-control" id="persona_apellidos"
                            name="persona_apellidos" required>
                    </div>

                    <div class="form-group">
                        <label for="persona_identidad">Identidad *</label>
                        <input type="text" class="form-control" id="persona_identidad"
                            name="persona_identidad" required>
                    </div>

                    <div class="form-group">
                        <label for="persona_telefono">Teléfono</label>
                        <input type="text" class="form-control" id="persona_telefono"
                            name="persona_telefono">
                    </div>

                    <div class="form-group">
                        <label for="persona_correo">Correo</label>
                        <input type="email" class="form-control" id="persona_correo"
                            name="persona_correo">
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

<script src="<?= asset('build/js/api/persona.js') ?>"></script>