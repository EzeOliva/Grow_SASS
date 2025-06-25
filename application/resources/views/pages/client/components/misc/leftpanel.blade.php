<!-- Column -->
<div class="card">
    <!--has logo-->
    @if (isset($client['client_logo_folder']) && $client['client_logo_folder'] != '')
        <div class="card-body profile_header">
            <img
                src="{{ url('/') }}/storage/logos/clients/{{ $client['client_logo_folder'] ?? '0' }}/{{ $client['client_logo_filename'] ?? '' }}">
        </div>
    @else
        <!--no logo -->
        <div class="card-body profile_header client logo-text">
            {{ $client->client_company_name }}
        </div>
    @endif
    <div class="card-body p-t-0 p-b-0">
        @if (auth()->user()->is_team)
            <div>
                <small class="text-muted">{{ cleanLang(__('lang.client_name')) }}</small>
                <h6 class="m-b-15">{{ $client->client_company_name }}</h6>
                <small class="text-muted">{{ cleanLang(__('lang.telephone')) }}</small>
                <h6 class="m-b-15">{{ $client->client_phone }}</h6>
                <small class="text-muted">{{ cleanLang(__('lang.account_owner')) }}</small>
                <div class="m-b-15"><img src="{{ getUsersAvatar($owner->avatar_directory, $owner->avatar_filename) }}"
                        alt="user" class="img-circle avatar-xsmall">
                    <a href="javascript:void(0);"
                        class="edit-add-modal-button js-ajax-ux-request reset-target-modal-form" data-toggle="modal"
                        data-target="#commonModal" data-url="{{ url('contacts/' . $owner->id) }}"
                        data-loading-target="commonModalBody" data-modal-title="" data-modal-size="modal-md"
                        data-header-close-icon="hidden" data-header-extra-close-icon="visible"
                        data-footer-visibility="hidden"
                        data-action-ajax-loading-target="commonModalBody">{{ $owner->first_name }}
                        {{ $owner->last_name }}
                    </a>
                </div>
                <small class="text-muted">{{ cleanLang(__('lang.category')) }}</small>
                <div class="p-b-15">
                    <span
                        class="badge badge-pill badge-primary p-t-4 p-l-12 p-r-12">{{ $client->category_name }}</span>
                </div>
                <small class="text-muted">{{ cleanLang(__('lang.account_status')) }}</small>
                <div class="p-b-15">
                    @if ($client->client_status == 'active')
                        <span
                            class="badge badge-pill badge-success p-t-4 p-l-12 p-r-12">{{ cleanLang(__('lang.active')) }}</span>
                    @else
                        <span
                            class="badge badge-pill badge-danger p-t-4 p-l-12 p-r-12">{{ cleanLang(__('lang.suspended')) }}</span>
                    @endif
                </div>

                <small class="text-muted">{{ cleanLang(__('lang.tags')) }}</small>
                <div class="l-h-24">
                    @foreach ($client->tags as $tag)
                        <span class="label label-rounded label-default tag p-t-3 p-b-3">{{ $tag->tag_title }}</span>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
    <div>
        <hr>
    </div>
    <div class="card-body p-t-0 p-b-0">
        <div>
            <table class="table no-border m-b-0">
                <tbody>
                    <!--invoices-->
                    @if (config('visibility.role_invoices'))
                        <tr>
                            <td class="p-l-0 p-t-5" id="fx-client-left-panel-invoices">
                                {{ cleanLang(__('lang.invoices')) }}
                            </td>
                            <td class="font-medium p-r-0 p-t-5">
                                {{ runtimeMoneyFormat($client->sum_invoices_all) }}
                                <div class="progress">
                                    <div class="progress-bar bg-info  w-100 h-px-3" role="progressbar"
                                        aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </td>
                        </tr>
                    @endif
                    <!--payments-->
                    @if (config('visibility.role_payments'))
                        <tr>
                            <td class="p-l-0 p-t-5">{{ cleanLang(__('lang.payments')) }}</td>
                            <td class="font-medium p-r-0 p-t-5">{{ runtimeMoneyFormat($client->sum_all_payments) }}
                                <div class="progress">
                                    <div class="progress-bar bg-success w-100 h-px-3" role="progressbar"
                                        aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </td>
                        </tr>
                    @endif
                    <!--completed projects-->
                    <tr>
                        <td class="p-l-0 p-t-5">{{ cleanLang(__('lang.completed_projects')) }}</td>
                        <td class="font-medium p-r-0 p-t-5">{{ $client->count_projects_completed }}
                            <div class="progress">
                                <div class="progress-bar bg-warning  w-100 h-px-3" role="progressbar" aria-valuenow="25"
                                    aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </td>
                    </tr>
                    <!--open projects-->
                    <tr>
                        <td class="p-l-0 p-t-5">{{ cleanLang(__('lang.open_projects')) }}</td>
                        <td class="font-medium p-r-0 p-t-5">{{ $client->count_projects_pending }}
                            <div class="progress">
                                <div class="progress-bar bg-danger w-100 h-px-3" role="progressbar" aria-valuenow="25"
                                    aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div>
        <hr>
    </div>


    @if (config('visibility.client_show_custom_fields'))
        <!--CUSTOMER FIELDS-->
        <div class="m-t-10 m-b-10">
            <hr>
        </div>
        <div class="card-body p-t-0 p-b-0">
            @foreach ($fields as $field)
                @if ($field->customfields_show_client_page == 'yes')
                    <div class="x-each-field m-b-18">
                        <div class="panel-label p-b-3">{{ $field->customfields_title }}
                        </div>
                        <div class="x-content">
                            {{ strip_tags(customFieldValue($field->customfields_name, $client, $field->customfields_datatype)) }}
                        </div>
                    </div>
                @endif
            @endforeach

            @if (config('app.application_demo_mode'))
                <!--DEMO INFO-->
                <div class="alert alert-info">
                    <h5 class="text-info"><i class="sl-icon-info"></i> Demo Info</h5>
                    These are custom fields. You can change them or <a
                        href="{{ url('app/settings/customfields/projects') }}">create your own.</a>
                </div>
            @endif

        </div>
    @endif


    {{-- Total client healthy with feedbacks and expectations --}}

    <div class="container" style="max-width: 420px;">
        <h3 class="text-center mb-4">{{ __('lang.customer_success') }}</h3>

        {{-- Expectation Compliance Card --}}
        <div class="card customer-success-block mb-3">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-check-circle text-success mr-2"></i>
                    <span>{{ __('lang.expectation_compliance_percent') }}</span>
                </div>
                <div class="font-weight-bold text-success">{{ $stats['expectation_percent'] }}%</div>
            </div>
        </div>

        {{-- Average Feedback Card --}}
        <div class="card customer-success-block mb-3">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-star text-warning mr-2"></i>
                    <span>{{ __('lang.average_feedback') }}</span>
                </div>
                <div class="font-weight-bold text-primary">{{ $stats['average_feedback'] }}</div>
            </div>
        </div>

        {{-- Recent Comments Card --}}
        <div class="card customer-success-block mb-3">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <i class="fas fa-comment-dots text-info mr-2"></i>
                    <span>{{ __('lang.latest_comments') }}</span>
                </div>
                <div class="pl-4">
                    @forelse ($stats['recent_comments'] as $comment)
                        <div>“{{ $comment }}”</div>
                    @empty
                        <div class="text-muted">{{ __('lang.no_recent_comments') }}</div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Health Status Card --}}
        @php
            $healthLabels = [
                'green' => '🟢 Saludable',
                'yellow' => '🟡 En riesgo',
                'red' => '🔴 Crítico'
            ];
            $healthStatusLabel = $healthLabels[$stats['health_status']] ?? ucfirst($stats['health_status']);
        @endphp

        <div class="card customer-success-block mb-3">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-heart 
                        @if ($stats['health_status'] === 'green') text-success
                        @elseif($stats['health_status'] === 'yellow') text-warning
                        @else text-danger 
                        @endif mr-2">
                    </i>
                    <span>{{ __('lang.client_health') }}</span>
                </div>
                <div class="font-weight-bold 
                    @if ($stats['health_status'] === 'green') text-success
                    @elseif($stats['health_status'] === 'yellow') text-warning
                    @else text-danger 
                    @endif">
                    {{ $healthStatusLabel }}
                </div>
            </div>
        </div>

    </div>
</div>
<!-- Column -->
