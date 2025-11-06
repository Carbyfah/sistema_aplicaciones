<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Gestión de Documentos</h1>
            </div>
            <div class="col-sm-6">
                <button type="button" class="btn btn-primary float-right" id="btnNuevoDocumento">
                    <i class="bi bi-plus-circle"></i> Nuevo Documento
                </button>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Listado de Documentos</h3>
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
                    <div class="col-md-6">
                        <div class="form-group mb-0">
                            <select class="form-control" id="filtroCategoria">
                                <option value="">Todas las categorías</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover" id="tablaDocumentos">
                        <thead>
                            <tr>
                                <th>Título</th>
                                <th>Categoría</th>
                                <th>Proyecto</th>
                                <th>Tipo</th>
                                <th>Fecha</th>
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

<div class="modal fade" id="modalDocumento" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDocumentoTitulo">Nuevo Documento</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="formDocumento" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="id_documentos" name="id_documentos">

                    <div class="form-group">
                        <label for="documentos_nombre">Título *</label>
                        <input type="text" class="form-control" id="documentos_nombre"
                            name="documentos_nombre" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="categorias_documentos_id_categorias_documentos">Categoría *</label>
                                <select class="form-control" id="categorias_documentos_id_categorias_documentos"
                                    name="categorias_documentos_id_categorias_documentos" required>
                                    <option value="">Seleccione una categoría</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ordenes_aplicaciones_id_ordenes_aplicaciones">Proyecto *</label>
                                <select class="form-control" id="ordenes_aplicaciones_id_ordenes_aplicaciones"
                                    name="ordenes_aplicaciones_id_ordenes_aplicaciones">
                                    <option value="">Sin proyecto asociado</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="documentos_archivo">Archivo</label>
                        <input type="file" class="form-control-file" id="documentos_archivo"
                            name="documentos_archivo">
                        <small class="form-text text-muted">
                            Formatos permitidos: PDF
                        </small>
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

<script src="<?= asset('build/js/api/documentos.js') ?>"></script>