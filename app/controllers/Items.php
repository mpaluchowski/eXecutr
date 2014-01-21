<?php

namespace controllers;

class Items
{
	public function create_parent($f3) {
		$actionModel = new \models\Action();

		\F3::set('categories', $actionModel->getCategories());

		echo \View::instance()->render('items/create_parent.php');
	}

	public function create_inbox_item($f3) {
		echo \View::instance()->render('items/create_inbox_item.php');
	}

	public function save_inbox_item($f3) {
		$actionModel = new \models\Action();
		$actionModel->createInboxItem(
				$f3->get('POST.title'),
				$f3->get('POST.description')
			);
	}

	public function create_action($f3) {
		$actionModel = new \models\Action();

		$f3->mset([
				'categories' => $actionModel->getCategories(),
				'inboxItems'  => $actionModel->getInboxItemCount(),
				'spaceContexts' => $actionModel->getSpaceContexts(),
				'timeContexts' => $actionModel->getTimeContexts()
			]);
		if (null !== $f3->get('GET.parentId'))
			$f3->set('parentItems', [$actionModel->getItem($f3->get('GET.parentId'))]);

		$flowController = new \helpers\FlowChainController($f3);

		if (null !== $f3->get('GET.flow')) {
			$f3->set('nextActions', $flowController->getNextActions($f3->get('GET.flow'), $f3->get('GET.flowStep')));
			$f3->set('flow', $f3->get('GET.flow'));
			$f3->set('flowStep', $f3->get('GET.flowStep') + 1);
		} else {
			/* Make next actions available */
			$f3->set('nextActions', [[
						'label' => 'CreateButton',
						'default' => true
					]]);
		}

		if ($f3->get('AJAX'))
			echo \View::instance()->render('components/edit_form_action.php');
		else
			echo \View::instance()->render('items/edit_action.php');
	}

	public function save_action($f3) {
		$actionModel = new \models\Action();

		if ($f3->get('POST.createParent')) {
			/* Parent creation requested */
			$projectId = $actionModel->createItem(array(
					'type' => 'p',
					'title' => $f3->get('POST.newProjectTitle'),
					'description' => $f3->get('POST.newProjectDescription'),
					'outcome' => $f3->get('POST.newProjectOutcome'),
					'parentIds' => $f3->get('POST.parentIds'),
					'categoryId' => $f3->get('POST.newProjectCategoryId'),
					'deadline' => $f3->get('POST.newProjectDeadline'),
					'isSomeday' => null !== $f3->get('POST.isProjectSomeday') ? $f3->get('POST.isProjectSomeday') : null
				));
			$parentIds[] = $projectId;
		} else {
			/* Not creating a new parent, use the ones provided */
			$parentIds = null !== $f3->get('POST.parentIds') ? $f3->get('POST.parentIds') : array();
		}

		$recurrence = \helpers\RecurrenceTool::extractRecursionString($f3->get('POST'));

		/* Create new item or update item with the new data */
		$actionProps = array(
				'type' => 'a',
				'title' => $f3->get('POST.title'),
				'description' => $f3->get('POST.description'),
				'parentIds' => $parentIds,
				'spaceContextId' => $f3->get('POST.spaceContext'),
				'timeContextId' => $f3->get('POST.timeContext'),
				'isNext' => null !== $f3->get('POST.isNext') ? $f3->get('POST.isNext') : null,
				'isSomeday' => null !== $f3->get('POST.isSomeday') ? $f3->get('POST.isSomeday') : null,
				'tickleDate' => $f3->get('POST.tickleDate'),
				'deadline' => $f3->get('POST.deadline'),
				'recur' => $recurrence['recur'],
				'recurDesc' => $recurrence['recurDesc'],
				'categoryId' => $f3->get('POST.categoryId')
			);
		if (!$f3->get('POST.itemId'))
			$actionModel->createItem($actionProps);
		else
			$actionModel->updateItem($f3->get('POST.itemId'), $actionProps);

		/* Choose next action */
		if ($f3->get('POST.nextAction'))
			$f3->reroute($f3->get('POST.nextAction'));
		else if (!$f3->get('AJAX'))
			$f3->reroute('/main/index');
	}

	public function create_waiting_for($f3) {
		$actionModel = new \models\Action();

		$f3->mset([
				'categories' => $actionModel->getCategories(),
				'inboxItems'  => $actionModel->getInboxItemCount()
			]);
		if (null !== $f3->get('GET.parentId'))
			$f3->set('parentItems', array($actionModel->getItem($f3->get('GET.parentId'))));

		$flowController = new \helpers\FlowChainController($f3);

		if (null !== $f3->get('GET.flow')) {
			$f3->set('nextActions', $flowController->getNextActions($f3->get('GET.flow'), $f3->get('GET.flowStep')));
			$f3->set('flow', $f3->get('GET.flow'));
			$f3->set('flowStep', $f3->get('GET.flowStep') + 1);
		} else {
			/* Make next actions available */
			$f3->set('nextActions', [[
						'label' => 'CreateButton',
						'default' => true
					]]);
		}

		if ($f3->get('AJAX'))
			echo \View::instance()->render('components/edit_form_waiting_for.php');
		else
			echo \View::instance()->render('items/edit_waiting_for.php');
	}

