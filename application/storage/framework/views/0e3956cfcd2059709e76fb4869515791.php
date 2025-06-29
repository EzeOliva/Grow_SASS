 <?php $__env->startSection('content'); ?>
<!--signup-->
<div class="login-logo m-t-30 p-b-5">
    <a href="javascript:void(0)" class="text-center db">
        <img src="<?php echo e(runtimeLogoLarge()); ?>" alt="Home">
    </a>
</div>

<div class="login-box m-t-20">
    <form class="form-horizontal form-material" id="loginForm">
        <div class="title">
            <h4 class="box-title m-t-10 text-center"><?php echo e(cleanLang(__('lang.sign_in_to_your_account'))); ?></h4>
            <div class="text-center  m-b-20 ">
            </div>
        </div>
        <div class="form-group">
            <div class="col-xs-12">
                <input class="form-control" type="text" name="email" id="email" placeholder="<?php echo e(cleanLang(__('lang.email'))); ?>">
            </div>
        </div>
        <div class="form-group">
            <div class="col-xs-12">
                <input class="form-control" type="password" name="password" id="password" placeholder="<?php echo e(cleanLang(__('lang.password'))); ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="custom-control custom-checkbox cursor-pointer">
                <input type="checkbox" class="custom-control-input" name="remember_me" checked="checked">
                <span class="custom-control-indicator"></span>
                <span class="custom-control-description"><?php echo e(cleanLang(__('lang.remember_me'))); ?></span>
            </label>
        </div>
        <div class="form-group row p-t-10 p-b-10">
            <div class="col-md-12">
                <a href="<?php echo e(url('app-admin/forgotpassword')); ?>" id="to-recover" class="text-dark pull-right js-toggle-login-forms"
                    data-target="login-forms-forgot">
                    <i class="fa fa-lock m-r-5"></i> <?php echo e(cleanLang(__('lang.forgot_password'))); ?></a>
            </div>
        </div>
        <div class="form-group text-center p-b-10">
            <div class="col-xs-12">
                <button class="btn btn-info btn-lg btn-block" id="loginSubmitButton"
                    data-button-loading-annimation="yes" data-button-disable-on-click="yes"
                    data-url="<?php echo e(url('app-admin/login?action=initial')); ?>" data-ajax-type="POST" type="submit"><?php echo e(cleanLang(__('lang.continue'))); ?></button>
            </div>
        </div>
        <?php if(config('system.settings_clients_registration') == 'enabled'): ?>
        <div class="form-group m-b-0">
            <div class="col-sm-12 text-center">
                <?php echo e(cleanLang(__('lang.dont_have_an_account'))); ?>

                <a href="<?php echo e(url('app-admin/signup')); ?>" class="text-info m-l-5 js-toggle-login-forms"
                    data-target="login-forms-signup">
                    <b><?php echo e(cleanLang(__('lang.sign_up'))); ?></b>
                </a>
                </p>
            </div>
        </div>
        <?php endif; ?>
    </form>
</div>

<div class="login-background">
    <div class="x-left p-b-80">
        <img src="<?php echo e(url('/')); ?>/public/images/landlord-login.svg"  class="login-images" />
    </div>
    <div class="x-right  p-b-80 hidden">
        <img src="<?php echo e(url('/')); ?>/public/images/landlord-login.svg"  class="login-images" />
    </div>
</div>
<!--signup-->
<?php $__env->stopSection(); ?>
<?php echo $__env->make('landlord.layout.wrapperplain', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/tasklist/public_html/application/resources/views/landlord/authentication/login.blade.php ENDPATH**/ ?>