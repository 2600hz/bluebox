$(document).ready(function() {
    $('.destinations_select').qtip({
        content: {
            title: {
                text: 'Select a Destination...'
            },
            url: '../../destinations/selector'
        },
        position: {
            target: $(document.body),
            corner: 'center'
        },
        show: {
            when: 'click', // Show it on click
            solo: true // And hide all other tooltips
        },
//        hide: {
//            when: 'unload'
//        },
        api: {
            beforeShow: function() {
                // Fade in the modal "blanket" using the defined show speed
                $('#qtip-blanket').fadeIn(this.options.show.effect.length);
            },
            beforeHide: function() {
                // Fade out the modal "blanket" using the defined hide speed
                $('#qtip-blanket').fadeOut(this.options.hide.effect.length);
            },
            onHide: function() {
                // Do something with the selections/results?
            }
        },
        style: {
            width: 700,
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

    // Create the modal backdrop on document load so all modal tooltips can use it
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

});
