<li id="member_{{type}}_{{id}}" class="ringgroup_member">

    <div style="float: right; margin-top: 10px;" class="field">

        <label for="checkbox_{{type}}_{{id}}" class="label" id="label_checkbox_{{type}}_{{id}}" style="width:160px; font-weight:normal; margin-top: ">Assign this {{type}}</label>

        <input type="checkbox" class="checkbox input {{type}}" rel="{{type}}" value="{{id}}" name="ringgroup[members][][id]" id="checkbox_{{type}}_{{id}}" style="margin-right:15px;">

    </div>

    <div><span>{{display_name}}</span></div>

    <span style="color:#666666; font-size: .8em; padding-left: 15px;">{{display_type}}</span>

</li>
