<?php echo form::open_section('About Bluebox'); ?>
    <p>
    <?php
        echo __('Bluebox provides a modular approach to traditional telephony systems. Instead of pre-determined configuration screens and business logic, Bluebox allows you to install Applications and Plug-ins to custom tailor the system\'s behavior to your needs.');
    ?>
    </p>
<?php echo form::close_section(); ?>

<?php echo form::open_section('Terms and Conditions'); ?>

    <?php
          //  echo form::dropdown('language', $defaultLanguages, $defaultLanguage, 'onchange="this.form.submit();"');
    ?>

    <?php echo form::textarea(array('name' => 'license', 'cols' => '90', 'rows' => '30'), $license, 'readonly'); ?>

    <div id="accept_div" class="field">
    <?php
        echo form::label('acceptLicense', 'I Accept');
        echo form::checkbox('acceptLicense');
    ?>
    </div>

<?php echo form::close_section(); ?>