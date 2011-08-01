<?php defined('SYSPATH') or die('No direct access allowed.');?>

<div id="directory_update_header" class="txt-center update directory module_header">
    <h2><?php echo $mode=='create'?'Create':'Edit' ?> Directory</h2>
</div>

<div id="directory_update_form" class="update directory">   
    <?php echo form::open(); ?>
    <?php echo form::open_section(''); ?>
        <div class="field">
        <?php
            echo form::label(array(
                    'for' => 'dbndir[dbn_name]',
                    'hint' => 'Name of Directory',
                    'help' => 'Name used to identify the directory when seen in the list.'
                ),
                'Name:'
            );
            echo form::input('dbndir[dbn_name]');
        ?>
        </div>
        <div class="field">
        <?php
            echo form::label(array(
                 'for' => 'dbndir[dbn_desc]'
            	),
            	'Description:'
            );
            echo form::textarea(array('name'=>'dbndir[dbn_desc]', 'cols' => 60, 'rows' => 10));
        ?>
        </div>
        <div class="field">
        <?php
            echo form::label(array(
                    'for' => 'dbndir[dbn_max_menu_attempts]',
                    'hint' => 'Max search attempts',
                    'help' => 'Number of times someone can search the menu before the call is terminated.  This helps to deter fishers.'
                ),
                'Max Attempts:'
            );
            echo form::input('dbndir[dbn_max_menu_attempts]');
        ?>
        </div>
        <div class="field">
        <?php
            echo form::label(array(
                    'for' => 'dbndir[dbn_min_search_digits]',
                    'hint' => 'Minimum digits required for search',
                    'help' => 'Minimum digits that are required before a search is allowed.  Prevents fishers from dialing a few common characters and getting transfered to someone.'
                ),
                'Minimum Digits:'
            );
            echo form::input('dbndir[dbn_min_search_digits]');
        ?>
        </div>
        <div class="field">
        <?php
            echo form::label(array(
                    'for' => 'dbndir[dbn_terminator_key]',
                    'hint' => 'Key to terminate entry',
                    'help' => 'When pressed, this key will terminate the entry.'
                ),
                'Terminator Key:'
            );
            echo form::input('dbndir[dbn_terminator_key]');
        ?>
        </div>
        <div class="field">
        <?php
            echo form::label(array(
                    'for' => 'dbndir[dbn_digit_timeout]',
                    'hint' => 'Max time between digits',
                    'help' => 'Maximum amount of time that the system waits for a key to be pressed.'
                ),
                'Interdigit Timeout:'
            );
            echo form::input('dbndir[dbn_digit_timeout]');
        ?>
        </div>
        <div class="field">
        <?php
            echo form::label(array(
                    'for' => 'dbndir[dbn_max_result]',
                    'hint' => 'Maximum results presented to caller',
                    'help' => 'The maximum number of results that will be returned for the caller to select from.  This prevents a fisher from entering a few common characters and getting a large list of employees.'
                ),
                'Max Results:'
            );
            echo form::input('dbndir[dbn_max_result]');
        ?>
        </div>
        <div class="field">
        <?php
            echo form::label(array(
                    'for' => 'dbndir[dbn_next_key]',
                    'hint' => 'Key to go to next result',
                    'help' => 'Key to go to next result in case multiple results are returned.'
                ),
                'Next Key:'
            );
            echo form::input('dbndir[dbn_next_key]');
        ?>
        </div>
        <div class="field">
        <?php
            echo form::label(array(
                    'for' => 'dbndir[dbn_prev_key]',
                    'hint' => 'Key to go to previous result',
                    'help' => 'Key to go to previous result in case multiple results are returned.'
                ),
                'Previous Key:'
            );
            echo form::input('dbndir[dbn_prev_key]');
        ?>
        </div>
        <div class="field">
        <?php
            echo form::label(array(
                    'for' => 'dbndir[dbn_switch_order_key]',
                    'hint' => 'Key to switch the search order',
                    'help' => 'Key to switch between "First Last" and "Last First" search order.'
                ),
                'Switch Order Key:'
            );
            echo form::input('dbndir[dbn_switch_order_key]');
        ?>
        </div>
        <div class="field">
        <?php
            echo form::label(array(
                    'for' => 'dbndir[dbn_select_name_key]',
                    'hint' => 'Key to select entry',
                    'help' => 'Key to select from the entrys returned by the query.'
                ),
                'Select Key:'
            );
            echo form::input('dbndir[dbn_select_name_key]');
        ?>
        </div>
        <div class="field">
        <?php
            echo form::label(array(
                    'for' => 'dbndbn[dbn_new_search_key]',
                    'hint' => 'Restart search',
                    'help' => 'Key to restart the search from the beginning.'
                ),
                'New Search Key:'
            );
            echo form::input('dbndir[dbn_new_search_key]');
        ?>
        </div>
        <div class="field">
        <?php
            echo form::label(array(
                 	'for' => 'dbndir[dbn_search_order]'
                ),
                'Default Search Order:'
            );
            echo form::dropdown(array('name'=>'dbndbn[dbn_search_order]'), array('first_name' => '[First] [Last]', 'last_name' => '[Last] [First]'), 'first_name');
        ?>
        </div>
        <?php 
       	echo form::hidden('dbndbn[dbn_profile]');
    echo form::close_section();
    
    if (isset($views))
    {
    	echo subview::renderAsSections($views);
    }
    echo form::close(TRUE); ?>
</div>