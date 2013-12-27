<div id="header-submenu">
  <ul id="space-context-menu">
<?php $i = 0; foreach($nextActions as $action => $times): $i++; ?>
    <li><a href="#" data-name="space-context-<?php echo $i ?>"><?php echo $action ?></a></li>
<?php endforeach; ?>
  </ul>

  <ul id="time-context-menu">
    <li><a href="#" data-name="time-context-Short">Short</a></li>
    <li><a href="#" data-name="time-context-Medium">Medium</a></li>
    <li><a href="#" data-name="time-context-Long">Long</a></li>
  </ul>
</div>