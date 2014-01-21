<?php

namespace controllers;

class Main
{
	public function index($f3)
	{
		$actionModel = new \models\Action();

		$actions = $actionModel->getNextActionsByContext();
		$waitingFors = $actionModel->getNextWaitingFors();

		$f3->set('showContextMenu', true);
		$f3->set('nextActions', $actions);
		$f3->set('waitingFors', $waitingFors);
		$f3->set('inboxItems',  $actionModel->getInboxItemCount());

		echo \View::instance()->render('main/index.php');
	}

	public function create_parent($f3) {
		$actionModel = new \models\Action();

		\F3::set('categories', $actionModel->getCategories());

		echo \View::instance()->render('main/create_parent.php');
	}

	public function get_inbox_item_count($f3) {
		$actionModel = new \models\Action();
		echo $actionModel->getInboxItemCount();
	}

	public function process_inbox($f3) {
		$actionModel = new \models\Action();
		
		$f3->set('inboxItem', $actionModel->getNextInboxItem());
		$f3->set('inboxItems', $actionModel->getInboxItemCount());

		echo \View::instance()->render('main/process_inbox.php');
	}

	public function process_inbox_decision($f3) {
		$actionModel = new \models\Action();

		$itemId = $f3->get('POST.itemId');

		switch($f3->get('POST.decision')) {
			case 'delete':
				$actionModel->deleteItem($itemId);
				$f3->reroute('/main/process_inbox');
				break;
			case 'complete':
				$actionModel->markItemCompleted($itemId);
				$f3->reroute('/main/process_inbox');
				break;
		}

		$inboxItem = $actionModel->getInboxItem($itemId);

		$vars = [
				'flow' => 'inboxItemProcess',
				'flowStep' => 1,
				'item' => $inboxItem,
				'categories' => $actionModel->getCategories(),
				'inboxItems'  => $actionModel->getInboxItemCount()
			];

		/* Make next actions available */
		$flowController = new \helpers\FlowChainController($f3);
		$vars['nextActions'] = $flowController->getNextActions('inboxItemProcess', 0);

		$f3->mset($vars);
		
		switch($f3->get('POST.decision')) {
			case 'reference':
				echo \View::instance()->render('main/edit_reference.php');
				break;
			case 'list':
				$f3->set('listType', 'L');
				echo \View::instance()->render('main/edit_list.php');
				break;
			case 'checklist':
				$f3->set('listType', 'C');
				echo \View::instance()->render('main/edit_list.php');
				break;
			case 'listItem':
				echo \View::instance()->render('main/edit_list_item.php');
				break;
			case 'action':
				$f3->set('spaceContexts', $actionModel->getSpaceContexts());
				$f3->set('timeContexts', $actionModel->getTimeContexts());
				echo \View::instance()->render('items/edit_action.php');
				break;
			case 'waitingFor':
				echo \View::instance()->render('items/edit_waiting_for.php');
				break;
			case 'project':
				$f3->set('flow', 'inboxItemProjectProcess');
				$f3->set('nextActions', $flowController->getNextActions('inboxItemProjectProcess', 0));
				echo \View::instance()->render('items/edit_project.php');
				break;
			default:
				$f3->reroute('/main/process_inbox');
		}
	}

	public function find_parents($f3) {
		$actionModel = new \models\Action();

		$parents = $actionModel->findParents($f3->get('GET.term'));
		
		$view = new \helpers\View();
		echo $view->renderJson($parents);
	}

	public function find_parent_lists($f3) {
		$actionModel = new \models\Action();

		$parents = $actionModel->findParents($f3->get('GET.term'), array('L', 'C'));

		$view = new \helpers\View();
		echo $view->renderJson($parents);
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
					'isSomeday' => nul !== $f3->get('POST.isProjectSomeday') ? $f3->get('POST.isProjectSomeday') : null
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

}
