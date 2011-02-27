<?php defined('SYSPATH') or die('No direct access allowed.'); ?>
<!-- List all available folders -->
<div>
<?php echo form::open();
      message::render(); ?>
	<div>
		<h2 class="txt-center">
			<?php echo Kohana::lang('voicemail.index.header'); ?>
		</h2>
	</div>
	
	<div class="three-fourths pos-center voicemailActions">
		<?php echo form::dropdown('action', array('mark_read' => Kohana::lang('voicemail.index.mark_read'), 
						  'mark_unread' => Kohana::lang('voicemail.index.mark_unread'), 
						  'mark_urgent_read' => Kohana::lang('voicemail.index.mark_urgent_read'), 
						  'mark_urgent_unread' => Kohana::lang('voicemail.index.mark_urgent_unread'), 
						  'delete' => Kohana::lang('voicemail.index.delete'),
						  'archive' => Kohana::lang('voicemail.index.archive')));
		      echo form::submit('go', Kohana::lang('voicemail.index.go')); ?>
		<p class="clear">&nbsp;</p>
	</div>
	
	<?php 
	foreach($allMessages as $message) {	
	?>
		<div class="voicemail three-fourths pos-center">

			
			<div>
				<?php echo form::checkbox('uuid[]',$message['uuid']);
				      echo VoicemailManager::getMessageStatus($message['uuid'], 'style="position: relative; top: -60px; left: -50px;"'); ?>
			</div>	
			
			<div class="flt-left margin-horz">
				<img class="margin" src="<?php echo url::base(); ?>modules/voicemail/assets/unknown_contact.jpg" />
				
			</div>
			<div class="flt-left margin-horz txt-small">	
				<ul>
					<li><b><?php echo Kohana::lang('voicemail.index.to'); ?>: </b> <?php echo $message['username'];?>@<?php echo $message['domain'];?></li>
					<li><b><?php echo Kohana::lang('voicemail.index.from'); ?>:  </b> <?php echo $message['cid_name'];?> &lt;<?php echo $message['cid_number'];?>&gt;</li>
					<li><b><?php echo Kohana::lang('voicemail.index.time'); ?> : </b><?php echo date('n/j/y h:i a', $message['created_epoch']);?> (<?php echo VoicemailManager::prettyDate($message['created_epoch'], true);?>)</li>
					<li><b><?php echo Kohana::lang('voicemail.index.duration'); ?> : </b><?php echo sprintf ("%d:%2.1f", floor ($message['message_len'] / 60), $message['message_len'] % 60);?> (m:s)</li>
					<li><b><?php echo Kohana::lang('voicemail.index.label'); ?> : </b><?php echo ucwords($message['in_folder']);?></li>
				</ul>
			</div>
			<div class="flt-right txt-center" >
			<audio class="player" id="<?php echo $message['uuid'];?>" style="width: 175px" class="margin" controls="true" src="<?php echo url::site('voicemail/listen/' . $message['uuid']);?>">
					<?php echo Kohana::lang('voicemail.index.unsupported_html5');?> <a href="http://www.firefox.com">Get Firefox 3.5</a>
			</audio>
			</div>
			<p class="clear">&nbsp;</p>
			<div class="clearfix">&nbsp;</div> 
		</div>
		
	<div class="clearfix">&nbsp;</div>

	<?php } ?>
	
	<div id="messageSummary" class="pos-center three-fourths clear">
	<table width="100%" class="fancy">
		<tr>
			<th width="20%">
				<?php echo Kohana::lang('voicemail.index.device');?>
			</th>
			<th width="20%">
				<?php echo Kohana::lang('voicemail.index.new');?><img class="margin-horz" src="<?php echo url::base();?>modules/voicemail/assets/new.png" border="0" />
			</th>
			<th width="20%">
				<?php echo Kohana::lang('voicemail.index.old');?> <img class="margin-horz" src="<?php echo url::base();?>modules/voicemail/assets/saved.png" border="0" />
			</th>
			<th width="20%">
				<?php echo Kohana::lang('voicemail.index.new_urgent');?> <img class="margin-horz" src="<?php echo url::base();?>modules/voicemail/assets/urgent.png" border="0" />
			</th>
			<th width="20%">
				<?php echo Kohana::lang('voicemail.index.old_urgent');?> <img class="margin-horz" src="<?php echo url::base();?>modules/voicemail/assets/saved_urgent.png" border="0" />
			</th>
		</tr>
		<?php echo $messageCount;?>	
	</table>
	
	</div>
	
	
<?php echo form::close();?>
</div>
<?php javascript::codeBlock(); ?>
    $(".player").bind("click", function(){
        var uuid = $(this).attr('id');
        //send mark as unread request
        $.post("<?php echo url::site('voicemail/status');?>", { "uuid": uuid, "status" : "mark_read" } );
    });
<?php javascript::blockEnd(); ?>
