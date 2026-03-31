<!-- Settings & Permissions - Main Page -->

<!-- ============================================================== -->
<!-- Settings & Permissions - Main Page -->
<!-- ============================================================== -->

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">{{ cleanLang(__('lang.settings_permissions')) }}</h4>
                <p class="card-subtitle mb-4">{{ cleanLang(__('lang.manage_module_permissions_and_role_access')) }}</p>

                <!-- Navigation Tabs -->
                <ul class="nav nav-tabs" id="permissionsTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="modules-tab" data-bs-toggle="tab" data-bs-target="#modules" type="button" role="tab" aria-controls="modules" aria-selected="true">
                            <i class="sl-icon-grid"></i> {{ cleanLang(__('lang.available_modules')) }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="roles-tab" data-bs-toggle="tab" data-bs-target="#roles" type="button" role="tab" aria-controls="roles" aria-selected="false">
                            <i class="sl-icon-people"></i> {{ cleanLang(__('lang.role_permissions')) }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="whatsapp-tab" data-bs-toggle="tab" data-bs-target="#whatsapp" type="button" role="tab" aria-controls="whatsapp" aria-selected="false">
                            <i class="fab fa-whatsapp"></i> {{ cleanLang(__('lang.whatsapp_permissions')) }}
                        </button>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content" id="permissionsTabsContent">
                    <!-- Available Modules Tab -->
                    <div class="tab-pane fade show active" id="modules" role="tabpanel" aria-labelledby="modules-tab">
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

                    <!-- Role Permissions Tab -->
                    <div class="tab-pane fade" id="roles" role="tabpanel" aria-labelledby="roles-tab">
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>{{ cleanLang(__('lang.role')) }}</th>
                                                <th>{{ cleanLang(__('lang.whatsapp_access')) }}</th>
                                                <th>{{ cleanLang(__('lang.manage_messages')) }}</th>
                                                <th>{{ cleanLang(__('lang.assign_tickets')) }}</th>
                                                <th>{{ cleanLang(__('lang.reply_clients')) }}</th>
                                                <th>{{ cleanLang(__('lang.view_only')) }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($roles as $role)
                                            <tr>
                                                <td>
                                                    <strong>{{ $role->role_name }}</strong>
                                                    @if($role->role_system == 'yes')
                                                        <span class="badge bg-primary ms-2">{{ cleanLang(__('lang.system')) }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($role->role_id == 1 || $role->role_id == 3)
                                                        <span class="badge bg-success">{{ cleanLang(__('lang.yes')) }}</span>
                                                    @else
                                                        <span class="badge bg-danger">{{ cleanLang(__('lang.no')) }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($role->role_id == 1)
                                                        <span class="badge bg-success">{{ cleanLang(__('lang.yes')) }}</span>
                                                    @else
                                                        <span class="badge bg-danger">{{ cleanLang(__('lang.no')) }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($role->role_id == 1)
                                                        <span class="badge bg-success">{{ cleanLang(__('lang.yes')) }}</span>
                                                    @else
                                                        <span class="badge bg-danger">{{ cleanLang(__('lang.no')) }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($role->role_id == 1 || $role->role_id == 3)
                                                        <span class="badge bg-success">{{ cleanLang(__('lang.yes')) }}</span>
                                                    @else
                                                        <span class="badge bg-danger">{{ cleanLang(__('lang.no')) }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($role->role_id == 2)
                                                        <span class="badge bg-warning">{{ cleanLang(__('lang.yes')) }}</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ cleanLang(__('lang.no')) }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- WhatsApp Permissions Tab -->
                    <div class="tab-pane fade" id="whatsapp" role="tabpanel" aria-labelledby="whatsapp-tab">
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <h5><i class="fab fa-whatsapp"></i> {{ cleanLang(__('lang.whatsapp_permissions_info')) }}</h5>
                                    <p class="mb-0">{{ cleanLang(__('lang.whatsapp_permissions_description')) }}</p>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <h5>{{ cleanLang(__('lang.permission_features')) }}</h5>
                                        <ul class="list-group">
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                {{ cleanLang(__('lang.access_whatsapp_pages')) }}
                                                <span class="badge bg-primary rounded-pill">{{ cleanLang(__('lang.admin_manager_agent')) }}</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                {{ cleanLang(__('lang.manage_messages')) }}
                                                <span class="badge bg-primary rounded-pill">{{ cleanLang(__('lang.admin_manager')) }}</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                {{ cleanLang(__('lang.assign_tickets')) }}
                                                <span class="badge bg-primary rounded-pill">{{ cleanLang(__('lang.admin_manager')) }}</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                {{ cleanLang(__('lang.reply_to_clients')) }}
                                                <span class="badge bg-primary rounded-pill">{{ cleanLang(__('lang.admin_manager_agent')) }}</span>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h5>{{ cleanLang(__('lang.role_summary')) }}</h5>
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="mb-3">
                                                    <strong>{{ cleanLang(__('lang.admin')) }}:</strong>
                                                    <span class="text-success">{{ cleanLang(__('lang.full_access')) }}</span>
                                                </div>
                                                <div class="mb-3">
                                                    <strong>{{ cleanLang(__('lang.manager')) }}:</strong>
                                                    <span class="text-success">{{ cleanLang(__('lang.full_access')) }}</span>
                                                </div>
                                                <div class="mb-3">
                                                    <strong>{{ cleanLang(__('lang.agent')) }}:</strong>
                                                    <span class="text-warning">{{ cleanLang(__('lang.reply_only')) }}</span>
                                                </div>
                                                <div class="mb-3">
                                                    <strong>{{ cleanLang(__('lang.viewer')) }}:</strong>
                                                    <span class="text-danger">{{ cleanLang(__('lang.no_access')) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


