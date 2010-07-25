

<div id="calls_download_form" class="update callsdownload">
    <?php echo form::open(); ?>

        <?php echo form::open_section('Filter Download'); ?>

            <?php echo "Search By:"; ?>

            <div class="field">
                <?php
                    echo form::label(array('for' => 'callsdownload[start]',
                                          'hint' => 'Leave blank to select no start date/time',
                                          'help' => 'This is the start date/time that you would like to filter your Call log download .'), 'Start:');

                    print form::input('start_date');
                ?>
            </div>
            <div class="field">
                <?php

                    echo form::label(array('for' => 'callsdownload[end]',
                                          'hint' => 'Leave blank to select no end date/time',
                                          'help' => 'This is the end date/time that you would like to filter your Call log download .'), 'End:');
                    print form::input('end_date');
                ?>
            </div>
        <?php echo form::close_section(); ?>
   <div class="buttons form_bottom">
        <?php echo form::submit(array('name' => 'submit', 'class' => 'save small_green_button'), 'Export Report'); ?>
    </div>

<?php echo $grid ?>

<p class="loud">Export Options</p>

    <div class="field">
        <?php
            echo form::label('callsdownload[export_type]', 'xls'); 
            echo form::radio('callsdownload[export_type]', 'xls');
        ?>
    </div>

    <div class="field">
        <?php
            echo form::label('callsdownload[export_type]', 'pdf'); 
            echo form::radio('callsdownload[export_type]', 'pdf');
        ?>
    </div>

    <div class="field">
        <?php
            echo form::label('callsdownload[export_type]', 'csv'); 
            echo form::radio('callsdownload[export_type]', 'csv');
        ?>
    </div>

    <div class="field">
        <?php
            echo form::label('callsdownload[export_type]', 'html'); 
            echo form::radio('callsdownload[export_type]', 'html');
        ?>
    </div>

</div>
   <div class="buttons form_bottom">
        <?php echo form::submit(array('name' => 'submit', 'class' => 'save small_green_button'), 'Export Report'); ?>
    </div>
 <?php echo form::close(); ?>

<a href="#lame" class="button">Export Report</a>

<fieldset>
<legend>RSS Feed Settings</legend>
<p>Monitor call log history using a RSS client</p>
<ul>
<li>http://www.awesome.com/rss/auth/1qazxsw23edcvfr45tgbnhy67ujm</li>
</ul>
<hr />
<a href="#lame" class="button">Regenerate Authentication Token</a>
</fieldset>

