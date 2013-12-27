<a href="main/process_inbox" title="<?php echo Base::instance()->get('lang.InboxNotificationTitle') ?>"><?php 
	echo Base::instance()->get('lang.InboxNotificationLabel');
	if ($inboxItems != 0):
?>
	<span id="inbox-count"><?php echo $inboxItems ?></span>
<?php
	endif;
?></a>