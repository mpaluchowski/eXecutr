<tr>
    <td colspan="2">
        <input type="hidden" name="createParent" value="true" />
        <label for="new-project-title"><?php echo Base::instance()->get('lang.ProjectTitleLabel') ?></label>
        <input type="text" id="new-project-title" name="newProjectTitle" />
    </td>
</tr>
<tr>
    <td colspan="2">
        <label for="new-project-outcome"><?php echo Base::instance()->get('lang.ProjectOutcomeLabel') ?></label>
        <textarea id="new-project-outcome" name="newProjectOutcome" rows="2" cols="50"></textarea>
    </td>
</tr>
<tr>
    <td colspan="2">
        <label for="is-project-someday">
            <input type="checkbox" id="is-project-someday" name="isProjectSomeday" />
            <?php echo Base::instance()->get('lang.SomedayMaybeLabel') ?>
        </label>
    </td>
</tr>
<tr>
    <td colspan="2">
        <label for="new-project-description"><?php echo Base::instance()->get('lang.ProjectDescriptionLabel') ?></label>
        <textarea id="new-project-description" name="newProjectDescription" rows="5" cols="50"></textarea>
    </td>
</tr>
<tr>
    <td>
        <label for="new-project-deadline"><?php echo Base::instance()->get('lang.ProjectDeadlineLabel') ?></label>
        <input type="text" id="new-project-deadline" name="newProjectDeadline" class="datepicker" autocomplete="off" />
    </td>
    <td>
        <label for="new-project-category"><?php echo Base::instance()->get('lang.ProjectCategoryLabel') ?></label>
        <select id="new-project-category" name="newProjectCategoryId" >
            <option value="0">&nbsp;</option>
    <?php foreach ($categories as $context): ?>
            <option value="<?php echo $context->categoryId ?>"><?php echo htmlspecialchars($context->name) ?></option>
    <?php endforeach;?>
        </select>
    </td>
</tr>