<div id="log_viewer_window">
    <div id="console"></div>
</div>

<script type="text/javascript">
    var timestamp = null;
    var outputconsole = document.getElementById('console');

    function pollLog() {
        $.ajax({
            type: 'GET',
            url: 'stream?timestamp=' + timestamp,
            dataType: 'json',
            cache: false,

            success: function(stream) {
                if(stream != null) {
                    var json_reply = stream.json_reply;
                    var stream_info = stream.stream_info;

                    $.each(json_reply, function(i, reply) {
                        // This should never happen, but just incase...
                        if(reply.data != "") {
                            $('<div id="result">' + reply.data + '</div>').appendTo('#console');
                            outputconsole.scrollTop = outputconsole.scrollHeight;
                        }
                    });

                    timestamp = stream_info.timestamp;
                }

                setTimeout('pollLog()', 1000);
            }
        });
    }

    $(function() {
        pollLog();
    });
</script>