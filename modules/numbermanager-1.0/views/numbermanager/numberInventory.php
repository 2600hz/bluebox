<style>
    ul.avaliable_numbers {font-family:helvetica,arial,sans-serif;margin:0;padding:0;}
    ul.avaliable_numbers li {margin:0;padding:5px; list-style:none;margin:0; border-bottom:1px solid #CCCCCC;}
    ul.avaliable_numbers li a {text-decoration:none;display:block;padding:0.3em 0.5em;border:1px solid silver;color:#003;background:#fff;}
    ul.avaliable_numbers li a:hover {border:1px solid gray;color:#000;background:#efefef}

    .avaliable_numbers { margin: 10px; }

    #number_inventory .ui-tabs-panel { border:1px solid #CCCCCC !important; }
    #number_inventory .ui-widget-header { background:#FFFFFF !important; border:0 !important; }
</style>

<?php echo form::open_section('Number Assignments'); ?>

<div id="number_inventory" style="border:0 !important;">

    <ul>

        <?php foreach($numbers['assigned'] as $assigned): ?>

            <li>

                <a href="#assign_number_<?php echo $assigned['number_id']; ?>"><span><?php echo $assigned['number']; ?></span></a>

            </li>

        <?php endforeach; ?>

        <li><a href="#assign_new_number"><span>Add Assignment</span></a></li>

    </ul>

        <?php foreach($numbers['assigned'] as $assigned): ?>

            <?php
                if (!empty($numberOptionTemplates[$assigned['class_type']]))
                {
                    $assigned['numberOptionTemplate'] = $numberOptionTemplates[$assigned['class_type']];
                }

                echo new view('numbermanager/assignedNumber.mus', $assigned);
            ?>

        <?php endforeach; ?>

        <div id="assign_new_number" class="assign_number_tab">

            <div style="text-align: right; padding-bottom: 5px;" class="avaliable_numbers_quick_add">

                <?php echo html::anchor('/numbermanager/create/' .$class_type ,'<span>Add New Number</span>', array('class' => 'qtipAjaxForm')); ?>

            </div>

            <div style="height: 225px; padding: 5px; overflow-y: auto; border: 1px solid #CCCCCC;">

                    <ul class="avaliable_numbers">

                        <?php foreach($numbers['avaliable'] as $avaliable): ?>

                            <?php echo new view('numbermanager/avaliableNumber.mus', $avaliable['Number']); ?>

                        <?php endforeach; ?>

                    </ul>

            </div>

        </div>

</div>

<?php echo form::close_section(); ?>

<?php jquery::addPlugin(array('tabs', 'scrollTo')); ?>

<?php javascript::add('mustache'); ?>

<?php javascript::codeBlock(NULL, array('scriptname' => 'listNumbers')); ?>

    var avaliableNumberTemplate = <?php echo $avaliableNumberTemplate; ?>;

    var assignedNumberTemplate = <?php echo $assignedNumberTemplate; ?>;

    function unassignNumberClickHandler(ev) {

        ev.preventDefault();

        panelId = '#' + $(this).parents('.assign_number_tab').attr('id');

        number = {
            number_id: $(panelId).find('.number_id_datastore').val(),
            number: $(panelId).find('.number_datastore').val(),
            class_type: $(panelId).find('.number_class_datastore').val()
        }

        $('.avaliable_numbers').append(Mustache.to_html(avaliableNumberTemplate, number));

        tabHandle = $('#number_inventory ul li a[href=' + panelId + ']').parent();

        tabIndex = $('#number_inventory ul li').index(tabHandle);

        $('#number_inventory').tabs('remove', tabIndex);

    }

    function assignNumberClickHandler(ev) {

        ev.preventDefault();

        number = {
            number_id: $(this).find('.number_id_datastore').val(),
            number: $(this).find('.number_datastore').val(),
            class_type: $(this).find('.number_class_datastore').val(),
            registry:{
                timeout: 30
            }
        }

        $('#number_inventory').append(Mustache.to_html(assignedNumberTemplate, number));

        $('#number_inventory').tabs('add', '#assign_number_' + number.number_id, number.number, 0);

        $(this).remove();

    }

    $('#number_inventory').tabs({ fxAutoHeight: true });

    $('.unassign_number').live('click', unassignNumberClickHandler);

    $('.avaliable_number').live('click', assignNumberClickHandler);

    $('.avaliable_number').live('hover', function (ev) {

            if (ev.type == 'mouseover') {

                $(this).css('background', '#F9F9F9');

            }

            if (ev.type == 'mouseout') {

                $(this).css('background', '#FFFFFF');

            }

        }
    );

<?php javascript::blockEnd(); ?>