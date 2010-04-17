(function($){
    $.fn.extend({
        animatePhoneboothMenu: function(options) {
            var defaults = {
                largeMenu: true,
                div: $('div.navMenuContainer'),
                list: $('div.navMenuContainer .navMenuList'),
                groups: $('div.navMenuContainer .navMenuGroup'),
                leftWindow: $('.navMenuLeftWindow'),
                rightWindow: $('.navMenuRightWindow'),
                farmostLeft: true,
                farmostRight: false,
                persistentPos: true,
                name: 'navMenu'
            };

            var options = $.extend(defaults, options);

            return this.each(function() {
                defaults.totalMenuWidth = 0;
                defaults.groups.each(function() { defaults.totalMenuWidth += $(this).outerWidth() + 10; });
                defaults.totalMenuWidth += 110;

                options.list.width($(this).width() * 3);

                defaults.divWidth = defaults.div.width();
                defaults.divOffset = defaults.div.offset().left + 50;
                defaults.scrollConstant = (defaults.totalMenuWidth - defaults.divWidth) / defaults.divWidth;
                
                var o = $.extend(defaults, options);

                if (o.persistentPos) {
                    // create a new client-side persistent data store
                    var persistent_pos = new Persist.Store('phonebooth_persistent_pos');
                    persistent_pos.get('lastPos', function(ok, val) {
                      if (ok)
                        o.div.scrollLeft(val);
                    });                    
                }

                $(this).mousemove(function (e) {
                    var loc = e.pageX-o.divOffset;
                    var left = loc*o.scrollConstant;
                    if (o.persistentPos) {
                        persistent_pos.set('lastPos', left);
                    }
                    o.div.scrollLeft(left);

                });
            });
        }
    });
})(jQuery);

$(document).ready(function() {
    /**
     * NAVIGATION SLIDE EFFECT
     ***************************************************************/
    $('.navMenuContainer').css({overflow: 'hidden'}).animatePhoneboothMenu();

    /**
     * MAKE THE NUMBER SELECTS INTO JQUERY MULTISELECT
     ***************************************************************/
    function numberDropdown() {
        $('.numbers_dropdown').multiselect();
    }
    numberDropdown();

    /**
     * DEPENDENT GROUP ENGINE
     ***************************************************************/
    function detrminant() {
        agents = $('.determinant');
        agents.change(function () {
            agentClasses = this.className;
            dependGroup = $(this).attr('dependGroup');
            if (!dependGroup) {

                agentClasses = agentClasses.split(' ');

                for (i in agentClasses) {
                    if (agentClasses[i].match('agent_for_')) {
                        dependGroup = agentClasses[i].substr(10);
                        $(this).attr('dependGroup', dependGroup);
                        break;
                    }
                }
            }
            if ($(this).attr('checked')) {
                $('.dependent_positive.rely_on_' + dependGroup).parent().show();
                $('.dependent_negative.rely_on_' + dependGroup).parent().hide();
            } else {
                $('.dependent_positive.rely_on_' + dependGroup).parent().hide();
                $('.dependent_negative.rely_on_' + dependGroup).parent().show();
            }
        }).trigger('change');
    }
    detrminant();
    
    /**
     * LANGUAGE BAR
     ***************************************************************/
    $('#lang').change(function () {
        $(this).fadeOut('fast', function () { $('#change_lang').fadeIn('fast'); });
        $.post(document.location.href, { lang: $(this).val() }, function (data) {
            $('#lang_bar').text('On the next page reload the language will be ' + data);
            $('#lang_bar').slideDown();
        });
    });
    $('#change_lang').click(function (e) {
        e.preventDefault();
        $(this).fadeOut('fast', function () { $('#lang').fadeIn('fast'); });
    });

    /**
     * AJAX THINKING BAR
     ***************************************************************/
    $.blockUI.defaults = {
        message: '<div class="thinking">Please Wait...</div>',
        css: {
            width: '225px',
            left: '42%',
            top: '40%',
            border: '5px solid #B9B9B9',
            textAlign: 'center',
            padding: 0,
            margin: 0,
            color: '#000',
            backgroundColor: '#fff',
            cursor: 'wait'
        },
        fadeIn: 100,
        fadeOut: 200
    };
    $(document).ajaxStart(function () {
        $.blockUI()
        }).ajaxStop($.unblockUI);

    /**
     * EXECUTE AFTER EVERY AJAX REQUEST
     ***************************************************************/
    $(document).bind('ajaxStop.jqueryGetResponse', function(){
        numberDropdown();
        detrminant();
    });
});