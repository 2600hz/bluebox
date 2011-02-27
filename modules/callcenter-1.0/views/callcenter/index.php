<div class="callcenter-wrapper">
    <div class="action-buttons">
        <input type="button" id="tier-button-add" class="button queue-focused-action tier-unfocused-action" value="Add Tier"/>
        <input type="button" id="tier-button-delete" class="button queue-focused-action tier-focused-action" value="Delete Tier"/>
        <input type="button" id="queue-button-delete" class="button queue-focused-action" value="Delete Queue"/>
        <input type="button" id="queue-button-add" class="button queue-unfocused-action" value="Add Queue"/>
    </div>
    <div class="left-pane pane">
        <div class="menu-bar-wrapper">
            <div class="menu-bar">
                <div class="menu-top-wrapper">
                    <div class="menu-top">
                        <div class="menu-selector-wrapper">
                            <div class="menu-selector">
                                <div class="menu-selector-button-holder">
                                    <input type="button" id="menu-select-agents" class="button menu-selector-button menu-select-small-menu" value="Agents"/>
                                </div>
                                <div class="menu-selector-button-holder">
                                    <input type="button" id="menu-select-add" class="button menu-selector-button menu-select-small-menu" value="Add"/>
                                </div>
                                <div class="menu-selector-button-holder">
                                    <input type="button" id="menu-select-manage" class="button menu-selector-button menu-select-big-menu" value="Manage"/>
                                </div>
                            </div>
                        </div>
                        <div class="menu-search-bar-wrapper">
                            <div class="menu-search-bar">
                                <div class="menu-search-bar-mag-lens"></div>
                                <input type="text" id="menu-search-bar-text" class="hint" value="" hint="type your search here"/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="menu-bottom-wrapper">
                    <div class="menu-agents menu" menu="agents">
                        <div class="menu-agents-agents-wrapper">
                            <div class="agents"></div>
                        </div>
                    </div>
                    <div class="menu-add menu" menu="add">
                        <div class="menu-title">Add an Agent</div>
                        <div class="add-form">
                            <!--<div class="add-picture"></div>-->
                            <div class="add-fields">
                                <div class="add-field-label">Name:</div>
                                <div class="add-name add-field-wrapper">
                                    <input type="text" id="add-name-text" class="hint add-text-field" maxlength="16" hint="type a name here"/>
                                </div>
                                <div class="add-field-label">Type:</div>
                                <div class="add-type add-field-wrapper">
                                    <select id="add-type-selected" class="add-select-field">
                                        <option value="callback">Callback</option>
                                        <option value="uuid-standby">Standby</option>
                                    </select>
                                </div>
                                <div class="add-field-label">Device:</div>
                                <div class="add-device add-field-wrapper">
                                    <select id="add-device-selected" class="add-select-field">
                                        <?php foreach($devices as $device) { ?>
                                        <option class="add-device-<?php echo $device['id']; ?>" value="<?php echo $device['id']; ?>"><?php echo $device['name']; ?></option>
                                        <?php } ?>
                                    </select>
                                    <select id="add-device-disabled" class="hidden"></select>
                                </div>
                                <div class="add-field-label">Maximum no answer calls:</div>
                                <div class="add-name add-field-wrapper">
                                    <input type="text" id="add-max-no-answer" class="hint add-text-field number-only-field" maxlength="11" hint="enter number of calls"/>
                                </div>
                                <div class="add-field-label">Wrap up time:</div>
                                <div class="add-name add-field-wrapper">
                                    <input type="text" id="add-wrap-up-time" class="hint add-text-field number-only-field" maxlength="11" hint="enter time in seconds"/>
                                </div>
                                <div class="add-field-label">Reject delay time:</div>
                                <div class="add-name add-field-wrapper">
                                    <input type="text" id="add-reject-delay" class="hint add-text-field number-only-field" maxlength="11" hint="enter time in seconds"/>
                                </div>
                                <div class="add-field-label">Busy delay time:</div>
                                <div class="add-name add-field-wrapper">
                                    <input type="text" id="add-busy-delay" class="hint add-text-field number-only-field" maxlength="11" hint="enter time in seconds"/>
                                </div>
                            </div>
                            <div class="add-buttons">
                                <div class="add-button-wrapper">
                                    <input type="button" id="add-save-button" class="button" value="Add agent"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="menu-manage menu" menu="manage">
                        Manage
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="right-pane pane focused-pane">
        <div class="queues-wrapper">
            <div class="queues" domain="<?php echo $domain; ?>"></div>
        </div>
    </div>
</div>
<br><br>