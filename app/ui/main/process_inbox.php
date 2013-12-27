<?php echo \View::instance()->render('header.php') ?>

<div id="main" class="columns-1" role="main">

<div class="col">

<?php if (!empty($inboxItem)): ?>
<form method="post" action="main/process_inbox_decision" class="edition-form" id="inbox-processing-form">
<fieldset class="columns-2">

    <h1><?php $ProcessInboxTitle ?></h1>

    <input type="hidden" name="itemId" value="<?php echo $inboxItem->itemId ?>" />
    <span class="label"><?php echo $ItemTitleLabel ?></span>
    <div class="field"><?php echo $inboxItem->title ?></div>
    <span class="label"><?php echo $ItemDescriptionLabel ?></span>
    <div class="field"><?php echo empty($inboxItem->description) ? $FieldEmpty : nl2br($inboxItem->description) ?></div>
    <span class="question"><?php echo $IsActionableQuestion ?></span>
    
    <table class="form" id="inbox-process-decisions">
    <tr>
        <td>
            <span class="title"><?php echo $No ?></span>
            <button type="submit" id="delete-button" data-key="d" name="decision" value="delete" class="eject-button"><?php echo View::instance()->raw($DeleteButtonLabel) ?></button>
            <button type="submit" id="reference-button" data-key="r" name="decision" value="reference"><?php echo View::instance()->raw($ReferenceButtonLabel) ?></button>
            <button type="submit" id="list-button" data-key="l" name="decision" value="list"><?php echo View::instance()->raw($ListButtonLabel) ?></button>
            <button type="submit" id="checklist-button" data-key="k" name="decision" value="checklist"><?php echo View::instance()->raw($ChecklistButtonLabel) ?></button>
            <button type="submit" id="list-item-button" data-key="i" name="decision" value="listItem"><?php echo View::instance()->raw($ListItemButtonLabel) ?></button>
        </td>
        <td>
            <span class="title"><?php echo $Yes ?></span>
            <button type="submit" id="complete-button" data-key="c" name="decision" value="complete" class="default-button"><?php echo View::instance()->raw($CompleteButtonLabel) ?></button>
            <button type="submit" id="action-button" data-key="a" name="decision" value="action"><?php echo View::instance()->raw($ActionButtonLabel) ?></button>
            <button type="submit" id="waiting-for-button" data-key="w" name="decision" value="waitingFor"><?php echo View::instance()->raw($WaitingForButtonLabel) ?></button>
            <button type="submit" id="project-button" data-key="p" name="decision" value="project"><?php echo View::instance()->raw($ProjectButtonLabel) ?></button>
        </td>
    </tr>
    </table>
</fieldset>
</form>
<?php else: ?>
<p><?php echo $InboxEmptyMsg ?></p>
<?php endif; ?>

</div>
</div>

<?php echo \View::instance()->render('footer.php') ?>
