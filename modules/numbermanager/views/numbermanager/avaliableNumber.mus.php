<li id="avaliable_number_{{number_id}}" class="avaliable_number">

    <div style="float: right; margin-top: 10px;" class="field">

        <label for="avaliable_number_{{number_id}}" class="label" id="label_avaliable_number" style="width:160px; font-weight:normal; margin-top: ">Assign this number</label>

        <input type="checkbox" class="checkbox input" value="{{number_id}}" name="numbers[unused][]" id="avaliable_number_{{number_id}}" style="margin-right:15px;">

    </div>

    <div><span>{{number}}</span></div>

    <span style="color:#666666; font-size: .8em; padding-left: 15px;">{{number_type_description}}</span>

    <input type="hidden" value="{{number}}" class="number_datastore" name="numbers[avaliable][{{number_id}}][number]"/>

    <input type="hidden" value="{{number_id}}" class="number_id_datastore" name="numbers[avaliable][{{number_id}}][number_id]"/>

    <input type="hidden" value="{{class_type}}" class="number_class_datastore" name="numbers[avaliable][{{number_id}}][class_type]"/>

</li>