/**
 * FORMS TO AJAX REQUESTS
 ***************************************************************/
function qtipAjaxForm(link) {
    try {
        link.qtip('api');
        return true;
    } catch(err) {}

    link.click(function() {
        return false;
    });

    var contentLoadedOnce = false;

    link.qtip({
        content: {
            text: '<div class="thinking">Please Wait...</div>',
            data: {'qtipAjaxForm' : true},
            url: link.attr('href')
        },
        position: {
            target: $(document.body),
            corner: 'center'
        },
        show: {
            when: 'click',
            solo: true
        },
        hide: {
            when: 'unload'
        },
        style: {
            width: 550,
            padding: '8px',
            title: {
                'background-color': '#72a400',
                color: '#ffffff',
                padding: '3px 10px 5px 10px',
                'font-size': '110%'
            },
            border: {
                width: 8,
                radius: 4,
                color: '#72a400'
            }
        },
        api: {
            beforeHide: function () {
                $('#qtip-blanket').fadeOut();
                this.elements.content.html(this.options.content.text);
                contentLoadedOnce = true;
            },
            beforeShow: function(){
                if (!contentLoadedOnce) {
                    contentLoadedOnce = true;
                } else {
                    this.loadContent(this.options.content.url, this.options.content.data, this.options.content.method);
                }
            },
            onContentUpdate: function () {
                var me = this;
                var options = {
                    complete: function (xhrObj, status) {
                        if(status == "error") {
                            $.jGrowl('There was an error processing this request.', { theme: 'error', life: 5000 });
                            me.hide();
                        } else {
                            if (xhrObj.getResponseHeader("X-AjaxForm-Status") == "cancel") {
                                me.hide();
                            }if (xhrObj.getResponseHeader("X-AjaxForm-Status") == "complete") {
                                $('#qtipAjaxForm-reciever').html(xhrObj.responseText);
                                me.hide();
                            } else {
                                me.updateContent(xhrObj.responseText);
                            }
                        }
                    },
                    beforeSubmit: function () {
                        me.elements.content.html(me.options.content.text);
                    }
                }

                this.elements.content.find('form').each(function () {
                    $("<input/>").attr('type', 'hidden').attr('name', 'qtipAjaxForm').attr('value', true).appendTo($(this));
                    $(this).ajaxForm(options)
                });

                var content = this.elements.content.html();

                if (content.match(/NO CONTENT/g)) {
                    contentLoadedOnce = true;
                    clearTimeout(this.timers.show);
                    this.elements.tooltip.stop(true, false);
                    if(this.elements.tooltip.css('display') !== 'none') {
                        this.hide();
                    }
                } else if (content != this.options.content.text){
                     $('#qtip-blanket').fadeIn();
                }
            }
        }
    });
};

