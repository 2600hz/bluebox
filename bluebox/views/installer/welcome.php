<?php echo form::open_section('About Bluebox'); ?>
    <p>
    <?php
        echo __('Bluebox provides a modular approach to traditional telephony systems. Instead of pre-determined configuration screens and business logic, Bluebox allows you to install Applications and Plug-ins to custom tailor the system\'s behavior to your needs.');
    ?>
    </p>
<?php echo form::close_section(); ?>

<?php echo form::open_section('Installation Environment'); ?>

    <?php if (empty($results)) : ?>
        <p>
        <?php
            echo __('No errors were detected with your installation envirionment. This system should be capable of running ' .kohana::config('core.product_name'));
        ?>
        </p>
    <?php else : ?>

        <ul class="envroResults" style="padding: 5px 10px;">

            <?php foreach($results as $result) : ?>

            <?php
                $fail = $result['required'] ? 'fail ' : 'optional ';
                $class = $result['result'] ? ' pass' : 'result ' . $fail;
            ?>

            <li class="test_group <?php echo text::alternate($class .' alternate', $class); ?>" style="padding: 5px;">
                <div class="test">
                    <?php echo $result['name'] ?>
                </div>
                <div class="result">
                    <?php echo $result['result'] ? $result['pass_msg'] : $result['fail_msg']; ?>
                </div>
            </li>

            <?php endforeach; ?>

        </ul>

     <?php endif; ?>

<?php echo form::close_section(); ?>

<?php echo form::open_section('Terms and Conditions'); ?>

    <?php
          //  echo form::dropdown('language', $defaultLanguages, $defaultLanguage, 'onchange="this.form.submit();"');
    ?>

    <?php echo form::textarea(array('name' => 'license', 'cols' => '90', 'rows' => '20'), $license, 'readonly'); ?>

    <div id="accept_div" class="field">
    <?php
        echo form::label('acceptLicense', 'I Accept');
        echo form::checkbox('acceptLicense');
    ?>
    </div>

<?php echo form::close_section(); ?>
