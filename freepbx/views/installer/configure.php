    <?php echo form::open_section('Database Connection'); ?>

        <div class="field">
        <?php
            echo form::label('dbType', 'Database Type:');
            echo form::dropdown('dbType', $dbTypes);
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('dbHostName', 'Database Host:');
            echo form::input('dbHostName');
            echo form::input('dbPathName');
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('dbName', 'Database Name:');
            echo form::input('dbName');
        ?>
        </div>

        <div class="field dbCredentials">
        <?php
            echo form::label('dbUserName', 'Database Username:');
            echo form::input('dbUserName');
        ?>
        </div>

        <div class="field dbCredentials">
        <?php
            echo form::label('dbUserPwd', 'Database Password:');
            echo form::input('dbUserPwd');
        ?>
        </div>

        <div class="field dbCredentials">
        <?php
            echo form::label(array('for' => 'dbPortSelection', 'hint' => 'Leave blank to use the default'), 'Database Port:');
            echo form::input('dbPortSelection');
        ?>
        </div>

        <div class="field dbCredentials">
        <?php
            echo form::label('dbPersistent', 'Use Persistent Connection');
            echo form::checkbox('dbPersistent');
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('samples', 'Install Sample Data');
            echo form::checkbox('samples');
        ?>
        </div>

    <?php echo form::close_section(); ?>


    <?php echo form::open_section('System Defaults'); ?>

        <div class="field domain">
        <?php
            echo form::label('siteDomain', 'Site Domain');
            echo '<div>http://' .$_SERVER['HTTP_HOST'] .'</div>' .form::input('siteDomain', $autoURI);
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('language', 'Default Langauage');
            echo form::dropdown('language', $defaultLanguages, $defaultLanguage);
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('defaultTimeZone', 'Default Timezone');
            echo form::timezones('defaultTimeZone', $defaultTimeZone);
        ?>
        </div>

        <div class="field">
        <?php
            echo form::label('defaultCurrency', 'Default Currency');
            echo form::dropdown('defaultCurrency', $defaultCurrencies);
        ?>
        </div>

        <div class="field upload">
        <?php
            echo form::label('uploadDir', 'Upload Directory');
            echo form::input('uploadDir');
        ?>
        </div>

    <?php echo form::close_section(); ?>


    <?php echo form::open_section('Anonymous Usage Statistics'); ?>

        <p>
            <?php echo __('If you decide to enable anonymous usage statistics, information about how you use the FreePBX 3 will be collected. This does not include any personal information, such as who you are or any phone numbers. We will use this data to improve our software - for example, we may change the system requirements to better suite our users or enhance the package selection in future versions of FreePBX. -Thank you!'); ?>
        </p>

        <div class="field">
        <?php
            echo form::label('collectStatistics', 'Allow Anonymous Statistic Collection');
            echo form::checkbox('collectStatistics');
        ?>
        </div>

    <?php echo form::close_section(); ?>

<?php
    // If the jquery exists use it to make it more interactive
    if (class_exists('jquery') )
    {
        jquery::addQuery('#dbType') -> change('function () {
                if($(this).val().indexOf(\'sqlite\') == -1)
                {
                    $(\'.dbCredentials\').show();
                    $(\'#dbHostName\').show();
                    $(\'#dbPathName\').hide();
                    $(\'#label_dbHostName\').text(\'' .__('Database Host:') .'\');
                } else {
                    $(\'.dbCredentials\').hide();
                    $(\'#dbHostName\').hide();
                    $(\'#dbPathName\').show();
                    $(\'#label_dbHostName\').text(\'' .__('Database Path:') .'\');
                }
            }
        ');
        jquery::addQuery('#dbType') -> trigger('change');

    }
?>
