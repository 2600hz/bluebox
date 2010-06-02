<div id="user_container_header" class="user login_containter module_header">
    <h2><span class="helptip"></span><?php echo __('Welcome to ' . Kohana::config('core.product_name')); ?></h2>
</div>

<div id="login_container_form" class="user login_containter">
    <?php echo form::open(); ?>

    <?php if (isset($views)) echo subview::render($views, 'login'); ?>

    <?php echo form::close(); ?>
</div>

<?php if (Kohana::config('config.allow_registrations')) : ?>
    <div id="register_container_form" class="user register_containter">
        <?php echo form::open(); ?>

        <?php if (isset($views)) echo subview::render($views, 'register'); ?>

        <?php echo form::close(); ?>
    </div>
<?php endif; ?>

