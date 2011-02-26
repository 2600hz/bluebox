$(function()
{
    // Globals
    //Log levels
    // 0 - turn off all notifications/logging
    // 1 - console all notifications/logging
    // 2 - jGrowl all notifications/logging (Default)
    var loglevel = 2;
    // End Globals

    // Start hint code
    $('.hint').each(function()
    {
        restoreHint($(this));
    });

    $('.hint').live('focusin', function()
    {
        if(!$(this).hasClass('text-typed'))
        {
            $(this).val('');
            $(this).removeClass('text-empty');
        }
        $(this).addClass('text-typed');
    });

    $('.hint').live('focusout', function()
    {
        if($(this).val().length == 0)
        {
            restoreHint($(this));
        }
    });

    function restoreHint(text_field)
    {
        text_field.removeClass('text-typed');
        text_field.addClass('text-empty');
        text_field.val(text_field.attr('hint'));
    }

    function resetHintClasses(text_field)
    {
        if(text_field.val() != text_field.attr('hint') && text_field.hasClass('text-empty'))
        {
            text_field.removeClass('text-empty').addClass('text-typed');
        }
    }
    // End hint code

    // Start number only code
    $('.number-only-field').live('keydown', function(event)
    {
        if(!((event.keyCode > 47 && event.keyCode < 58 && !event.shiftKey)
              || (event.keyCode > 95 && event.keyCode < 106)
              || (event.keyCode > 7 && event.keyCode < 10)
              || (event.keyCode > 36 && event.keyCode < 41)
              || (event.keyCode == 46)))
        {
            event.preventDefault();
        }
    });

    $('.number-only-field').live('change', function(event)
    {
        var text = $(this).val();
        if(text.match(/\D/g))
        {
            text = text.replace(/\D/g, '');
            $(this).val(text);
            $.jGrowl('Please only use numbers in this field.', {theme: 'alert', life: 5000});
        }

        if(text.match(/^0+/g) && text != '0')
        {
            text = text.replace(/^0+/g, '');

            if(text == '')
            {
                text = '0';
            }

            $(this).val(text);

            $.jGrowl('Auto-truncating 0\'s', {theme: 'alert', life: 5000});
        }
    });
    // End number only code

    // Start menu-bar code
    var agentDragOptions = {
                            helper: 'clone',
                            opacity: 0.8
                           }

    $('.menu').hide();

    $('.menu-bottom-wrapper').hide();

    loadAgentList();

    $('.agent').draggable(agentDragOptions);

    $('.menu-selector-button').click(function()
    {
        var id = $(this).attr('id');
        var menu_type = id.replace(/^menu-select-/g, '');
        var menu_name = '.menu-' + menu_type;
        var button = $(this);

        if(!$(menu_name).hasClass('focused-menu'))
        {
            $('.menu-bottom-wrapper').slideUp(function()
            {
                if(button.hasClass('menu-select-big-menu'))
                {
                    $('.right-pane').removeClass('focused-pane');
                    $('.left-pane').addClass('focused-pane');
                }
                else if(button.hasClass('menu-select-small-menu'))
                {
                    $('.left-pane').removeClass('focused-pane');
                    $('.right-pane').addClass('focused-pane');
                }

                $('.menu-temp').remove();

                $('.menu').not(menu_name).hide().removeClass('focused-menu');
                $(menu_name).show().addClass('focused-menu');
                $('.menu-bottom-wrapper').slideDown();
            });
        }
    });
    // End menu-bar code

    // Start add menu code
    $('#add-save-button').click(function()
    {
        saveAgent('add');
    });
    
    function saveAgent(saveType, id, __callback)
    {
        // Agent name field
        if($('#' + saveType + '-name-text').hasClass('text-empty'))
        {
            $.jGrowl('Agents must have a name!', {theme: 'error', life: 5000});
            return false;
        }

        var name = $('#' + saveType + '-name-text').val();

        // Agent device field
        var device = $('#' + saveType + '-device-selected').val();

        if(device == null)
        {
            $.jGrowl('Agents must be assigned a device!', {theme: 'error', life: 5000});
            return false;
        }

        // Agent type field
        var type = $('#' + saveType + '-type-selected').val();

        if(type == null)
        {
            $.jGrowl('Agents must be assigned a type!', {theme: 'error', life: 5000});
            return false;
        }

        // Max no anwser call field
        if($('#' + saveType + '-max-no-answer').hasClass('text-empty'))
        {
            $.jGrowl('Please define max no answer calls!', {theme: 'error', life: 5000});
            return false;
        }

        var no_answers = $('#' + saveType + '-max-no-answer').val();

        if(no_answers.match(/^\d*$/g) == null)
        {
            $.jGrowl('\'Max no answer calls\' field accepts numbers', {theme: 'error', life: 5000});
            return false;
        }

        // Wrap up time field
        if($('#' + saveType + '-wrap-up-time').hasClass('text-empty'))
        {
            $.jGrowl('Please define wrap up time!', {theme: 'error', life: 5000});
            return false;
        }

        var wrapup_time = $('#' + saveType + '-wrap-up-time').val();

        if(wrapup_time.match(/^\d*$/g) == null)
        {
            $.jGrowl('\'Wrap up time\' field accepts numbers', {theme: 'error', life: 5000});
            return false;
        }

        // Reject delay field
        if($('#' + saveType + '-reject-delay').hasClass('text-empty'))
        {
            $.jGrowl('Please define reject delay!', {theme: 'error', life: 5000});
            return false;
        }

        var reject_delay = $('#' + saveType + '-reject-delay').val();

        if(reject_delay.match(/^\d*$/g) == null)
        {
            $.jGrowl('\'Reject delay\' field accepts numbers', {theme: 'error', life: 5000});
            return false;
        }

        // Busy delay field
        if($('#' + saveType + '-busy-delay').hasClass('text-empty'))
        {
            $.jGrowl('Please define busy delay!', {theme: 'error', life: 5000});
            return false;
        }

        var busy_delay = $('#' + saveType + '-busy-delay').val();

        if(busy_delay.match(/^\d*$/g) == null)
        {
            $.jGrowl('\'Busy delay\' field only accepts numbers', {theme: 'error', life: 5000});
            return false;
        }

        var agent_json = agentToJSON(name, type, device, no_answers, wrapup_time, reject_delay, busy_delay);

        var request, url = 'agents';
        if(saveType == "add")
        {
            request = envelope('PUT', agent_json);
        }
        else if(saveType == "edit")
        {
            url += '/' + id;
            request = envelope('POST', agent_json);
        }
        
        $.ajax({url: url,
                type: 'post',
                contentType: 'application/json',
                data: request,
                dataType: 'json',
                success: function(reply)
                {
                    if(reply.status == 'success')
                    {
                         if(saveType == "add")
                         {
                            addAgentHTML(reply.data.agent_id, reply.data.name);
                         }
                         else if(saveType == "edit")
                         {
                            addDeviceToDropdown('add', $('#edit-device-selected').attr('olddeviceid'));
                         }

                         // FOR DEMO
                         removeDeviceFromDropdown('add', reply.data.device_id);
                         restoreHint($('.' + saveType + '-text-field'));

                         $('.agent-' + reply.data.agent_id).find('.agent-user-name').html(reply.data.name);

                         if(saveType == "add")
                         {
                            $.jGrowl('Added agent ' + reply.data.name + '!', {theme: 'success', life: 5000});
                         }
                         else if(saveType == "edit")
                         {
                            $.jGrowl('Edited agent ' + reply.data.name + '!', {theme: 'success', life: 5000});
                         }

                         if(typeof __callback == 'function')
                         {
                             __callback();
                         }
                    }
                    else
                    {
                         $.jGrowl('Error: ' + reply.message, {theme: 'error', life: 5000});
                    }
                },
                error: function()
                {
                    $.jGrowl('No server response. Have you lost your internet connection?', {theme: 'error', life: 5000});
                }
        });

        return true;
    }

    function loadAgentList()
    {
        var request = envelope('GET', '""');
        $.ajax({url: 'agents',
                type: 'post',
                contentType: 'application/json',
                data: request,
                dataType: 'json',
                success: function(reply)
                {
                    if(reply.status == 'success')
                    {
                        $.each(reply.data, function(i, agent)
                        {
                            addAgentHTML(agent.agent_id, agent.name);
                            // FOR DEMO
                            removeDeviceFromDropdown('add', agent.device_id);
                        });
                    }
                    else
                    {
                         $.jGrowl('Error loading agents: ' + reply.message, {theme: 'error', life: 5000});
                    }

                    loadQueues();
                },
                error: function()
                {
                    $.jGrowl('No server response. Have you lost your internet connection?', {theme: 'error', life: 5000});
                }
        });
    }

    function addAgentHTML(id, name)
    {
        var agent_html= '   <div class="agent agent-' + id + '" agentid=' + id + '>' +
                        '       <div class="agent-user-icon"></div>' +
                        '       <div class="agent-user-name">' + name + '</div>' +
                        '   </div>';

        var agent = $(agent_html);
        agent.draggable(agentDragOptions);
        agent.appendTo('.menu-agents-agents-wrapper > .agents');
    }

    function removeDeviceFromDropdown(type, device_id)
    {
        $('#' + type + '-device-selected').find('.add-device-' + device_id).detach().appendTo($('#' + type + '-device-disabled'));
    }

    function addDeviceToDropdown(type, device_id)
    {
        $('#' + type + '-device-disabled').find('.add-device-' + device_id).detach().appendTo($('#' + type + '-device-selected'));;
    }

    $('.agent').live('dblclick', function()
    {
        editAgent($(this).attr('agentid'));
    });

    //For this we will hijack the add-menu
    function editAgent(agent_id)
    {
        var request = envelope('GET', '""');
        $.ajax({url: 'agents/' + agent_id,
                type: 'post',
                contentType: 'application/json',
                data: request,
                dataType: 'json',
                success: function(reply)
                {
                    if(reply.status == 'success')
                    {
                        var html = $('.menu-add').html();

                        html = html.replace(/id\=\"add/g,'id="edit');

                        var $editMenu = $('<div id="menu-edit" class="menu-add menu menu-temp"></div>');
                        $editMenu.html(html);
                        $editMenu.find('.menu-title').html('Edit an Agent');
                        $editMenu.find('#edit-save-button').val('Save Agent').attr('agentid', agent_id);
                        $editMenu.find('#edit-save-button').attr('returnmenu', $('.focused-menu').attr('menu'));
                        $editMenu.find('#edit-name-text').removeClass('text-empty').addClass('text-typed').val(reply.data.name);
                        $editMenu.find('#edit-type-selected').val(reply.data.type);
                        $editMenu.find('#edit-max-no-answer').removeClass('text-empty').addClass('text-typed').val(reply.data.registry.max_no_answer);
                        $editMenu.find('#edit-wrap-up-time').removeClass('text-empty').addClass('text-typed').val(reply.data.registry.wrap_up_time);
                        $editMenu.find('#edit-reject-delay').removeClass('text-empty').addClass('text-typed').val(reply.data.registry.reject_delay_time);
                        $editMenu.find('#edit-busy-delay').removeClass('text-empty').addClass('text-typed').val(reply.data.registry.busy_delay_time);
                        
                        $editMenu.hide();
                        $('.menu-bottom-wrapper').append($editMenu);

                        // FOR DEMO
                        addDeviceToDropdown('edit', reply.data.device_id);

                        $editMenu.find('#edit-device-selected').find('.add-device-' + reply.data.device_id).attr('selected', 'selected');
                        $editMenu.find('#edit-device-selected').attr('olddeviceid', reply.data.device_id);

                        $('.menu-bottom-wrapper').slideUp(function()
                        {
                            $('.left-pane').removeClass('focused-pane');
                            $('.right-pane').addClass('focused-pane');
                            
                            $('.menu').removeClass('focused-menu').hide();
                            $('#menu-edit').addClass('focused-menu').removeClass('menu-add').show();
                            $('.menu-bottom-wrapper').slideDown();
                        });
                    }
                    else
                    {
                        $.jGrowl('Error loading agent: ' + reply.message, {theme: 'error', life: 5000});
                    }
                },
                error: function()
                {
                    $.jGrowl('No server response. Have you lost your internet connection?', {theme: 'error', life: 5000});
                }
        });
    }

    $('#edit-save-button').live('click', function()
    {
        saveAgent('edit', $(this).attr('agentid'), function()
        {
            var menu = $('#edit-save-button').attr('returnmenu');

            $('.menu-bottom-wrapper').slideUp(function()
            {
                if(menu === undefined)
                {
                    return false;
                }

                if(menu == 'manage')
                {
                    $('.right-pane').removeClass('focused-pane');
                    $('.left-pane').addClass('focused-pane');
                }
                else
                {
                    $('.left-pane').removeClass('focused-pane');
                    $('.right-pane').addClass('focused-pane');
                }

                $('.menu-temp').remove();

                $('.menu-' + menu).addClass('focused-menu').show();
                $('.menu-bottom-wrapper').slideDown();
            });
        });
    });

    // End add menu code

    // Start item code
    function selectItem($item, itemName, __callback)
    {
        var $parentContainer = $item.parents('.' + itemName + 's-wrapper:first');

        //Close
        if($item.hasClass('focused-' + itemName))
        {
            $item.removeClass('focused-' + itemName);

            $item.siblings('.' + itemName + '-content').slideUp(function()
            {
                var slidedown_function = function()
                {
                    $parentContainer.scrollTop($parentContainer.attr('oldscroll'));

                    enableUnfocusedActions(itemName);

                    $item.find('.' + itemName + '-bar-toggle').html('Press to open');

                    if(typeof __callback == 'function')
                    {
                        __callback();
                    }
                };

                if($item.parents('.' + itemName + '-wrapper ').siblings().length == 0)
                {
                    slidedown_function();
                }
                else
                {
                    $item.parents('.' + itemName + '-wrapper ').siblings().slideDown(slidedown_function);
                }
            });
        }
        //Open
        else
        {
            $parentContainer.find('.' + itemName + '-bar').removeClass('focused-' + itemName);
            $item.addClass('focused-' + itemName);

            $parentContainer.attr('oldscroll', $parentContainer.scrollTop());

            var slidedown_content = function()
            {
                $parentContainer.scrollTop(0);
                $item.siblings('.' + itemName + '-content').slideDown(function() {

                    enableFocusedActions(itemName);

                    $item.find('.' + itemName + '-bar-toggle').html('Press to close');

                    if(typeof __callback == 'function')
                    {
                        __callback();
                    }
                });
            }

            if($item.parents('.' + itemName + '-wrapper ').siblings().length == 0)
            {
                slidedown_content();
            }
            else
            {
                $item.parents('.' + itemName + '-wrapper ').siblings().slideUp(slidedown_content);
            }
        }
    }

    function deleteItem(itemName, $parent)
    {
        var $focusedItem = $parent.find('.focused-' + itemName);
        var $focusedParent = $focusedItem.parents('.' + itemName + '-wrapper:first');
        selectItem($focusedItem, itemName, function()
        {
            $focusedParent.hide('slide', {direction: 'right'}, function()
            {
                $focusedParent.remove();
            });
        });
        
        enableUnfocusedActions(itemName);
    }

    function addItem(itemName, id, $parent, content)
    {
        var item = '<div class="' + itemName +'-wrapper">' +
                    '    <div class="' + itemName + ' ' + itemName + '-' + id + '">' +
                    '        <div class="' + itemName + '-bar">' +
                    '            <div class="' + itemName + '-bar-toggle">Press to open</div>' +
                    '            <div class="' + itemName + '-bar-text">' + itemName.substr(0,1).toUpperCase() + itemName.substr(1) + ' ' + id + '</div>' +
                    '        </div>' +
                    '        <div class="' + itemName + '-content">' +
                    '            ' + content +
                    '        </div>' +
                    '    </div>' +
                    '</div>';

        var $ref = $(item);
        $ref.find('.' + itemName + '-content').hide();
        $ref.find('.' + itemName + '-bar').hover(function()
        {
            $(this).find('.' + itemName + '-bar-toggle').clearQueue().fadeTo(400, 1);
        },
        function()
        {
            $(this).find('.' + itemName + '-bar-toggle').clearQueue().fadeTo(400, 0);
        });
        $ref.find('.' + itemName + '-bar-toggle').hide();
        $parent.append($ref);

        return $ref;
    }
    // End item code

    // Start action button code
    function enableFocusedActions(itemName)
    {

        $('.action-buttons > .' + itemName + '-focused-action').show();
        $('.action-buttons > .' + itemName + '-unfocused-action').hide();
    }

    function enableUnfocusedActions(itemName)
    {
        $('.action-buttons > .' + itemName + '-focused-action').hide();
        $('.action-buttons > .' + itemName + '-unfocused-action').show();
    }

    function disableAllActions(itemName)
    {
        $('.action-buttons > .' + itemName + '-focused-action').hide();
        $('.action-buttons > .' + itemName + '-unfocused-action').hide();
    }
    // End action button code

    // Start queue code
    $('.queue-focused-action').hide();

    $('.queue-bar').live('click', function()
    {
        var $queue = $(this);
        selectItem($queue, 'queue', function()
        {
            if($queue.hasClass('focused-queue'))
            {
                // Find out if we have a tier selected
                var $tier = $queue.parent().find('.focused-tier');
                if($tier.length != 0)
                {
                    if($tier.hasClass('queue-options-tier'))
                    {
                        disableAllActions('tier');
                        return true;
                    }

                    enableFocusedActions('tier');
                }
                else
                {
                    enableUnfocusedActions('tier');
                }
            }
        });
    });

    // STILL NEED TO IMPLEMENT MOH AND RECORD OPTIONS!!!!
    $('#queue-button-add').click(function()
    {
        var request = envelope('PUT', '{"name":"temp"}')
        $.ajax({url: 'queues',
                type: 'post',
                contentType: 'application/json',
                data: request,
                dataType: 'json',
                success: function(reply)
                {
                    if(reply.status == 'success')
                    {
                         var $queue = addQueue(reply.data.queue_id);
                         updateQueue($queue.find('.queue'), 'Created Queue ' + $queue.find('.queue').attr('queueid'));
                    }
                    else
                    {
                         $.jGrowl('Error: ' + reply.message, {theme: 'error', life: 5000});
                    }
                },
                error: function()
                {
                    $.jGrowl('No server response. Have you lost your internet connection?', {theme: 'error', life: 5000});
                }
        });
    });

    function loadQueues()
    {
        ajaxAPI('queues',
                'GET',
                '""',
                function(reply)
                {
                    $.each(reply.data, function(i, queue)
                    {
                        var $queue = addQueue(queue.queue_id);
                        $queue.find('.queue-option-discard-after').val(queue.registry.discard_abandoned_after);
                        $queue.find('.queue-option-max-wait').val(queue.registry.max_wait_time);
                        $queue.find('.queue-option-max-wait-no-agent').val(queue.registry.max_wait_time_with_no_agent);
                        $queue.find('.queue-option-max-wait-no-agent-reached').val(queue.registry.max_wait_time_with_no_agent_time_reached);
                        $queue.find('.queue-option-tier-wait').val(queue.registry.tier_rule_wait_second)

                        $queue.find('.queue-option-field[hint]').each(function()
                        {
                            resetHintClasses($(this));
                        });

                        $queue.find('.queue-option-strategy > option').each(function()
                        {
                            if($(this).val() == queue.registry.strategy)
                            {
                                $(this).attr('selected', 'selected')
                            }
                        });
                        $queue.find('.queue-option-resume-position > option').each(function()
                        {
                            if($(this).val() == queue.registry.abandoned_resume_allowed)
                            {
                                $(this).attr('selected', 'selected')
                            }
                        });
                        $queue.find('.queue-option-call-score > option').each(function()
                        {
                            if($(this).val() == queue.registry.time_based_score)
                            {
                                $(this).attr('selected', 'selected')
                            }
                        });
                        $queue.find('.queue-option-tier-rules > option').each(function()
                        {
                            if($(this).val() == queue.registry.tier_rules_apply)
                            {
                                $(this).attr('selected', 'selected')
                            }
                        });
                        $queue.find('.queue-option-tier-mult-wait > option').each(function()
                        {
                            if($(this).val() == queue.registry.tier_rule_wait_multiply_level)
                            {
                                $(this).attr('selected', 'selected')
                            }
                        });
                        $queue.find('.queue-option-tier-no-agents > option').each(function()
                        {
                            if($(this).val() == queue.registry.tier_rule_no_agent_no_wait)
                            {
                                $(this).attr('selected', 'selected')
                            }
                        });
                        $queue.find('.queue-option-moh > option').each(function()
                        {
                            if($(this).val() == queue.registry.moh)
                            {
                                $(this).attr('selected', 'selected')
                            }
                        });
                    });

                    // Make sure that the queues are loaded,
                    // before trying to invoke loadTiers()
                    loadTiers();
                },
                function(reply){gError('Error loading queues: ' + reply.messages);},
                function(){gError('No server response. Have you lost your internet connection?');});
    }

    function addQueue(queue_id)
    {
        var $newQueue = addItem('queue', queue_id, $('.queues'), '<div class="tiers-wrapper"><div class="tiers"></div></div>');
        $newQueue.find('.queue').attr('queueid', queue_id);

        var queueOptions = '<div class="queue-options-wrapper">' +
                            '    <div class="queue-options-title">Options</div>' +
                            '    <div class="queue-options">' +
                            '       <span class="queue-options-pane">' +
                            '           <div class="queue-option">' +
                            '              <div class="queue-option-title">Ring:</div>' +
                            '               <select class="queue-option-strategy queue-option-field">' +
                            '                   <option value="ring-all">All agents</option>' +
                            '                   <option value="longest-idle-agent">Agent with longest idle time</option>' +
                            '                   <option value="agent-with-least-talk-time">Agent with least talk time</option>' +
                            '                   <option value="agent-with-fewest-calls">Agent with fewest calls</option>' +
                            '                   <option value="sequentially-by-agent-order">Agents in order</option>' +
                            '               </select>' +
                            '           </div>' +
                            '           <div class="queue-option">' +
                            '              <div class="queue-option-title">Discard time:</div>' +
                            '              <input class="queue-option-discard-after hint queue-option-field number-only-field" maxlength="11" value="" hint="enter time in seconds"/>' +
                            '           </div>' +
                            '           <div class="queue-option">' +
                            '               <div class="queue-option-title">Action for DC\'ed caller:</div>' +
                            '               <select class="queue-option-resume-position queue-option-field">' +
                            '                   <option value="true">Resume previous position</option>' +
                            '                   <option value="false">Start over</option>' +
                            '               </select>' +
                            '           </div>' +
                            '           <div class="queue-option">' +
                            '              <div class="queue-option-title">Max wait:</div>' +
                            '              <input class="queue-option-max-wait hint queue-option-field number-only-field" maxlength="11" value="" hint="enter time in seconds"/>' +
                            '           </div>' +
                            '           <div class="queue-option">' +
                            '              <div class="queue-option-title">No agent: Max wait:</div>' +
                            '              <input class="queue-option-max-wait-no-agent hint queue-option-field number-only-field" maxlength="11" value="" hint="enter time in seconds"/>' +
                            '           </div>' +
                            '           <div class="queue-option">' +
                            '              <div class="queue-option-title">No agent wait reached:</div>' +
                            '              <input class="queue-option-max-wait-no-agent-reached hint queue-option-field number-only-field" maxlength="11" value="" hint="enter time in seconds"/>' +
                            '           </div>' +
                            '       </span>' +
                            '       <span class="queue-options-pane">' +
                            '           <div class="queue-option">' +
                            '               <div class="queue-option-title">Caller\'s priority:</div>' +
                            '               <select class="queue-option-call-score queue-option-field">' +
                            '                   <option value="queue">Based on time in queue</option>' +
                            '                   <option value="system">Based on time in system</option>' +
                            '               </select>' +
                            '           </div>' +
                            '           <div class="queue-option">' +
                            '               <div class="queue-option-title">Use tier rules?</div>' +
                            '               <select class="queue-option-tier-rules queue-option-field">' +
                            '                   <option value="false">Disabled</option>' +
                            '                   <option value="true">Enabled</option>' +
                            '               </select>' +
                            '           </div>' +
                            '           <div class="queue-option">' +
                            '               <div class="queue-option-title">Tier: Wait time:</div>' +
                            '               <input class="queue-option-tier-wait hint queue-option-field number-only-field" maxlength="11" value="" hint="enter time in seconds"/>' +
                            '           </div>' +
                            '           <div class="queue-option">' +
                            '               <div class="queue-option-title">Tier: Wait per tier:</div>' +
                            '               <select class="queue-option-tier-mult-wait queue-option-field">' +
                            '                   <option value="false">Wait the same time</option>' +
                            '                   <option value="true">Multiply wait by tier level</option>' +
                            '               </select>' +
                            '           </div>' +
                            '           <div class="queue-option">' +
                            '               <div class="queue-option-title">Tier: No agents available:</div>' +
                            '               <select class="queue-option-tier-no-agents queue-option-field">' +
                            '                   <option value="true">Skip tier</option>' +
                            '                   <option value="system">Honor tier wait time</option>' +
                            '               </select>' +
                            '           </div>' +
			    '           <div class="queue-option">' +
	 	            '               <div class="queue-option-title">Music on Hold</div>' +
			    '               <select class="queue-option-moh queue-option-field">' +
			    '                   <option value="$${hold_music}">Default MOH</option>' +
			    '               </select>' +
			    '           </div>' +
                            '       </span>' +
                            '    </div>' +
                            '</div>';
	
        //Set the id to -1 to cancel out the add
        var $newTier = addItem('tier', 0, $newQueue.find('.tiers'), queueOptions);
        $newTier.find('.tier-bar').addClass('queue-options-tier');
        $newTier.find('.tier-bar-text').html('Queue Options');
        $newTier.find('.hint').each(function()
        {
            restoreHint($(this));
        });

	$newTier.find('.queue-option-moh').append($('#data #mediafile .dropdown').html());

        return $newQueue;
    }

    $('.queue-option-field').live('change', function()
    {
        updateQueue($(this).parents('.queue'), 'Updated queue ' + $(this).parents('.queue').attr('queueid'));
    });

    function updateQueue($queue, msg)
    {
        var id = $queue.attr('queueid'),
            queue_json = queueToJSON($queue);

        ajaxAPI('queues/' + id,
                'POST',
                queue_json,
                function()
                {
                    gOk(msg);
                },
                function(reply){gError('Error: ' + reply.message);},
                function(){gError('No server response. Have you lost your internet connection?');});
    }

    $('#queue-button-delete').click(function()
    {
        var id = $('.focused-queue').parents('.queue').attr('queueid');

        deleteQueue(id,
                    function(data)
                    {
                        deleteItem('queue', $('.queues'));
                    });
    });

    function deleteQueue(queue_id, success)
    {
        ajaxAPI('queues/' + queue_id,
                'DELETE',
                '""',
                function(reply)
                {
                    gOk('Delete Queue!');

                    if(typeof success == 'function')
                    {
                        success(reply.data);
                    }
                },
                function(reply){gError('Error: ' + reply.message);},
                function(){gError('No server response. Have you lost your internet connection?');});
    }
    // End queue code

    // Start tier code
    disableAllActions('tier');

    $('.tier-bar').live('click', function()
    {
        var $tier = $(this);
        selectItem($tier, 'tier', function()
        {
            if($tier.hasClass('queue-options-tier') && $tier.hasClass('focused-tier'))
            {
                $('#tier-button-delete').hide();
            }
        });
    });

    $('#tier-button-add').click(function()
    {
        var queue_id = $('.focused-queue').parents('.queue').attr('queueid'),
            level = $('.focused-queue').parents('.queue').find('.tier').length;
            request = envelope('PUT', '{"queue_id":' + queue_id + ',"level":' + level + '}');

        addTier('{"queue_id": ' + queue_id + ', "level":' + level + '}',
                function(data)
                {
                    addTierHTML(data.tier_id, queue_id);
                });
    });

    $('#tier-button-delete').click(function()
    {
        var id = $('.focused-tier').parents('.tier').attr('tierid');
            
        deleteTier(id,
                   function()
                   {
                       deleteItem('tier', $('.focused-tier').parents('.tiers'))
                   });
    });

    function loadTiers()
    {
        ajaxAPI('tiers',
                'GET',
                '""',
                function(reply)
                {
                    $.each(reply.data, function(i, tier)
                    {
                        addTierHTML(tier.tier_id, tier.queue_id);

                        $.each(tier.agents, function(agent_i, agent)
                        {
                            addTierAgentHTML(agent.tier_id, agent.agent_id, agent.tier_agent_id);
                        });
                    });
                },
                function(reply){gError('Error: ' + reply.message);},
                function(){gError('No server response. Have you lost your internet connection?');});
    }

    function addTier(tier_data, success)
    {
        ajaxAPI('tiers',
                 'PUT',
                 tier_data,
                 function(reply)
                 {
                     gOk('Added Tier!');

                     if(typeof success == 'function')
                     {
                        success(reply.data);
                     }
                 },
                 function(reply){gError('Error: ' + reply.message);},
                 function(){gError('No server response. Have you lost your internet connection?');});
    }

    function deleteTier(tier_id, success)
    {
        ajaxAPI('tiers/' + tier_id,
                 'DELETE',
                 '""',
                 function(reply)
                 {
                     gOk('Deleted Tier!');

                     if(typeof success == 'function')
                     {
                        success(reply.data);
                     }
                 },
                 function(reply){gError('Error: ' + reply.message);},
                 function(){gError('No server response. Have you lost your internet connection?');});
    }

    function updateTier(tier_id, success)
    {
        var tier_data = tierToJSON($('.tier-' + tier_id));

        ajaxAPI('tiers/' + tier_id,
                'POST',
                tier_data,
                function(reply)
                {
                    gOk('Updated Tier!');

                    if(typeof success == 'function')
                    {
                        success(reply.data);
                    }
                },
                function(reply){gError('Error: ' + reply.message);},
                function(){gError('No server response. Have you lost your internet connection?');});
    }

    function addTierHTML(tier_id, queue_id)
    {
        var tiercode = '<span class="tier-agents-wrapper"><div class="tier-agents"></div></span><span class="tier-agents-options"></span>';
        var $newTier = addItem('tier', tier_id, $('.queue-' + queue_id).find('.tiers'), tiercode);
        $newTier.find('.tier').attr('tierid', tier_id);

        $newTier.find('.tier-agents').droppable({accept: '.agent:not(.tier-agent)',
                                                 drop: function(event, ui)
                                                 {
                                                    if($(this).find('.agent-' + ui.draggable.attr('agentid')).length == 0)
                                                    {
                                                        var $tier_agents = $(this);
                                                        $tier_agents.append(ui.draggable.clone().addClass('tier-agent'));
                                                        updateTier(tier_id,
                                                                   function(data)
                                                                   {
                                                                       var tier_agent_id = data.agents.pop().tier_agent_id;

                                                                       $tier_agents.find('.agent')
                                                                                   .last()
                                                                                   .addClass('tier-agent-' + tier_agent_id)
                                                                                   .attr('tieragentid', tier_agent_id);
                                                                   });
                                                    }
                                                    else
                                                    {
                                                        gError('Agents can only occur once in a tier');
                                                    }
                                                 }});

        $newTier.find('.tier-agents').sortable({update: function()
                                                {
                                                    updateTier(tier_id);
                                                }});

        return $newTier;
    }

    function addTierAgentHTML(tier_id, agent_id, tier_agent_id)
    {
        $('.tier-' + tier_id).find('.tier-agents')
                             .append($('.menu')
                             .find('.agent-' + agent_id)
                             .clone()
                             .addClass('tier-agent tier-agent-' + tier_agent_id)
                             .attr('tieragentid', tier_agent_id));
    }
    //End tier code


    // Start API code
    function ajaxAPI(url, verb, data, success, softerror, harderror)
    {
        var request = envelope(verb, data);
        $.ajax({
            url: url,
            type: 'post',
            data: request,
            contentType: 'application/json',
            dataType: 'json',
            success: function(reply)
            {
                if(reply.status == 'success')
                {
                    if(typeof success == 'function')
                    {
                        success(reply);
                    }
                }
                else
                {
                    if(typeof softerror == 'function')
                    {
                        softerror(reply);
                    }
                }
            },
            error: function()
            {
                if(typeof harderror == 'function')
                {
                    harderror();
                }
            }
        });
    }

    function envelope(action, json)
    {
        return '{"verb":"' + action + '","data":' + json + '}';
    }

    function agentToJSON(name, type, device, no_answer, wrapup_time, reject_delay, busy_delay)
    {
        return '{"name":"' + name + '","type":"' + type +
               '","device_id":' + device + ',"registry":{"status":"Available",' +
               '"max_no_answer":' + no_answer + ',"wrap_up_time":' + wrapup_time + "," +
               '"reject_delay_time":' + reject_delay + ',"busy_delay_time":' + busy_delay + '}}'
    }
    
    function queueToJSON($queue)
    {
        var $key;

        return  '{"name":"' + $queue.find('.queue-bar-text').html() +
                '","registry":{"strategy":"' + $queue.find('.queue-option-strategy').val() +
                '","time_based_score":"' + $queue.find('.queue-option-call-score').val() +
                '","tier_rules_apply":"' + $queue.find('.queue-option-tier-rules').val() +
                '","tier_rule_wait_second":"' + ((($key = $queue.find('.queue-option-tier-wait')).hasClass('text-empty')) ? 0 : $key.val()) +
                '","tier_rule_wait_multiply_level":"' + $queue.find('.queue-option-tier-mult-wait').val() +
                '","tier_rule_no_agent_no_wait":"' + $queue.find('.queue-option-tier-no-agents').val() +
                '","discard_abandoned_after":"' + ((($key = $queue.find('.queue-option-discard-after')).hasClass('text-empty')) ? 0 : $key.val()) +
                '","abandoned_resume_allowed":"' + $queue.find('.queue-option-resume-position').val() +
                '","max_wait_time":"' + ((($key = $queue.find('.queue-option-max-wait')).hasClass('text-empty')) ? 0 : $key.val()) +
                '","max_wait_time_with_no_agent":"' + ((($key = $queue.find('.queue-option-max-wait-no-agent')).hasClass('text-empty')) ? 0 : $key.val()) +
                '","max_wait_time_with_no_agent_time_reached":"' + ((($key = $queue.find('.queue-option-max-wait-no-agent-reached')).hasClass('text-empty')) ? 0 : $key.val()) +
                '","moh-sound":"' + $queue.find('.queue-option-moh').val().replace('/\//g', '//') + '"}}';
    }

    function tierToJSON($tier)
    {
        return '{"level":' + $tier.parents('.tier-wrapper').index() +
               ',"agents":' + tierAgentsToJSON($tier) + '}';
    }

    function tierAgentsToJSON($tier)
    {
        var agents = '[',
            tier_id = $tier.attr('tierid');

        $tier.find('.agent').each(function(pos)
        {
            var tier_agent_id;

            agents += '{'

            if(!((tier_agent_id = $(this).attr('tieragentid')) === undefined))
            {
                agents += '"tier_agent_id":' + tier_agent_id + ',';
            }

            agents += '"tier_id":' + tier_id + ',"agent_id":' + $(this).attr('agentid') + ',"position":' + (pos + 1) + '},';
        });

        agents = agents.replace(/,$/g, '') + ']';

        return agents;
    }
    // End API code

    // Start helper code
    function gError(msg)
    {
        switch(loglevel)
        {
            case 0:
                // do nothing
                break;

            case 1:
                console.log(msg);
                break;

            default:
                $.jGrowl(msg, {theme: 'error', life: 5000});
                break;
        }
    }

    function gOk(msg)
    {
        switch(loglevel)
        {
            case 0:
                // do nothing
                break;

            case 1:
                console.log(msg);
                break;

            default:
                $.jGrowl(msg, {theme: 'success', life: 5000});
                break;
        }
    }
    // End helper code
});
