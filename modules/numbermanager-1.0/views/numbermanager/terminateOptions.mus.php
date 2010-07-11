<style>
    .no_answer_accordion {width:55em; margin: 0 auto;}
</style>

<?php echo form::open_section('No Answer'); ?>

    <div id="number{{number_id}}_no_answer_accordion" class="no_answer_accordion">

        <h3>
            
            <a href="#" rel="hangup">Hangup</a>
            
        </h3>

        <div style="text-align: center;">

            If this call is not answered hangup.

        </div>


        <h3>

            <a href="#" rel="voicemail">Send to Voicemail</a>

        </h3>

        <div style="text-align: center;">

            If this call is not answered direct the caller to the voicemail box 

            <?php
                echo form::dropdown(
                    'number{{number_id}}[dialplan][terminate][voicemail]',
                    Voicemails::provideNumberTerminators(),
                    isset($terminate['voicemail']) ? $terminate['voicemail'] : NULL
                );
            ?>

        </div>


        <h3>

            <a href="#" rel="transfer">Transfer</a>

        </h3>

        <div style="text-align: center;">

            <div>
            
                If this call is not answered transfer the caller to
                
            </div>

            <?php
                if (isset($terminate['transfer'])) {

                    $selectedClass = numbering::getAssignedPoolByNumber($terminate['transfer']);

                } else {

                    $selectedClass = NULL;

                }

                echo numbering::poolsDropdown('number{{number_id}}_transfer_class', $selectedClass);

                echo ' named ';

                echo numbering::numbersDropdown(array(
                    'id' => 'number{{number_id}}_targets',
                    'name' => 'number{{number_id}}[dialplan][terminate][transfer]',
                    'useNames' => TRUE,
                    'optGroups' => FALSE
                ), isset($terminate['transfer']) ? $terminate['transfer'] : NULL);
            ?>

        </div>

    </div>

    <input type="hidden" value="{{terminate_action}}" name="number{{number_id}}[dialplan][terminate][action]" id="number{{number_id}}_terminate_action"/>

<?php echo form::close_section(); ?>

<?php jquery::addPlugin(array('accordion', 'dependent')); ?>

<script type="text/javascript">
    
    var actionIndex = $("#number{{number_id}}_no_answer_accordion h3 a").index($("#number{{number_id}}_no_answer_accordion h3 a[rel={{terminate_action}}]"));

    if (actionIndex < 0)
    {
        actionIndex = 0;
    }

    $("#number{{number_id}}_no_answer_accordion").accordion({ autoHeight: false, active: actionIndex });

    $("#number{{number_id}}_no_answer_accordion").bind("accordionchange", function(event, ui) {

        var terminateAction = ui.newHeader.find("a").attr("rel");

        $("#number{{number_id}}_terminate_action").val(terminateAction);

    });

    $("#number{{number_id}}_targets").dependent({ parent: "number{{number_id}}_transfer_class", group: "common_class" });

</script>