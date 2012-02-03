<div id="downloadrange_form" class="update downloadrange">
    <?php echo form::open(); ?>

        <?php echo form::open_section('Filter Records'); ?>
            <div class="field">
                <?php
                    echo form::label(array('for' => 'startdate',
                                          'hint' => 'Leave blank to select no start date/time',
                                          'help' => 'This is the start date/time that you would like to filter your Call log download .'),
                                     	'Start Date:');

                    print form::input('startdate');
                ?>
            </div>

            <div class="field">
                <?php

                    echo form::label(array('for' => 'enddate',
                                          'hint' => 'Leave blank to select no end date/time',
                                          'help' => 'This is the end date/time that you would like to filter your Call log download .'),
                    				 	'End Date:');
                    print form::input('enddate');
                ?>
            </div>
            <div class="buttons">
                <?php echo form::button(array('name' => 'filter', 'type' => 'button', 'class' => 'save small_green_button', 'onclick' => 'gridReload()'), 'Filter'); ?>
            </div>
        <?php echo form::close_section(); ?>

<?php echo $grid ?>

<p class="loud">Export Options</p>

    <div class="field">
        <?php
            echo form::label('exporttype', 'Csv (Tab Delimited)'); 
            echo form::radio('exporttype', 'csv', 'checked');
        ?>
    </div>

    <div class="field">
        <?php
            echo form::label('exporttype', 'Xml'); 
            echo form::radio('exporttype', 'xml');
        ?>
    </div>

</div>
   <div class="buttons form_bottom">
        <?php echo form::confirm_button('Export Records'); ?>
    </div>
 <?php echo form::close(); ?>

<?php jquery::addPlugin(array('datepicker')); ?>
        
<script type="text/javascript" charset="utf-8">
    
	$(function() {
		$("#startdate").datepicker({dateFormat: 'yy-mm-dd'});
		$("#enddate").datepicker({dateFormat: 'yy-mm-dd'});
	});

	function gridReload(){
		var start_mask = $("#startdate").val();
		if (start_mask == '')
			start_mask = 'null';
		var end_mask = $("#enddate").val();
		if (end_mask == '')
			end_mask = 'null';
		$("#downloadrange").jqGrid('setGridParam',{url:"index/" + start_mask +"/" + end_mask,page:1}).trigger("reloadGrid");
	}
	
</script>