	public function save_waiting_for($f3) {
		$actionModel = new \models\Action();

		if ($f3->get('POST.createParent')) {
			/* Parent creation requested */
			$projectId = $actionModel->createItem(array(
					'type' => 'p',
					'title' => $f3->get('POST.newProjectTitle'),
					'description' => $f3->get('POST.newProjectDescription'),
					'outcome' => $f3->get('POST.newProjectOutcome'),
					'parentIds' => $f3->get('POST.parentIds'),
					'categoryId' => $f3->get('POST.newProjectCategoryId'),
					'deadline' => $f3->get('POST.newProjectDeadline'),
					'isSomeday' => $f3->get('POST.isProjectSomeday')
				));
			$parentIds[] = $projectId;
		} else {
			/* Not creating a new parent, use the ones provided */
			$parentIds = null !== $f3->get('POST.parentIds') ? $f3->get('POST.parentIds') : array();
		}

		$recurrence = \helpers\RecurrenceTool::extractRecursionString($f3->get('POST'));

		/* Create new item or update item with the new data */
		$waitingForProps = array(
				'type' => 'w',
				'title' => $f3->get('POST.title'),
				'description' => $f3->get('POST.description'),
				'parentIds' => $parentIds,
				'isNext' => null !== $f3->get('POST.isNext') ? $f3->get('POST.isNext') : null,
				'tickleDate' => $f3->get('POST.tickleDate'),
				'deadline' => $f3->get('POST.deadline'),
				'recur' => $recurrence['recur'],
				'recurDesc' => $recurrence['recurDesc'],
				'categoryId' => $f3->get('POST.categoryId')
			);
		if (!$f3->get('POST.itemId'))
			$actionModel->createItem($waitingForProps);
		else
			$actionModel->updateItem($f3->get('POST.itemId'), $waitingForProps);

		/* Choose next action */
		if ($f3->get('POST.nextAction'))
			$f3->reroute($f3->get('POST.nextAction'));
		else if (!$f3->get('AJAX'))
			$f3->reroute('/main/index');
	}

	public function create_project($f3) {
		$actionModel = new \models\Action();

		$f3->mset([
				'categories' => $actionModel->getCategories(),
				'inboxItems'  => $actionModel->getInboxItemCount()
			]);
		if (null !== $f3->get('GET.parentId'))
			$f3->set('parentItems', array($actionModel->getItem($f3->get('GET.parentId'))));

		$flowController = new \helpers\FlowChainController($f3);

		if (null !== $f3->get('GET.flow')) {
			$f3->set('nextActions', $flowController->getNextActions($f3->get('GET.flow'), $f3->get('GET.flowStep')));
			$f3->set('flow', $f3->get('GET.flow'));
			$f3->set('flowStep', $f3->get('GET.flowStep') + 1);
		} else {
			$f3->set('nextActions', $flowController->getNextActions('projectCreate', 0));
			$f3->set('flow', 'projectCreate');
			$f3->set('flowStep', 1);
		}

		if ($f3->get('AJAX'))
			echo \View::instance()->render('components/edit_form_project.php');
		else
			echo \View::instance()->render('items/edit_project.php');
	}

	public function save_project($f3) {
		$actionModel = new \models\Action();
		$parentIds = null !== $f3->get('POST.parentIds') ? $f3->get('POST.parentIds') : array();

		$recurrence = \helpers\RecurrenceTool::extractRecursionString($f3->get('POST'));

		/* Update item with the new data */
		$projectProps = [
				'type' => 'p',
				'title' => $f3->get('POST.title'),
				'description' => $f3->get('POST.description'),
				'parentIds' => $parentIds,
				'outcome' => $f3->get('POST.outcome'),
				'isSomeday' => null !== $f3->get('POST.isSomeday') ? $f3->get('POST.isSomeday') : null,
				'tickleDate' => $f3->get('POST.tickleDate'),
				'deadline' => $f3->get('POST.deadline'),
				'recur' => $recurrence['recur'],
				'recurDesc' => $recurrence['recurDesc'],
				'categoryId' => $f3->get('POST.categoryId')
			];
		if (!$f3->get('POST.itemId'))
			$newItemId = $actionModel->createItem($projectProps);
		else
			$actionModel->updateItem($f3->get('POST.itemId'), $projectProps);

		if ($f3->get('POST.nextAction')) {
			
			$query['parentId'] = $f3->get('POST.itemId') ? $f3->get('POST.itemId') : $newItemId;
			if ($f3->get('POST.flow')) {
				$query['flow'] = $f3->get('POST.flow');
				$query['flowStep'] = $f3->get('POST.flowStep');
			}

			$f3->reroute(
					$f3->get('POST.nextAction')
					. '?'
					. http_build_query($query)
				);

		} else if (!$f3->get('AJAX')) {
			$f3->reroute('/main/index');
		}
	}

