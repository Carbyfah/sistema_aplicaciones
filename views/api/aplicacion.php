<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Gestión de Proyectos</h1>
            </div>
            <div class="col-sm-6">
                <button type="button" class="btn btn-primary float-right" id="btnNuevoProyecto">
                    <i class="fas fa-plus-circle"></i> Nuevo Proyecto
                </button>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Listado de Proyectos</h3>
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
                    <table class="table table-bordered table-striped table-hover" id="tablaProyectos">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Descripción Corta</th>
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

<div class="modal fade" id="modalProyecto" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalProyectoTitulo">Nuevo Proyecto</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="formProyecto">
                <div class="modal-body">
                    <input type="hidden" id="id_aplicacion" name="id_aplicacion">

                    <div class="form-group">
                        <label for="aplicacion_nombre">Nombre del Proyecto *</label>
                        <input type="text" class="form-control" id="aplicacion_nombre"
                            name="aplicacion_nombre" required>
                    </div>

                    <div class="form-group">
                        <label for="aplicacion_desc_corta">Descripción Corta *</label>
                        <input type="text" class="form-control" id="aplicacion_desc_corta"
                            name="aplicacion_desc_corta" maxlength="150" required>
                        <small class="form-text text-muted">Máximo 150 caracteres</small>
                    </div>

                    <div class="form-group">
                        <label for="aplicacion_larga">Descripción Detallada</label>
                        <textarea class="form-control" id="aplicacion_larga"
                            name="aplicacion_larga" rows="5"></textarea>
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

<script src="<?= asset('build/js/api/aplicacion.js') ?>"></script>