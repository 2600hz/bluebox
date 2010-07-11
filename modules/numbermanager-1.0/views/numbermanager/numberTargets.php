<style>
    /* Vertical Tabs
    ----------------------------------*/
    .ui-tabs-vertical { width: 55em; margin:0 auto; }
    .ui-tabs-vertical .ui-tabs-nav { padding: .2em .1em .2em .2em; float: left; width: 12em; }
    .ui-tabs-vertical .ui-tabs-nav li { clear: left; width: 100%; border-bottom-width: 1px !important; border-right-width: 0 !important; margin: 0 -1px .2em 0; }
    .ui-tabs-vertical .ui-tabs-nav li a { display:block; }
    .ui-tabs-vertical .ui-tabs-nav li.ui-tabs-selected { padding-bottom: 0; padding-right: .1em; border-right-width: 1px; border-right-width: 1px; }
    .ui-tabs-vertical .ui-tabs-panel { padding: 0 1em; float: right; width: 40em;}

    ul.number_target_objects {font-family:helvetica,arial,sans-serif;margin:0;padding:0;}
    ul.number_target_objects li {margin:-1px 0 0;padding:5px; list-style:none;border-bottom:1px solid #CCCCCC; border-top:1px solid #CCCCCC;}
    ul.number_target_objects li a {text-decoration:none;display:block;padding:0.3em 0.5em;border:1px solid silver;color:#003;background:#fff;}
    ul.number_target_objects li a:hover {border:1px solid gray;color:#000;background:#efefef}
    ul.number_target_objects li .label {width:160px; font-weight:normal;}
    ul.number_target_objects li .input {margin-right:15px;}
    ul.number_target_objects li .field {float: right; margin-top: 10px;}
    ul.number_target_objects li .number_target_object_description {color:#666666; font-size: .8em; padding-left: 15px;}

    /* #number_target_selector .target_object { margin: 10px; }
    #number_target_selector {border:0 !important;}
    #number_target_selector .ui-tabs-panel { border:1px solid #CCCCCC !important; }
    #number_target_selector .ui-widget-header { background:#FFFFFF !important; border:0 !important; } */

    #number_target_selector .number_target_quick_add {text-align:right; padding-bottom: 5px;}
    #number_target_selector #number_target_list {height:225px; padding: 5px; overflow-y:auto; border: 1px solid #CCCCCC;}
</style>

<?php echo form::open_section('Route'); ?>

    <div id="number_target_selector">

        <ul>

            <?php foreach ($targets as $target): ?>
            
                <li>
                    
                    <a href="#<?php echo $target['short_name']; ?>">

                        <span><?php echo $target['display_name']; ?></span>

                    </a>

                </li>

            <?php endforeach; ?>

        </ul>

        <?php foreach ($targets as $numberType => $target): ?>

            <div id="<?php echo $target['short_name']; ?>" class="number_target_tab">

                <?php echo form::open_section('Destination'); ?>

                    <div class="number_target_quick_add">

                        <?php
                            if (!empty($target['quick_add']))
                            {
                                echo html::anchor($target['quick_add'] ,'<span>Add New ' .$target['display_name'] .'</span>', array('class' => 'qtipAjaxForm'));
                            }
                        ?>
                        
                    </div>

                    <div id="number_target_list">

                        <ul class="number_target_objects <?php echo $target['short_name']; ?>">

                            <?php foreach ($target['target_objects'] as $targetObject): ?>

                                <?php echo new View('numbermanager/targetObject.mus', $targetObject); ?>

                            <?php endforeach; ?>

                        </ul>

                    </div>

                <?php echo form::close_section(); ?>

                <?php if (!empty($numberOptions[$numberType])): ?>
                
                    <?php echo new View($numberOptions[$numberType], $registry); ?>

                <?php endif; ?>

            </div>

        <?php endforeach; ?>

    </div>

    <fieldset class="hidden_inputs">

        <input type="hidden" class=" hidden" value="<?php echo $number['class_type']; ?>" name="number[class_type]" id="number_class_type">

    </fieldset>

<?php echo form::close_section(); ?>

<?php jquery::addPlugin(array('tabs', 'scrollTo')); ?>

<?php javascript::codeBlock(NULL, FALSE); ?>

function selectDestination(class, foreignId)
{
    $('#number_target_selector .radio.' + class + '[value=' + foreignId + ']').attr('checked', true);

    $('#number_target_selector .radio:checked').each( function () {

        panel = $(this).parents('.ui-tabs-panel');

        $('#number_target_selector').tabs('select', panel.attr('id'));

        $('#number_class_type').val($(this).attr('rel'));

        $(this).parents('.number_target_objects').parent().scrollTo($(this), 0, { offset: -30 });
    });
}

$(document).ready(function () {

    $('#number_target_selector').tabs({ fxAutoHeight: true }).addClass('ui-tabs-vertical ui-helper-clearfix');

    $('#number_target_selector li').removeClass('ui-corner-top').addClass('ui-corner-left');

    $('.assign_target_object').live('change', function () {

        if ($(this).attr('checked')) {

            $('#number_class_type').val($(this).attr('rel'));

            console.log($(this).attr('rel'));

        }

    });

    $('.number_target_object').live('click', function () {

        $(this).find('.radio').attr('checked', true).trigger('change');

    });

    $('.number_target_object').hover(function() {

            $(this).css('background', '#F9F9F9');

        }, function() {

            $(this).css('background', '#FFFFFF');

    });

    <?php if (!empty($number['class_type']) && !empty($number['foreign_id'])): ?>

        selectDestination('<?php echo $number['class_type']; ?>', <?php echo $number['foreign_id']; ?>);

    <?php endif; ?>
});

<?php javascript::blockEnd(); ?>