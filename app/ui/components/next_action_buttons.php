<?php if (isset($flow)): ?>
	<input type="hidden" name="flow" value="<?php echo $flow ?>">
	<input type="hidden" name="flowStep" value="<?php echo $flowStep ?>">
<?php endif; ?>
<?php foreach ($nextActions as $action): ?>
    <button type="submit" name="<?php echo isset($action['action']) ? 'nextAction' : 'submit' ?>" <?php if (isset($action['action'])) echo 'value="' . $action['action'] . '"' ?> <?php if (isset($action['default']) && $action['default']) echo 'class="default-button"' ?>>
        <?php echo Base::instance()->get('lang.'.$action['label']) ?>
    </button>
<?php endforeach; ?>