$(document).ready(function () {

    /**
     * ATTEMPT TO HANDLE AJAX ERRORS
     ***************************************************************/
    $.ajaxSetup({
        error:function(request, err) {

            $('.qtip').qtip('api').updateContent('NO CONTENT');
            contentLoadedOnce = true;

            if(request.status == 0) {
                $.jGrowl('Can not connect to server!\n Please Check Your Network.', { theme: 'error', life: 5000 });
            } else if(request.status == 404) {
                $.jGrowl('You are not authorized to access that function.', { theme: 'error', life: 5000 });
            } else if(request.status == 500) {
                $.jGrowl('There was an error processing this request.', { theme: 'error', life: 5000 });
            } else if (request.status == 401) {
                window.location = "http://192.168.1.119/index.php/user/index";
                //$.jGrowl('Your session has timed out.', { theme: 'notice', life: 5000 });
            } else if(err == 'parsererror') {
                $.jGrowl('The data recieved from the server was invalid.', { theme: 'error', life: 5000 });
            } else if(err == 'timeout') {
                $.jGrowl('Request Time out.', { theme: 'error', life: 5000 });
            }

        }
    });
    
    /**
     * EXECUTE AFTER EVERY AJAX REQUEST
     ***************************************************************/
    $(document).bind('ajaxStop.jqueryGetResponse', function(){
        numberDropdown();
        toolTips();
        detrminant();
        additionalInfo();
        $('.qtipAjaxForm').each(function () { qtipAjaxForm($(this)); });
        $("a:contains('Delete')").each(function () { qtipAjaxForm($(this)); });
    });

    /**
     * NAVIGATION CAROUSEL
     ***************************************************************/
    // Get the inner width of our navigation contianer
    navInnerWidth = $('#navigation').innerWidth();
    
    // for each of the nav groups lets correct their formating
    $('.navGroup ul').each(function () {
        // calculate the actual width of the ul elements
        totalWidth = 0;
        $(this).find('li').each(function () {totalWidth += $(this).outerWidth();});

        $(this).parent().parent().addClass('infiniteCarousel');
        $(this).parent().parent().infiniteCarousel();
    });

    /**
     * NAVIGATION PERSISTENT CATEGORIES
     ***************************************************************/
    // create a new client-side persistent data store
    var persistent_category = new Persist.Store('bluebox_persistent_category');

    // Calculate how wide each of the category items should be (keeping them as identical as possible)
    navCategoriesWidth = $('#navCategories').innerWidth() - 10; // 10px for the padding
    categories = $('#navCategories li');
    categoryWidth = Math.floor(navCategoriesWidth/categories.length);
    categories.each(function (){
        $(this).width(categoryWidth);
    });

    // Bind a function to the click of each category
    categories.click(function (e) {
        // stop the defualt execution
        e.preventDefault();

        // save selected category in the persist store
        persistent_category.set('lastCategory', this.id);

        // hide the navigation items
        $('.navGroup').hide();
        // show the navigation items that belong to this group
        $('#navGroup' + $(this).text()).show();

        // change the class to reflect the new category on the category bar
        categories.removeClass('current_category');
        $(this).addClass('current_category');
    });

    // on start up see if we can get the users last selected category
    persistent_category.get('lastCategory', function(ok, val) {
      if (ok)
        lastCategory = val;
    });

    // if we know the users last catergory go to it, otherwise just go to the first
    if ($('#' + lastCategory).length > 0) {
        $('#' + lastCategory).trigger('click');
    } else {
        categories.filter(':first').trigger('click');
    }

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
     * TOOLTIP HELP
     ***************************************************************/
    function toolTips() {
        $('.help').each(function() {
            try {
                $(this).qtip('api');
                return true;
            } catch(err) {}

            $(this).click(function() {
                return false;
            });

            $(this).qtip({
                content: {
                    title: {
                        text: 'Additional Help...'
                    },
                    text: $(this).attr('tooltip')
                },
                position: {
                    corner: {
                        tooltip: 'topLeft',
                        target: 'bottomRight'
                    }
                },
                show: {
                    solo: true // And hide all other tooltips
                },
                style: {
                    width: 550,
                    padding: '8px',
                    title: {
                        'background-color': '#72a400',
                        color: '#ffffff',
                        padding: '3px 10px 5px 10px',
                        'font-size': '110%'
                    },
                    border: {
                        width: 8,
                        radius: 4,
                        color: '#72a400'
                    }
                }
            });

        });
    };
    toolTips();

    /**
     * ADDITIONAL INFO
     ***************************************************************/
    function additionalInfo() {
        $('.addInfo').each(function() {
            try {
                $(this).qtip('api');
                return true;
            } catch(err) {}

            $(this).click(function() {
                return false;
            });

            $(this).qtip({
                content: {
                    title: {
                        text: $(this).attr('title')
                    },
                    text: $(this).attr('tooltip')
                },
                position: {
                    corner: {
                        tooltip: 'bottomRight',
                        target: 'topLeft'
                    }
                },
                show: {
                    solo: true // And hide all other tooltips
                },
                style: {
                    padding: '8px',
                    title: {
                        'background-color': '#72a400',
                        color: '#ffffff',
                        padding: '3px 10px 5px 10px',
                        'font-size': '110%'
                    },
                    border: {
                        width: 8,
                        radius: 4,
                        color: '#72a400'
                    }
                }
            });

        });
    };
    additionalInfo();

    /**
     * NUMBER SELECTION HELPER
     ***************************************************************/
    function numberDropdown() {
        $('.numbers_dropdown').dropdownchecklist({maxDropHeight: 100, multipleSelect: true});
    }
    numberDropdown();

    $('.qtipAjaxForm').each(function () { qtipAjaxForm($(this)); });

    // Create the modal backdrop on document load so all modal tooltips can use it
    $('<div id="qtipAjaxForm-reciever">')
    .appendTo(document.body) // Append to the document body
    .hide(); // Hide it initially

    /**
     * QTIP MODAL BACKDROP
     ***************************************************************/
    $('<div id="qtip-blanket">')
    .css({
        position: 'absolute',
        top: $(document).scrollTop(), // Use document scrollTop so it's on-screen even if the window is scrolled
        left: 0,
        height: $(document).height(), // Span the full document height...
        width: '100%', // ...and full width
        opacity: 0.7, // Make it slightly transparent
        backgroundColor: 'black',
        zIndex: 5000  // Make sure the zIndex is below 6000 to keep it below tooltips!
    })
    .appendTo(document.body) // Append to the document body
    .hide(); // Hide it initially

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
});