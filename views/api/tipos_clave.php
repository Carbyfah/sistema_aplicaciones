<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Gestión de Tipos de Clave</h1>
            </div>
            <div class="col-sm-6">
                <button type="button" class="btn btn-primary float-right" id="btnNuevoTipoClave">
                    <i class="fas fa-plus-circle"></i> Nuevo Tipo de Clave
                </button>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Listado de Tipos de Clave</h3>
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
                    <table class="table table-bordered table-striped table-hover" id="tablaTiposClave">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Descripción</th>
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

<div class="modal fade" id="modalTipoClave" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTipoClaveTitulo">Nuevo Tipo de Clave</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="formTipoClave">
                <div class="modal-body">
                    <input type="hidden" id="id_tipo_clave" name="id_tipo_clave">

                    <div class="form-group">
                        <label for="tipos_clave_nombre">Nombre *</label>
                        <input type="text" class="form-control" id="tipos_clave_nombre"
                            name="tipos_clave_nombre" required placeholder="Ej: PRIMARY, FOREIGN, UNIQUE, INDEX">
                    </div>

                    <div class="form-group">
                        <label for="tipos_clave_descripcion">Descripción</label>
                        <textarea class="form-control" id="tipos_clave_descripcion"
                            name="tipos_clave_descripcion" rows="3"
                            placeholder="Describe el tipo de clave"></textarea>
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

<script src="<?= asset('build/js/api/tipos_clave.js') ?>"></script>