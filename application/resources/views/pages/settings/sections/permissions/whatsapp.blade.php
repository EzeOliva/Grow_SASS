<!-- WhatsApp Permissions Page -->

<!-- ============================================================== -->
<!-- WhatsApp Permissions Page -->
<!-- ============================================================== -->

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">{{ cleanLang(__('lang.whatsapp_permissions')) }}</h4>
                <p class="card-subtitle mb-4">{{ cleanLang(__('lang.manage_whatsapp_permissions_and_access')) }}</p>

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

                        <!-- Detailed Permissions Table -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <h5>{{ cleanLang(__('lang.detailed_permissions')) }}</h5>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>{{ cleanLang(__('lang.role')) }}</th>
                                                <th>{{ cleanLang(__('lang.access_whatsapp_pages')) }}</th>
                                                <th>{{ cleanLang(__('lang.manage_messages')) }}</th>
                                                <th>{{ cleanLang(__('lang.assign_tickets')) }}</th>
                                                <th>{{ cleanLang(__('lang.reply_to_clients')) }}</th>
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
                </div>
            </div>
        </div>
    </div>
</div>


