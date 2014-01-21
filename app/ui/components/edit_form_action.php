<?php
// If there are more parent items, choose first one to determine context
if (isset($parentItems))
    $mainParentItem = $parentItems[0];
?>

<form method="post" action="items/save_action" class="edition-form">
<fieldset class="columns2">

<h1><?php echo Base::instance()->get('lang.EditActionTitle') ?></h1>

<table class="form">
<tbody>
    <tr>
        <td colspan="2">
            <label for="title"><?php echo Base::instance()->get('lang.ItemTitleLabel') ?></label>
            <input type="text" id="title" name="title" value="<?php if (isset($item)) echo $item->title ?>" />
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <label for="parent-name"><?php echo Base::instance()->get('lang.ItemParentLabel') ?></label>
            <ul id="item-parents" class="autocomplete-added">
<?php if (isset($parentItems)): ?>
<?php foreach ($parentItems as $parentItem): ?>
                <li>
                    <?php echo $parentItem->title ?>
                    <input type="hidden" name="parentIds[]" value="<?php echo $parentItem->itemId ?>">
                    <a href="#">x</a>
                </li>
<?php endforeach; ?>
<?php endif; ?>
            </ul>
            <input type="text" id="parent-name" name="parentName" class="select" data-url="items/create_parent" data-autocomplete="main/find_parents">
        </td>
    </tr>
</tbody>
<tbody>
    <tr>
        <td>
            <label for="is-next">
                <input type="checkbox" id="is-next" checked="checked" name="isNext" data-default="checked">
                <?php echo Base::instance()->get('lang.ItemNextActionLabel') ?>
            </label>
        </td>
        <td>
            <label for="is-someday">
                <input type="checkbox" id="is-someday" name="isSomeday" />
                <?php echo Base::instance()->get('lang.SomedayMaybeLabel') ?>
            </label>
        </td>
    </tr>
    <tr>
        <td>
            <label for="space-context"><?php echo Base::instance()->get('lang.ItemSpaceContextLabel') ?></label>
            <select id="space-context" name="spaceContext">
                <option value="0">&nbsp;</option>
<?php foreach ($spaceContexts as $context): ?>
                <option value="<?php echo $context->contextId ?>"><?php echo $context->name ?></option>
<?php endforeach;?>
            </select>
        </td>
        <td>
            <label for="time-context"><?php echo Base::instance()->get('lang.ItemTimeContextLabel') ?></label>
            <select id="time-context" name="timeContext">
                <option value="0">&nbsp;</option>
<?php foreach ($timeContexts as $context): ?>
                <option value="<?php echo $context->contextId ?>"><?php echo $context->name ?></option>
<?php endforeach;?>
            </select>
        </td>
    </tr>
    <tr>
        <td>
            <label for="tickle-date"><?php echo Base::instance()->get('lang.ItemTickleDateLabel') ?></label>
            <input type="text" id="tickle-date" name="tickleDate" class="datepicker" autocomplete="off">
        </td>
        <td>
            <label for="deadline"><?php echo Base::instance()->get('lang.ItemDeadlineLabel') ?></label>
            <input type="text" id="deadline" name="deadline" class="datepicker" autocomplete="off" value="<?php if (isset($mainParentItem)) echo $mainParentItem->deadline ?>">
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <label for="description"><?php echo Base::instance()->get('lang.ItemDescriptionLabel') ?></label>
            <textarea id="description" name="description" rows="5" cols="50"><?php if (isset($item)) echo $item->description ?></textarea>
        </td>
    </tr>
<?php echo \View::instance()->render('components/edit_form_recurrence.php'); ?>
    <tr>
        <td>
            <label for="category"><?php echo Base::instance()->get('lang.ItemCategoryLabel') ?></label>
            <select name="categoryId" id="category">
                <option value="0">&nbsp;</option>
<?php foreach ($categories as $context): ?>
                <option value="<?php echo $context->categoryId ?>" <?php if (isset($mainParentItem) && $mainParentItem->categoryId === $context->categoryId) echo 'selected="selected"' ?>><?php echo $context->name ?></option>
<?php endforeach;?>
            </select>
        </td>
    </tr>
</tbody>
</table>

    <input type="hidden" name="itemId" value="<?php if (isset($item)) echo $item->itemId ?>" />

<?php echo \View::instance()->render('components/next_action_buttons.php') ?>

</fieldset>
</form>