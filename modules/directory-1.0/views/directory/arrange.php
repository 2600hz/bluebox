<div id="super">
<div id="container">

<div id="branches"></div>
</div>
</div>
<!-- JavaScript neccessary for the tree -->
<script type="text/javascript">
$(function () {
        // Settings up the tree - using $(selector).jstree(options);
        // All those configuration options are documented in the _docs folder
        $("#branches")
                .jstree({ 
                        "plugins" : [ "themes", "json_data", "ui", "crrm", "dnd", "types", "hotkeys", "contextmenu" ],
                        "json_data" : { 
                                "ajax" : {
                                        "url" : "/directory/server",
                                        "data" : function (n) { 
                                                return { 
                                                        "operation" : "get_children", 
                                                        "id" : n.attr ? n.attr("id") : ""
                                                }; 
                                        }
                                }
                        },
                        "types" : {
                                "max_depth" : -2,
                                "max_children" : -2,
                                "valid_children" : [ "folder" ],
                                "types" : {
                                        "extension" : {
                                                "valid_children" : "none",
                                                "icon" : {
                                                        "image" : "/assets/js/jstree/_demo/file.png"
                                                }
                                        },
                                        // The `folder` type
                                        "default" : {
                                                // can have files and other folders inside of it, but NOT `drive` nodes
                                                "valid_children" : [ "default", "extension" ],
                                                "icon" : {
                                                        "image" : "/assets/js/jstree/_demo/folder.png"
                                                }
                                        }
                                }
                        }
                })
                .bind("create.jstree", function (e, data) {
                        $.post(
                                "/directory/server", 
                                { 
                                        "operation" : "create_node", 
                                        "id" : data.rslt.parent.attr("id"), 
                                        "position" : data.rslt.position,
                                        "title" : data.rslt.name,
                                        "type" : data.rslt.obj.attr("rel")
                                }, 
                                function (r) {
                                        if(r.status) {
                                                $(data.rslt.obj).attr("id", r.id);
                                        }
                                        else {
                                                $.jstree.rollback(data.rlbk);
                                        }
                                }
                        );
                })
                .bind("remove.jstree", function (e, data) {
                        data.rslt.obj.each(function () {
                                $.ajax({
                                        async : false,
                                        type: 'POST',
                                        url: "/directory/server",
                                        data : { 
                                                "operation" : "remove_node", 
                                                "id" : this.id
                                        }, 
                                        success : function (r) {
                                                if(!r.status) {
                                                        data.inst.refresh();
                                                }
                                        }
                                });
                        });
                })
                .bind("rename.jstree", function (e, data) {
                        $.post(
                                "/directory/server", 
                                { 
                                        "operation" : "rename_node", 
                                        "id" : data.rslt.obj.attr("id"),
                                        "title" : data.rslt.new_name
                                }, 
                                function (r) {
                                        if(!r.status) {
                                                $.jstree.rollback(data.rlbk);
                                        }
                                }
                        );
                })
                .bind("move_node.jstree", function (e, data) {
                        data.rslt.o.each(function (i) {
                                $.ajax({
                                        async : false,
                                        type: 'POST',
                                        url: "/directory/server",
                                        data : { 
                                                "operation" : "move_node", 
                                                "id" : $(this).attr("id"), 
                                                "ref" : data.rslt.np.attr("id"), 
                                                "position" : data.rslt.cp + i,
                                                "title" : data.rslt.name,
                                                "copy" : data.rslt.cy ? 1 : 0
                                        },
                                        success : function (r) {
                                                if(!r.status) {
                                                        $.jstree.rollback(data.rlbk);
                                                }
                                                else {
                                                        $(data.rslt.oc).attr("id", r.id);
                                                        if(data.rslt.cy && $(data.rslt.oc).children("UL").length) {
                                                                data.inst.refresh(data.inst._get_parent(data.rslt.oc));
                                                        }
                                                }
                                        }
                                });
                        });
                });
});

</script>
