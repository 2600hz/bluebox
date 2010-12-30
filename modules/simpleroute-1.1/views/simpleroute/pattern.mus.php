<div class="field">

    <?php echo form::label('simpleroute[patterns][{{patternCount}}]', 'Pattern {{displayCount}}'); ?>

    <?php echo form::input('simpleroute[patterns][{{patternCount}}]', '{{pattern}}'); ?>

    <span class="ui-icon ui-icon-trash remove_pattern" style="display:inline-block;" id="remove_pattern_{{patternCount}}">&nbsp;</span>

</div>