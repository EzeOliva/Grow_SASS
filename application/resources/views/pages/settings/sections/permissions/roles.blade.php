<!-- Role Permissions Page -->

<!-- ============================================================== -->
<!-- Role Permissions Page -->
<!-- ============================================================== -->

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">{{ cleanLang(__('lang.role_permissions')) }}</h4>
                <p class="card-subtitle mb-4">{{ cleanLang(__('lang.manage_role_based_permissions')) }}</p>

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
        </div>
    </div>
</div>


