
<?php echo \View::instance()->render('header.php') ?>

<div id="main" class="columns-2" role="main">

  <div id="next-actions" class="col">
    <h2><?php echo Base::instance()->get('NextActionsTitle') ?></h2>

    <form method="post" action="main/mark_completed" class="completion-form">
<?php $i = 0; foreach($nextActions as $space => $actions): ++$i; ?>
    <div class="space-context-<?php echo $i ?>">
      <h3>@<?php echo $space ?></h3>
      <table class="report">
   <?php foreach ($actions as $action): ?>
      <tr class="time-context-<?php echo $action->timeContext ?>">
        <td><input type="checkbox" name="itemId" value="<?php echo $action->actionId ?>"/></td>
        <td>
            <a href="#" class="table-cell-button"><?php echo $action->title ?></a>
            <div class="properties-float"><div class="inner-float">
              <?php if (!empty($action->parentTitles)): ?>
              <a href="#" class="project-button"><?php echo $action->parentTitles ?></a>
              <?php endif; ?>
              <?php if (!empty($action->description)): ?>
              <div class="description-button"><?php echo nl2br($action->description) ?></div>
              <?php endif; ?>
            </div></div>
        </td>
        <td>
<?php
  if (!empty($action->deadline)):
    if ($action->deadline == date('Y-m-d'))
        $class = "today";
    else if ($action->deadline < date('Y-m-d'))
        $class = "past";
    else if ($action->deadline <= date('Y-m-d', strtotime("+7 days")))
        $class = "soon";
    else
        $class = "";
?>
        <span class="deadline <?php echo $class ?>"><?php echo \helpers\HumaneDateView::getHumanReadable($action->deadline) ?></span>
<?php
  endif;
  if (!empty($action->recurdesc)):
?>
        <a href="#" title="<?php echo $action->recurdesc ?>" class="recurrence-button">R</a>
<?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>

      </table>
    </div>
<?php endforeach; ?>
    </form>

  </div>
  <div id="waiting-fors" class="col">

    <h2><?php echo Base::instance()->get('WaitingForsTitle') ?></h2>

    <form method="post" action="main/mark_completed" class="completion-form">
    <table class="report">
<?php foreach($waitingFors as $waitingFor): ?>
    <tr>
      <td><input type="checkbox" name="itemId" value="<?php echo $waitingFor->actionId ?>"/></td>
      <td>
        <a href="#" class="table-cell-button"><?php echo $waitingFor->title ?></a>
        <div class="properties-float"><div class="inner-float">
          <?php if (!empty($waitingFor->parentTitles)): ?>
          <a href="#" class="project-button"><?php echo $waitingFor->parentTitles ?></a>
          <?php endif; ?>
          <?php if (!empty($waitingFor->description)): ?>
          <div class="description-button"><?php echo nl2br($waitingFor->description) ?></div>
          <?php endif; ?>
        </div></div>
      </td>
      <td>
<?php
  if (!empty($waitingFor->deadline)):
    if ($waitingFor->deadline == date('Y-m-d'))
        $class = "today";
    else if ($waitingFor->deadline < date('Y-m-d'))
        $class = "past";
    else if ($waitingFor->deadline <= date('Y-m-d', strtotime("+7 days")))
        $class = "soon";
    else
        $class = "";
?>
        <span class="deadline <?php echo $class ?>"><?php echo \helpers\HumaneDateView::getHumanReadable($waitingFor->deadline) ?></span>
<?php
  endif;
  if (!empty($waitingFor->recurdesc)):
?>
        <a href="#" title="<?php echo $waitingFor->recurdesc ?>" class="recurrence-button">R</a>
<?php endif; ?>
      </td>
    </tr>
<?php endforeach; ?>
    </form>

    </table>
  </div>

</div>

<?php echo \View::instance()->render('footer.php') ?>