	public function save_reference($f3) {
		$actionModel = new \models\Action();

		if ($f3->get('POST.createParent')) {
			/* Parent creation requested */
			$projectId = $actionModel->createItem(array(
					'type' => 'p',
					'title' => $f3->get('POST.newProjectTitle'),
					'description' => $f3->get('POST.newProjectDescription'),
					'outcome' => $f3->get('POST.newProjectOutcome'),
					'parentIds' => $f3->get('POST.parentIds'),
					'categoryId' => $f3->get('POST.newProjectCategoryId'),
					'deadline' => $f3->get('POST.newProjectDeadline'),
					'isSomeday' => null !== $f3->get('POST.isProjectSomeday') ? $f3->get('POST.isProjectSomeday') : null
				));
			$parentIds[] = $projectId;
		} else {
			/* Not creating a new parent, use the ones provided */
			$parentIds = null !== $f3->get('POST.parentIds') ? $f3->get('POST.parentIds') : array();
		}

		/* Update item with the new data */
		$actionModel->updateItem(
				$f3->get('POST.itemId'),
				array(
					'type' => 'r',
					'title' => $f3->get('POST.title'),
					'description' => $f3->get('POST.description'),
					'parentIds' => $parentIds,
					'categoryId' => $f3->get('POST.categoryId')
				)
			);

		if ($f3->get('POST.nextAction'))
			$f3->reroute($f3->get('POST.nextAction'));
		else if (!$f3->get('AJAX'))
			$f3->reroute('/main/index');
	}

	public function save_list($f3) {
		$actionModel = new \models\Action();

		if ($f3->get('POST.createParent')) {
			/* Parent creation requested */
			$projectId = $actionModel->createItem(array(
					'type' => 'p',
					'title' => $f3->get('POST.newProjectTitle'),
					'description' => $f3->get('POST.newProjectDescription'),
					'outcome' => $f3->get('POST.newProjectOutcome'),
					'parentIds' => $f3->get('POST.parentIds'),
					'categoryId' => $f3->get('POST.newProjectCategoryId'),
					'deadline' => $f3->get('POST.newProjectDeadline'),
					'isSomeday' => null !== $f3->get('POST.isProjectSomeday') ? $f3->get('POST.isProjectSomeday') : null
				));
			$parentIds[] = $projectId;
		} else {
			/* Not creating a new parent, use the ones provided */
			$parentIds = null !== $f3->get('POST.parentIds') ? $f3->get('POST.parentIds') : array();
		}

		/* Update item with the new data */
		$actionModel->updateItem(
				$f3->get('POST.itemId'),
				array(
					'type' => $f3->get('POST.listType'),
					'title' => $f3->get('POST.title'),
					'description' => $f3->get('POST.description'),
					'parentIds' => $parentIds
				)
			);

		/* Create children */
		foreach ($f3->get('POST.listItems') as $listItem) {
			$actionModel->createItem(array(
					'type' => 'T',
					'title' => $listItem,
					'parentIds' => array($f3->get('POST.itemId'))
				));
		}

		/* Move on to next requested action, or to main page */
		if ($f3->get('POST.nextAction'))
			$f3->reroute($f3->get('POST.nextAction'));
		else if (!$f3->get('AJAX'))
			$f3->reroute('/main/index');
	}

	public function save_list_item($f3) {
		$actionModel = new \models\Action();

		/* Update item with the new data */
		$actionModel->updateItem(
				$f3->get('POST.itemId'),
				array(
					'type' => 'T',
					'title' => $f3->get('POST.title'),
					'description' => $f3->get('POST.description'),
					'parentIds' => $f3->get('POST.parentIds')
				)
			);

		/* Move on to next requested action, or to main page */
		if ($f3->get('POST.nextAction'))
			$f3->reroute($f3->get('POST.nextAction'));
		else if (!$f3->get('AJAX'))
			$f3->reroute('/main/index');
	}

	public function mark_completed($f3) {
		$itemId = $f3->get('POST.itemId');

		$actionModel = new \models\Action();
		$actionModel->markItemCompleted($itemId);

		die;
	}

}