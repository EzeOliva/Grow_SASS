<!-- ============================================================== -->
<!-- Left Sidebar - style you can find in sidebar.scss  -->
<!-- ============================================================== -->
<aside class="left-sidebar" id="js-trigger-nav-team">
    <!-- Sidebar scroll-->
    <div class="scroll-sidebar" id="main-scroll-sidebar">
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav" id="main-sidenav">
            <ul id="sidebarnav">



                <!--home-->
                <li class="sidenav-menu-item <?php echo e($page['mainmenu_home'] ?? ''); ?> menu-tooltip menu-with-tooltip"
                    title="<?php echo e(cleanLang(__('lang.home'))); ?>">
                    <a class="waves-effect waves-dark" href="/app-admin/home" aria-expanded="false" target="_self">
                        <i class="ti-home"></i>
                        <span class="hide-menu"><?php echo e(cleanLang(__('lang.dashboard'))); ?>

                        </span>
                    </a>
                </li>

                <!--customer-->
                <li class="sidenav-menu-item <?php echo e($page['mainmenu_customers'] ?? ''); ?> menu-tooltip menu-with-tooltip"
                    title="<?php echo e(cleanLang(__('lang.customers'))); ?>">
                    <a class="waves-effect waves-dark" href="/app-admin/customers" aria-expanded="false" target="_self">
                        <i class="sl-icon-people">
                            <?php if(!config('app.application_demo_mode') && config('system.count_tenant_email_config_status') > 0 && config('customer_defaults.defaults_email_delivery') == 'smtp_and_sendmail'): ?>
                            <span class="notify email-blinking-icon-table" id="menu_tenant_email_config_status"> <span
                                    class="heartbit"></span> <span class="point"></span> </span>
                            <?php endif; ?>
                        </i>
                        <span class="hide-menu"><?php echo e(cleanLang(__('lang.customers'))); ?>

                        </span>
                    </a>
                </li>



                <!--packages-->
                <li class="sidenav-menu-item <?php echo e($page['mainmenu_packages'] ?? ''); ?> menu-tooltip menu-with-tooltip"
                    title="<?php echo e(cleanLang(__('lang.packages'))); ?>">
                    <a class="waves-effect waves-dark" href="/app-admin/packages" aria-expanded="false" target="_self">
                        <i class="sl-icon-diamond"></i>
                        <span class="hide-menu"><?php echo e(cleanLang(__('lang.packages'))); ?>

                        </span>
                    </a>
                </li>


                <!--subscriptions-->
                <li class="sidenav-menu-item <?php echo e($page['mainmenu_subscriptions'] ?? ''); ?> menu-tooltip menu-with-tooltip"
                    title="<?php echo e(cleanLang(__('lang.subscriptions'))); ?>">
                    <a class="waves-effect waves-dark" href="/app-admin/subscriptions" aria-expanded="false"
                        target="_self">
                        <i class="ti-reload"></i>
                        <span class="hide-menu"><?php echo e(cleanLang(__('lang.subscriptions'))); ?>

                        </span>
                    </a>
                </li>


                <!--payments-->
                <li data-modular-id="main_menu_team_clients"
                    class="sidenav-menu-item <?php echo e($page['mainmenu_payments'] ?? ''); ?>">
                    <a class="has-arrow waves-effect waves-dark" href="javascript:void(0);" aria-expanded="false">
                        <i class="ti-credit-card"></i>
                        <span class="hide-menu"><?php echo app('translator')->get('lang.payments'); ?>
                        </span>
                    </a>
                    <ul aria-expanded="false" class="collapse">
                        <li class="sidenav-submenu <?php echo e($page['submenu_online'] ?? ''); ?>" id="submenu_online">
                            <a href="/app-admin/payments"
                                class="<?php echo e($page['submenu_online'] ?? ''); ?>"><?php echo app('translator')->get('lang.online'); ?></a>
                        </li>
                        <li class="sidenav-submenu <?php echo e($page['submenu_offline'] ?? ''); ?>" id="submenu_offline">
                            <a href="/app-admin/offline-payments"
                                class="<?php echo e($page['submenu_offline'] ?? ''); ?>"><?php echo app('translator')->get('lang.offline'); ?></a>
                        </li>
                    </ul>
                </li>


                <!--blogs-->
                <li class="sidenav-menu-item hidden <?php echo e($page['mainmenu_blogs'] ?? ''); ?> menu-tooltip menu-with-tooltip"
                    title="<?php echo e(cleanLang(__('lang.blogs'))); ?>">
                    <a class="waves-effect waves-dark" href="/app-admin/blogs" aria-expanded="false" target="_self">
                        <i class="sl-icon-docs"></i>
                        <span class="hide-menu"><?php echo e(cleanLang(__('lang.blogs'))); ?>

                        </span>
                    </a>
                </li>


                <!--events-->
                <li class="sidenav-menu-item <?php echo e($page['mainmenu_events'] ?? ''); ?> menu-tooltip menu-with-tooltip"
                    title="<?php echo e(cleanLang(__('lang.events'))); ?>">
                    <a class="waves-effect waves-dark" href="/app-admin/events" aria-expanded="false" target="_self">
                        <i class="ti-time"></i>
                        <span class="hide-menu"><?php echo e(cleanLang(__('lang.events'))); ?>

                        </span>
                    </a>
                </li>

                <!--team-->
                <li class="sidenav-menu-item <?php echo e($page['mainmenu_team'] ?? ''); ?> menu-tooltip menu-with-tooltip"
                    title="<?php echo app('translator')->get('lang.team'); ?>">
                    <a class="waves-effect waves-dark" href="/app-admin/team" aria-expanded="false" target="_self">
                        <i class="sl-icon-user-follow"></i>
                        <span class="hide-menu"><?php echo app('translator')->get('lang.team'); ?>
                        </span>
                    </a>
                </li>

                <!--frontend-->
                <li class="sidenav-menu-item <?php echo e($page['mainmenu_frontend'] ?? ''); ?> menu-tooltip menu-with-tooltip"
                    title="<?php echo e(cleanLang(__('lang.frontend'))); ?>">
                    <a class="waves-effect waves-dark" href="/app-admin/frontend/start" aria-expanded="false"
                        target="_self">
                        <i class="sl-icon-picture"></i>
                        <span class="hide-menu"><?php echo e(cleanLang(__('lang.frontend'))); ?>

                        </span>
                    </a>
                </li>

                <!--settings-->
                <li class="sidenav-menu-item <?php echo e($page['mainmenu_settings'] ?? ''); ?> menu-tooltip menu-with-tooltip"
                    title="<?php echo e(cleanLang(__('lang.settings'))); ?>">
                    <a class="waves-effect waves-dark" href="/app-admin/settings/general" aria-expanded="false"
                        target="_self">
                        <i class="sl-icon-settings"></i>
                        <span class="hide-menu"><?php echo e(cleanLang(__('lang.settings'))); ?>

                        </span>
                    </a>
                </li>


            </ul>
        </nav>
        <!-- End Sidebar navigation -->
    </div>
    <!-- End Sidebar scroll-->
</aside><?php /**PATH /home/tasklist/public_html/application/resources/views/landlord/layout/leftmenu.blade.php ENDPATH**/ ?>