<li id="member_{{type}}_{{id}}" class="ringgroup_member">
    
    <span class="ui-icon ui-icon-arrowthick-2-n-s sort_handle"></span>

    <ul>

        <li>

            <span>{{display_name}}</span>

            <span style="color:#666666; font-size: .8em; padding-left: 15px;">{{display_type}}</span>

        </li>

        <li style="float:right;">

            <label for="checkbox_{{type}}_{{id}}" class="label" id="label_checkbox_{{type}}_{{id}}" style="width:160px; font-weight:normal; margin-top: ">Assign this {{type}}</label>

            <input type="checkbox" class="checkbox input {{type}}" rel="{{type}}" value="{{id}}" name="ringgroup[members][{{type}}][][id]" id="checkbox_{{type}}_{{id}}" style="margin:3px 0 0 15px;">

        </li>

    </ul>

</li>