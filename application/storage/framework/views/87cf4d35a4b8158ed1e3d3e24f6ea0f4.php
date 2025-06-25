<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>" id="meta-csrf" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title><?php echo e(config('system.settings_company_name')); ?></title>

    <!--BASEURL-->
    <base href="<?php echo e(url('/')); ?>" target="_self">

    <!--JQUERY & OTHER HEADER JS-->
    <script src="public/vendor/js/vendor.header.js?v=<?php echo e(config('system.versioning')); ?>"></script>

    <!--BOOTSTRAP-->
    <link href="public/vendor/css/bootstrap/bootstrap.min.css" rel="stylesheet">


    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="public/vendor/js/html5shiv/html5shiv.js"></script>
    <script src="public/vendor/js/respond/respond.min.js"></script>
    <![endif]-->

    <!--GOOGLE FONTS-->
    <link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,500,600,700" rel="stylesheet"
        type="text/css">


    <!--VENDORS CSS-->
    <link rel="stylesheet" href="public/vendor/css/vendor.css?v=<?php echo e(config('system.versioning')); ?>">

    <!--ICONS-->
    <link rel="stylesheet" href="public/vendor/fonts/growcrm-icons/styles.css?v=<?php echo e(config('system.versioning')); ?>">

    <!--THEME STYLE-->
    <link href="<?php echo e(config('theme.backend.selected_theme_css') ?? 'public/themes/default/css/style.css?v='); ?>"
        rel="stylesheet">

    <!--SAAS STYLE-->
    <link href="<?php echo e(config('theme.backend.selected_theme_saas_css') ?? 'public/themes/default/css/saas.css?v='); ?>"
        rel="stylesheet">

    <link href="public/css/custom.css?v=<?php echo e(config('system.versioning')); ?>" rel="stylesheet">

    <!--USERS CUSTON CSS FILE-->
    <link href="public/themes/landlord/css/landlord.css?v=<?php echo e(config('system.versioning')); ?>" rel="stylesheet">

    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16"
        href="storage/logos/app/<?php echo e(config('system.settings_favicon_landlord_filename')); ?>">

    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="public/images/favicon/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">


    <!--SET DYNAMIC VARIABLE IN JAVASCRIPT-->
    <script type="text/javascript">
        //name space & settings
        NX = (typeof NX == 'undefined') ? {} : NX;
        NXJS = (typeof NXJS == 'undefined') ? {} : NXJS;
        NXLANG = (typeof NXLANG == 'undefined') ? {} : NXLANG;
        NX.data = (typeof NX.data == 'undefined') ? {} : NX.data;

        //variables
        NX.system_type = "landlord";
        NX.site_url = "<?php echo e(url('/')); ?>";
        NX.app_admin_url = "app-admin/"; //including traling /
        NX.csrf_token = "<?php echo e(csrf_token()); ?>";
        NX.system_language = "<?php echo e(request('system_language')); ?>";
        NX.date_format = "<?php echo e(config('system.settings_system_date_format')); ?>";
        NX.date_picker_format = "<?php echo e(config('system.settings_system_datepicker_format')); ?>";
        NX.date_moment_format = "<?php echo e(runtimeMomentFormat(config('system.settings_system_date_format'))); ?>";
        NX.upload_maximum_file_size = "<?php echo e(config('system.settings_files_max_size_mb')); ?>";
        NX.settings_system_currency_symbol = "<?php echo e(config('system.settings_system_currency_symbol')); ?>";
        NX.settings_system_decimal_separator =
            "<?php echo e(runtimeCurrrencySeperators(config('system.settings_system_decimal_separator'))); ?>";
        NX.settings_system_thousand_separator =
            "<?php echo e(runtimeCurrrencySeperators(config('system.settings_system_thousand_separator'))); ?>";
        NX.settings_system_currency_position = "<?php echo e(config('system.settings_system_currency_position')); ?>";
        NX.show_action_button_tooltips = "<?php echo e(config('settings.show_action_button_tooltips')); ?>";
        NX.notification_position = "<?php echo e(config('settings.notification_position')); ?>";
        NX.notification_error_duration = "<?php echo e(config('settings.notification_error_duration')); ?>";
        NX.notification_success_duration = "<?php echo e(config('settings.notification_success_duration')); ?>";

        //javascript console debug modes
        NX.debug_javascript = "<?php echo e(config('app.debug_javascript')); ?>";

        //popover template
        NX.basic_popover_template = '<div class="popover card-popover" role="tooltip">' +
            '<span class="popover-close" onclick="$(this).closest(\'div.popover\').popover(\'hide\');" aria-hidden="true">' +
            '<i class="ti-close"></i></span>' +
            '<div class="popover-header"></div><div class="popover-body" id="popover-body"></div></div>';

        //lang - used in .js files
        NXLANG.delete_confirmation = "<?php echo e(cleanLang(__('lang.delete_confirmation'))); ?>";
        NXLANG.are_you_sure_delete = "<?php echo e(cleanLang(__('lang.are_you_sure_delete'))); ?>";
        NXLANG.cancel = "<?php echo e(cleanLang(__('lang.cancel'))); ?>";
        NXLANG.continue = "<?php echo e(cleanLang(__('lang.continue'))); ?>";
        NXLANG.file_too_big = "<?php echo e(cleanLang(__('lang.file_too_big'))); ?>";
        NXLANG.maximum = "<?php echo e(cleanLang(__('lang.maximum'))); ?>";
        NXLANG.generic_error = "<?php echo e(cleanLang(__('lang.error_request_could_not_be_completed'))); ?>";
        NXLANG.drag_drop_not_supported = "<?php echo e(cleanLang(__('lang.drag_drop_not_supported'))); ?>";
        NXLANG.use_the_button_to_upload = "<?php echo e(cleanLang(__('lang.use_the_button_to_upload'))); ?>";
        NXLANG.file_type_not_allowed = "<?php echo e(cleanLang(__('lang.file_type_not_allowed'))); ?>";
        NXLANG.cancel_upload = "<?php echo e(cleanLang(__('lang.cancel_upload'))); ?>";
        NXLANG.remove_file = "<?php echo e(cleanLang(__('lang.remove_file'))); ?>";
        NXLANG.maximum_upload_files_reached = "<?php echo e(cleanLang(__('lang.maximum_upload_files_reached'))); ?>";
        NXLANG.upload_maximum_file_size = "<?php echo e(cleanLang(__('lang.upload_maximum_file_size'))); ?>";
        NXLANG.upload_canceled = "<?php echo e(cleanLang(__('lang.upload_canceled'))); ?>";
        NXLANG.are_you_sure = "<?php echo e(cleanLang(__('lang.are_you_sure'))); ?>";
        NXLANG.image_dimensions_not_allowed = "<?php echo e(cleanLang(__('lang.image_dimensions_not_allowed'))); ?>";
        NXLANG.ok = "<?php echo e(cleanLang(__('lang.ok'))); ?>";
        NXLANG.cancel = "<?php echo e(cleanLang(__('lang.cancel'))); ?>";
        NXLANG.close = "<?php echo e(cleanLang(__('lang.close'))); ?>";
        NXLANG.system_default_category_cannot_be_deleted =
            "<?php echo e(cleanLang(__('lang.system_default_category_cannot_be_deleted'))); ?>";
        NXLANG.default_category = "<?php echo e(cleanLang(__('lang.default_category'))); ?>";
        NXLANG.select_atleast_one_item = "<?php echo e(cleanLang(__('lang.select_atleast_one_item'))); ?>";
        NXLANG.invalid_discount = "<?php echo e(cleanLang(__('lang.invalid_discount'))); ?>";
        NXLANG.add_lineitem_items_first = "<?php echo e(cleanLang(__('lang.add_lineitem_items_first'))); ?>";
        NXLANG.fixed = "<?php echo e(cleanLang(__('lang.fixed'))); ?>";
        NXLANG.percentage = "<?php echo e(cleanLang(__('lang.percentage'))); ?>";
        NXLANG.action_not_completed_errors_found = "<?php echo e(cleanLang(__('lang.action_not_completed_errors_found'))); ?>";
        NXLANG.selected_expense_is_already_on_invoice =
            "<?php echo e(cleanLang(__('lang.selected_expense_is_already_on_invoice'))); ?>";
        NXLANG.please_wait = "<?php echo e(cleanLang(__('lang.please_wait'))); ?>";
        NXLANG.invoice_time_unit = "<?php echo e(cleanLang(__('lang.time'))); ?>";

        //arrays to use generically
        NX.array_1 = [];
        NX.array_2 = [];
        NX.array_3 = [];
        NX.array_4 = [];
    </script>

    <!--boot js-->
    <script src="public/js/core/head.js?v=<?php echo e(config('system.versioning')); ?>"></script>

    <!--stripe payments js-->
    <?php if(@config('visibility.stripe_js')): ?>
    <script src="https://js.stripe.com/v3/"></script>
    <?php endif; ?>

    <!--razorpay payments js-->
    <?php if(@config('visibility.razorpay_js')): ?>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <?php endif; ?>
</head><?php /**PATH /home/tasklist/public_html/application/resources/views/landlord/layout/header.blade.php ENDPATH**/ ?>