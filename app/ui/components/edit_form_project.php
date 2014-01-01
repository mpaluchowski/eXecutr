<form method="post" action="main/save_project" class="edition-form">
<fieldset class="columns-2">

<h1><?php echo Base::instance()->get('lang.EditProjectTitle') ?></h1>

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
            <ul id="item-parents" class="autocomplete-added"></ul>
            <input type="text" id="parent-name" name="parentName" class="select" data-autocomplete="main/find_parents">
        </td>
    </tr>
</tbody>
<tbody>
    <tr>
        <td colspan="2">
            <label for="outcome"><?php echo Base::instance()->get('lang.ItemOutcomeLabel') . " (" . Base::instance()->get('lang.ItemOutcomeTip') . ")" ?></label>
            <textarea id="outcome" name="outcome" rows="2" cols="50"></textarea>
        </td>
    </tr>
    <tr>
        <td>
            <label for="tickle-date"><?php echo Base::instance()->get('lang.ItemTickleDateLabel') ?></label>
            <input type="text" id="tickle-date" name="tickleDate" class="datepicker" autocomplete="off" />
        </td>
        <td>
            <label for="deadline"><?php echo Base::instance()->get('lang.ItemDeadlineLabel') ?></label>
            <input type="text" id="deadline" name="deadline" class="datepicker" autocomplete="off" />
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <label for="is-someday">
                <input type="checkbox" id="is-someday" name="isSomeday" />
                <?php echo Base::instance()->get('lang.SomedayMaybeLabel') ?>
            </label>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <label for="description"><?php echo Base::instance()->get('lang.ItemDescriptionLabel') ?></label>
            <textarea id="description" name="description" rows="5" cols="50"><?php if (isset($item)) echo $item->description ?></textarea>
        </td>
    </tr>
<?php echo View::instance()->render('components/edit_form_recurrence.php'); ?>
    <tr>
        <td>
            <label for="category"><?php echo Base::instance()->get('lang.ItemCategoryLabel') ?></label>
            <select name="categoryId" id="category">
                <option value="0">&nbsp;</option>
    <?php foreach ($categories as $context): ?>
                <option value="<?php echo $context->categoryId ?>"><?php echo $context->name ?></option>
    <?php endforeach;?>
            </select>
        </td>
    </tr>
</tbody>
</table>

    <input type="hidden" name="itemId" value="<?php if (isset($item)) echo $item->itemId ?>" />
    
    <?php echo View::instance()->render('components/next_action_buttons.php') ?>

</fieldset>
</form>