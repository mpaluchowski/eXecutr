<form method="post" action="items/save_inbox_item" class="edition-form">
<fieldset>

<table class="form">
<tr><td>
        <label for="title"><?php echo Base::instance()->get('lang.ItemTitleLabel') ?></label>
        <input type="text" id="title" name="title" />
</td></tr>
<tr><td>
        <label for="description"><?php echo Base::instance()->get('lang.ItemDescriptionLabel') ?></label>
        <textarea id="description" name="description" rows="5" cols="50"></textarea>
</td></tr>
</table>

    <button type="submit" name="submit" class="default-button">
    	<?php echo Base::instance()->get('lang.CreateButton') ?>
    </button>
</fieldset>
</form>
