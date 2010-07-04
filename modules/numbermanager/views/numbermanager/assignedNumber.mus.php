<div id="assign_number_{{number_id}}" class="assign_number_tab">
    
    <span style="float:right;">

        <a href="#assign_number_{{number_id}}" class="unassign_number" title="Unassign number" style="padding:5px;">Unassign</a>

    </span>

    <?php if (!empty($numberOptionTemplate)): ?>

        {{#registry}}

        <?php echo new View($numberOptionTemplate, array('mustache_template' => FALSE)); ?>

        {{/registry}}
        
    <?php endif; ?>

    <?php
        $view = NumberManager_Plugin::terminateOptions(TRUE);

        if (isset($dialplan['terminate']))
        {
            $view->terminate = $dialplan['terminate'];
        }

        if (isset($dialplan['terminate']['action']))
        {
            $view->terminate_action = $dialplan['terminate']['action'];
        }

        if (!empty($number_id))
        {
            $view->number_id = $number_id;
        }
        else
        {
            $view->mustache_template = FALSE;
        }

        echo $view;
    ?>

    <input type="hidden" value="{{number}}" class="number_datastore" name="numbers[assigned][{{number_id}}][number]"/>

    <input type="hidden" value="{{number_id}}" class="number_id_datastore" name="numbers[assigned][{{number_id}}][number_id]"/>

    <input type="hidden" value="{{class_type}}" class="number_class_datastore" name="numbers[assigned][{{number_id}}][class_type]"/>

</div>