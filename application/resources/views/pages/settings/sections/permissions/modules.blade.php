<!-- Available Modules Page -->

<!-- ============================================================== -->
<!-- Available Modules Page -->
<!-- ============================================================== -->

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">{{ cleanLang(__('lang.available_modules')) }}</h4>
                <p class="card-subtitle mb-4">{{ cleanLang(__('lang.manage_module_permissions_and_role_access')) }}</p>

                <div class="row mt-4">
                    @foreach($modules as $module)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card border">
                            <div class="card-body text-center">
                                <div class="mb-3">
                                    <i class="{{ $module['icon'] }} fa-3x text-primary"></i>
                                </div>
                                <h5 class="card-title">{{ $module['name'] }}</h5>
                                <p class="card-text text-muted">{{ $module['description'] }}</p>
                                
                                <!-- Role Access Indicators -->
                                <div class="mt-3">
                                    <h6 class="text-muted mb-2">{{ cleanLang(__('lang.role_access')) }}:</h6>
                                    <div class="d-flex justify-content-center gap-3">
                                        <div class="text-center">
                                            <div class="badge bg-success rounded-circle p-2 mb-1">
                                                <i class="fas fa-check text-white"></i>
                                            </div>
                                            <small class="d-block text-muted">{{ cleanLang(__('lang.admin')) }}</small>
                                        </div>
                                        <div class="text-center">
                                            <div class="badge bg-success rounded-circle p-2 mb-1">
                                                <i class="fas fa-check text-white"></i>
                                            </div>
                                            <small class="d-block text-muted">{{ cleanLang(__('lang.manager')) }}</small>
                                        </div>
                                        <div class="text-center">
                                            <div class="badge bg-warning rounded-circle p-2 mb-1">
                                                <i class="fas fa-check text-white"></i>
                                            </div>
                                            <small class="d-block text-muted">{{ cleanLang(__('lang.agent')) }}</small>
                                        </div>
                                        <div class="text-center">
                                            <div class="badge bg-danger rounded-circle p-2 mb-1">
                                                <i class="fas fa-times text-white"></i>
                                            </div>
                                            <small class="d-block text-muted">{{ cleanLang(__('lang.viewer')) }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>


