<div id="powerdns_edit_header" class="edit powerdns module_header">
    <h2><span class="helptip"></span><?php echo __($title); ?></h2>
</div>

<div id="powerdns_edit_form" class="txt-left form powerdns edit">
    <?php echo form::open(); ?>

    <?php echo form::open_section('Domain'); ?>

        <div class="field">
            <?php echo form::label('pdnsdomain[name]', 'Name:'); ?>
            <?php echo form::input(array('name' => 'pdnsdomain[name]')); ?>
        </div>

    <?php echo form::close_section(); ?>

    <?php echo form::open_section('Records'); ?>

        <div id="powerdns_table" class="powerdns_records field">

            <?php $iteration = 0; foreach($records as $record_id => $record): $iteration++; ?>

                <div id="record_<?php echo $iteration; ?>" class="record">
                    <div class="field record_name">
                        <?php echo form::label('pdnsrecord[' .$iteration .'][name]', 'Record:'); ?>
                        <?php if ($record['type'] == 'SOA'): ?>
                        <div class="record_soa">&nbsp;</div> <?php echo $pdnsdomain['name']; ?>
                        <?php else: ?>
                            <?php echo form::input(array('name' => 'pdnsrecord[' .$iteration .'][name]'), $record['name']) .'.' .$pdnsdomain['name']; ?>
                        <?php endif; ?>
                    </div>
                    <span class="remove_record"></span>

                    <div class="field record_type">
                        <?php echo form::label('pdnsrecord[' .$iteration .'][type]', 'Type:'); ?>
                        <?php echo form::dropdown('pdnsrecord[' .$iteration .'][type]', $recordTypes, $record['type']); ?>
                    </div>

                    <div class="field record_ttl">
                        <?php echo form::label('pdnsrecord[' .$iteration .'][ttl]', 'TTL:'); ?>
                        <?php echo form::input(array('name' => 'pdnsrecord[' .$iteration .'][ttl]'), $record['ttl']); ?>
                    </div>

                    <div class="field record_prio">
                        <?php echo form::label('pdnsrecord[' .$iteration .'][prio]', 'Priority:'); ?>
                        <?php echo form::input(array('name' => 'pdnsrecord[' .$iteration .'][prio]'), $record['prio']); ?>
                    </div>

                    <div class="field record_content">
                        <?php echo form::label('pdnsrecord[' .$iteration .'][content]', 'Content:'); ?>
                        <?php echo form::input(array('name' => 'pdnsrecord[' .$iteration .'][content]', 'class' => 'pdnsrecord_content'), $record['content']); ?>
                    </div>

                </div>

            <?php endforeach; ?>
            
            <?php jquery::addQuery('#powerdns_table .remove_record')->click('function (e) { $(this).parent().slideUp(\'500\', function () { $(this).remove() });}'); ?>

        </div>

        <div class="new_record_container">
            <?php echo '<a href="' . url::current() .'" id="new_record" class="nxt_aval_link"><span>New DNS Record</span></a>'; ?>
        </div>

        <div id="record_template" class="record hide">
            <div class="field record_name">
                <?php echo form::label('pdnsrecord_name', 'Record:'); ?>
                <?php echo form::input(array('name' => 'pdnsrecord_name')) .'.' .$pdnsdomain['name']; ?>
            </div>
            <span class="remove_record"></span>
            
            <div class="field record_type">
                <?php echo form::label('pdnsrecord_type', 'Type:'); ?>
                <?php echo form::dropdown('pdnsrecord_type', $recordTypes); ?>
            </div>

            <div class="field record_ttl">
                <?php echo form::label('pdnsrecord_ttl', 'TTL:'); ?>
                <?php echo form::input(array('name' => 'pdnsrecord_ttl')); ?>
            </div>

            <div class="field record_prio">
                <?php echo form::label('pdnsrecord_prio', 'Priority:'); ?>
                <?php echo form::input(array('name' => 'pdnsrecord_prio')); ?>
            </div>

            <div class="field record_content">
                <?php echo form::label('pdnsrecord_content', 'Content:'); ?>
                <?php echo form::input(array('name' => 'pdnsrecord_content', 'class' => 'pdnsrecord_content')); ?>
            </div>
            
        </div>

        <?php javascript::codeBlock(); ?>
            var divCount = $('.powerdns_records > div').length;

            $('#new_record').click(function (e) {
                e.preventDefault();
                newRecord = $('#record_template').clone().appendTo('#powerdns_table');

                divCount++;

                newRecord.attr('id', 'record_' + divCount);
                newRecord.find('#pdnsrecord_name').attr('id', 'pdnsrecord_' + divCount + '_name').attr('name', 'pdnsrecord[' + divCount + '][name]');
                newRecord.find('#pdnsrecord_type').attr('id', 'pdnsrecord_' + divCount + '_type').attr('name', 'pdnsrecord[' + divCount + '][type]');
                newRecord.find('#pdnsrecord_ttl').attr('id', 'pdnsrecord_' + divCount + '_ttl').attr('name', 'pdnsrecord[' + divCount + '][ttl]');
                newRecord.find('#pdnsrecord_prio').attr('id', 'pdnsrecord_' + divCount + '_prio').attr('name', 'pdnsrecord[' + divCount + '][prio]');
                newRecord.find('#pdnsrecord_content').attr('id', 'pdnsrecord_' + divCount + '_content').attr('name', 'pdnsrecord[' + divCount + '][content]');


                newRecord.find('.remove_record').click(function (e) {
                    $(this).parent().slideUp('500', function () { $(this).remove() });
                });

                newRecord.slideDown();
            });
        <?php javascript::blockEnd(); ?>

    <?php echo form::close_section(); ?>

    <div class="buttons form_bottom">
        <?php echo form::button(array('name' => 'submit', 'class' => 'cancel small_red_button'), 'Cancel'); ?>
        <?php echo form::submit(array('name' => 'submit', 'class' => 'save small_green_button'), 'Save'); ?>
    </div>

    <?php echo form::close(); ?>
</div>