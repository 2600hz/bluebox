<?php echo form::open_section('Voicemail Alert'); ?>

    <div class="field">
        <?php echo form::label('vmalert[enabled]', 'Enable Voicemail Alert: '); ?>
        <?php echo form::checkbox('vmalert[enabled]'); ?>
    </div>
    <div id="vmalert-container">
        <div class="field">
        <?php
            echo form::label('vmalert[number]', 'Number to dial:');
            echo form::input('vmalert[number]');
        ?>
        </div>

        <div class="field">
            <?php
                echo form::label('vmalert[context]', 'Context to dial from:');
                echo numbering::selectContext('vmalert[context]', isset($vmalert['context']) ? $vmalert['context'] : '');
            ?>
        </div>
    </div>

<?php echo form::close_section(); ?>

<script type="text/javascript">
    $(function() {
        if(!$("#vmalert_enabled").attr("checked")) {
            $("#vmalert-container").hide();
        }

        $("#vmalert_enabled").click(function() {
            $("#vmalert-container").slideToggle(200);
        });
    });

</script>