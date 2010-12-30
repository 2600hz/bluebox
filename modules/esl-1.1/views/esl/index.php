<div class="column-container">
    <div class="column-sides">
        <fieldset class="title-frame">
            <legend class="title-frame-text">Quick System Commands</legend>
            <div>
                <button class="eslEvent" name="reinitializeNAT" id="reinitializeNAT">Reinitialize NAT</button>
                <button class="eslEvent" name="republishNAT" id="republishNAT">Republish NAT</button>
            </div>
        </fieldset>
    </div>

    <div class="column-middle">
        <fieldset class="title-frame">
            <legend class="title-frame-text">General System Information</legend>
            <div id="system-up-time"></div>
            <div id="num-of-calls"></div>
            <div id="num-of-modules"></div>
            <div id="num-of-channels"></div>
            <div id="num-of-codecs"></div>
        </fieldset>
    </div>

    <div class="column-sides">
        <fieldset class="title-frame">
            <legend class="title-frame-text">Quick Reload Options</legend>
            <div>
                <button class="eslEvent" name="reloadacl" id="reloadacl">Reload ACL</button>
                <button class="eslEvent" name="reloadxml" id="reloadxml">Reload XML</button>
                <button class="eslEvent" name="reloadsofia" id="reloadsofia">Reload Sofia</button>
                <button class="eslEvent" name="reloadDingaling" id="reloadDingaling">Reload Dingaling</button>
            </div>
        </fieldset>
    </div>
</div>

<div id ="demo">
    <div id="tabs">
        <ul>
            <li><a href="#debug_tab">Insert Command</a></li>
            <li><a href="#act_calls_tab">Active Calls</a></li>
            <li><a href="#sys_log_tab">Realtime Log</a></li>
            <li><a href="#mods_tab">Modules</a></li>
            <li><a href="#sipinterfaces_tab">Sip Interfaces</a></li>
            <li><a href="#channels_tab">Channels</a></li>
        </ul>
        <div id="debug_tab">
            <div>
                <?php echo form::input(array('id' => 'manual_entry_param', 'value' => '')); ?>
                <?php echo form::button(array('id' => 'manual_entry', 'param' => 'version', 'class' => 'eslEvent', 'value' => 'Send'));?>
            </div>
        </div>
        <div id="act_calls_tab">
            Active Call Information
            <div id="active-calls"></div>
        </div>
        <div id="sys_log_tab">
            System Log:
            <div id="logviewer"></div>
        </div>
        <div id="mods_tab">
            <div id="mods-output"></div>
        </div>
        <div id="sipinterfaces_tab">
            Sip Interfaces:
            <div id="sip-interfaces"></div>
        </div>
         <div id="channels_tab">
             <div id="active-channels"></div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function() {
        $("#tabs").tabs();

        $(".eslEvent").click(function() {
            $.publish("esl/" + this.id, []);
        });

        $.subscribe("esl/manual_entry", function() {
            $.post('eslresponse', 'event=esl/manual_entry&param=' + $('#manual_entry_param').val(), function(output) {
                $.jGrowl(output, { theme: 'alert', life: 5000 });
            });
        });

        $.subscribe("esl/reloadacl", function() {
            $.post('eslresponse', 'event=esl/reloadacl', function(output) {
                $.jGrowl(output, { theme: 'alert', life: 5000 });
            });
        });

        $.subscribe("esl/reloadxml", function() {
            $.post('eslresponse', 'event=esl/reloadxml', function(output) {
                $.jGrowl(output, { theme: 'alert', life: 5000 });
            });
        });

        $.subscribe("esl/reloadsofia", function() {
            $.post('eslresponse', 'event=esl/reloadsofia', function(output) {
                $.jGrowl(output, { theme: 'alert', life: 5000 });
            });
        });

        $.subscribe("esl/reinitializeNAT", function() {
            $.post('eslresponse', 'event=esl/reinitializeNAT', function() {
                $.jGrowl("Reinitialized NAT", { theme: 'alert', life: 5000 });
            });
        });

        $.subscribe("esl/republishNAT", function() {
            $.post('eslresponse', 'event=esl/republishNAT', function() {
                $.jGrowl("Republished NAT", { theme: 'alert', life: 5000 });
            });
        });

        $.subscribe("esl/reloadDingaling", function() {
            $.post('eslresponse', 'event=esl/reloadDingaling', function(output) {
                $.jGrowl(output, { theme: 'alert', life: 5000 });
            });
        });

        $.subscribe("esl/error", function(error) {
            $.jGrowl(error, { theme: 'alert', life: 5000 });
        });

        $.subscribe("esl/numactivecalls", function(numOfCalls) {
            $("#num-of-calls").text("Number of active calls: " + numOfCalls);
        });

        $.subscribe("esl/numactivemodules", function(numOfModules) {
            $("#num-of-modules").text("Number of active modules: " + numOfModules);
        });

        $.subscribe("esl/numactivechannels", function(numOfChannels) {
            $("#num-of-channels").text("Number of active channels: " + numOfChannels);
        });

        $.subscribe("esl/numactivecodecs", function(numOfCodecs) {
            $("#num-of-codecs").text("Number of active codecs: " + numOfCodecs);
        });

        $.subscribe("esl/uptime", function(time) {
            $("#system-up-time").text("System uptime: " + time);
        });

        $.subscribe("esl/modules", function(modules){
            $("#mods-output").html(modules.replace(/,/g,"<br>"));
        });
        $.subscribe("esl/sipinterfaces", function(interfaces) {
            // This is bad, but it works
            interfaces = interfaces.replace(/===/g, "=");
            $("#sip-interfaces").html(interfaces.replace(/\n/g, "<br>"));
        });
        $.subscribe("esl/calls", function(calls) {
            $("#active-calls").html(calls.replace(/\n/g, "<br>"));
        });

        $.subscribe("esl/logviewer", function(data) {
            var outputconsole = document.getElementById('sys_log_tab');
            $("#logviewer").append(data.replace(/\n/g, "<br>"));
            outputconsole.scrollTop = outputconsole.scrollHeight;
        });

        $.subscribe("esl/channels", function(channels) {
            $("#active-channels").html(channels.replace(/\n/g,"<br>"));
        });

        $.flux("fluxresponse");
    });
</script>
<?php
    jquery::addPlugin('tabs');
?>