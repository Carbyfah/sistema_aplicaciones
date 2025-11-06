<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Notificaciones</h1>
            </div>
            <div class="col-sm-6">
                <button type="button" class="btn btn-primary float-right" id="btnMarcarTodasLeidas">
                    <i class="bi bi-check-all"></i> Marcar todas como leídas
                </button>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Centro de Notificaciones</h3>
            </div>
            <div class="card-body">
                <ul class="nav nav-tabs" id="notificacionesTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="tab-no-leidas" data-toggle="pill" href="#no-leidas" role="tab">
                            No leídas <span class="badge badge-danger" id="contadorNoLeidas">0</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="tab-todas" data-toggle="pill" href="#todas" role="tab">
                            Todas
                        </a>
                    </li>
                </ul>
                <div class="tab-content p-3" id="notificacionesTabContent">
                    <div class="tab-pane fade show active" id="no-leidas" role="tabpanel">
                        <div id="listaNoLeidas" class="list-group">
                            <div class="text-center p-4 text-muted">
                                <i class="fas fa-spinner fa-spin fa-2x mb-3"></i>
                                <p>Cargando notificaciones...</p>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="todas" role="tabpanel">
                        <div id="listaTodas" class="list-group">
                            <div class="text-center p-4 text-muted">
                                <i class="fas fa-spinner fa-spin fa-2x mb-3"></i>
                                <p>Cargando notificaciones...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="<?= asset('build/js/api/notificaciones.js') ?>"></script>