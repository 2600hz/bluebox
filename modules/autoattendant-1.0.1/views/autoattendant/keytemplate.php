<div id="key_{{key_number}}" class="autoattendant_key">

    Option <input type="text" name="autoattendant[keys][{{key_number}}][digits]" class="input" value="{{digits}}" /> transfers to a

    <select id="key_{{key_number}}_class_type" class="pools_dropdown dropdown" name="key_{{key_number}}_class_type">

        {{#number_pools}}

            <option title="{{value}}" value="{{value}}">{{text}}</option>

        {{/number_pools}}

    </select>

    named

    <select id="key_{{key_number}}_number" class="destination_dropdown dropdown" name="autoattendant[keys][{{key_number}}][number_id]">

        {{#destinations}}

            <option class="{{class}}" value="{{value}}">{{text}}</option>

        {{/destinations}}
        
    </select>

    <span id="remove_key_{{key_number}}" class="remove_key">&nbsp;</span>

</div>