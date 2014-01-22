<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <base href="<?php echo $SCHEME . '://' . $HOST . ':' . $PORT . $BASE . '/'; ?>">
  <meta name="viewport" content="width=device-width">
  <title>eXecutr</title>

  <link rel="shortcut icon" href="favicon.ico">

  <link rel="stylesheet" type="text/css" media="all" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/themes/smoothness/jquery-ui.min.css" />
  <link rel="stylesheet" type="text/css" media="all" href="css/general.css" />

  <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
  <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>

  <script src="js/jquery.hotkeys.js" type="text/javascript"></script>

  <script src="js/global.js" type="text/javascript"></script>
  <script src="js/next-actions.js" type="text/javascript"></script>
  <script type="text/javascript">
      $(document).ready(function(){
        eXecutr.Global.init();
        eXecutr.NextActions.init();
      });
  </script>
</head>

<body>

<header id="top-header">

  <div id="header-mainmenu">
    <div id="inbox-config-menu">
      <a href="main/weekly_review">[<?php echo Base::instance()->get('lang.WeeklyReviewMenuItem') ?>]</a>
      <a id="inbox-notification" href="main/process_inbox" title="<?php echo Base::instance()->get('lang.InboxNotificationTitle') ?>"><?php
        echo Base::instance()->get('lang.InboxNotificationLabel');
      ?>
        <span id="inbox-count"><?php echo $inboxItems ?></span>
      </a>
    </div>

    <a id="logo" href=".">eXecutr</a>

    <ul id="add-items-menu">
        <li><a href="items/create_inbox_item" id="add-inbox-item" data-key="shift+i">+<?php echo Base::instance()->get('lang.AddInboxMenuItem') ?></a></li>
        <li><a href="items/create_action" id="add-action-item" data-key="shift+a">+<?php echo Base::instance()->get('lang.AddActionMenuItem') ?></a></li>
        <li><a href="items/create_waiting_for" id="add-waiting-for-item" data-key="shift+w">+<?php echo Base::instance()->get('lang.AddWaitingForMenuItem') ?></a></li>
        <li><a href="items/create_project" id="add-project-item" data-key="shift+p">+<?php echo Base::instance()->get('lang.AddProjectMenuItem') ?></a></li>
    </ul>
  </div>

<?php
if (isset($showContextMenu)) {
  echo \View::instance()->render('components/context_menu.php');
}
?>

</header>
