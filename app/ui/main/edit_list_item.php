<?php echo View::instance()->render('header.php'); ?>

<div id="main" class="columns-1" role="main">

<div class="col">

<form method="post" action="main/save_list_item" class="edition-form">
<fieldset class="columns-2">

<h1><?php echo Base::instance()->get('lang.EditListItemTitle') ?></h1>

<table class="form">
<tbody>
    <tr>
        <td>
            <label for="title"><?php echo Base::instance()->get('lang.ItemTitleLabel') ?></label>
            <input type="text" id="title" name="title" value="<?php echo $item->title ?>" />
        </td>
    </tr>
    <tr>
        <td>
            <label for="parent-name"><?php echo Base::instance()->get('lang.ItemParentLabel') ?></label>
            <ul id="item-parents" class="autocomplete-added"></ul>
            <input type="text" id="parent-name" name="parentName" class="select" />
        </td>
    </tr>
</tbody>
<tbody>
    <tr>
        <td>
            <label for="description"><?php echo Base::instance()->get('lang.ItemDescriptionLabel') ?></label>
            <textarea id="description" name="description" rows="5" cols="50"><?php echo $item->description ?></textarea>
        </td>
    </tr>
</tbody>
</table>

    <input type="hidden" name="itemId" value="<?php echo $item->itemId ?>" />
    <input type="hidden" name="autoCompleteSource" id="autocomplete-source" value="main/find_parent_lists"/>

    <?php echo View::instance()->render('components/next_action_buttons.php') ?>

</fieldset>
</form>

</div>
</div>

<?php echo View::instance()->render('footer.php'); ?>
