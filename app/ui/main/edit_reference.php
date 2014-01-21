<?php echo View::instance()->render('header.php'); ?>

<div id="main" class="columns-1" role="main">

<div class="col">

<form method="post" action="items/save_reference" class="edition-form">
<fieldset class="columns2">

<h1><?php echo Base::instance()->get('lang.EditReferenceTitle') ?></h1>

<table class="form">
<tbody>
    <tr>
        <td colspan="2">
            <label for="title"><?php echo Base::instance()->get('lang.ItemTitleLabel') ?></label>
            <input type="text" id="title" name="title" value="<?php echo $item->title ?>" />
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
            <label for="description"><?php echo Base::instance()->get('lang.ItemDescriptionLabel') ?></label>
            <textarea id="description" name="description" rows="5" cols="50"><?php echo $item->description ?></textarea>
        </td>
    </tr>
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

    <input type="hidden" name="itemId" value="<?php echo $item->itemId ?>" />
    
    <?php echo View::instance()->render('components/next_action_buttons.php') ?>
</fieldset>
</form>

</div>
</div>

 <?php echo View::instance()->render('footer.php'); ?>