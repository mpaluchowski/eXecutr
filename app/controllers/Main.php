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
				echo \View::instance()->render('items/edit_reference.php');
				break;
			case 'list':
				$f3->set('listType', 'L');
				echo \View::instance()->render('items/edit_list.php');
				break;
			case 'checklist':
				$f3->set('listType', 'C');
				echo \View::instance()->render('items/edit_list.php');
				break;
			case 'listItem':
				echo \View::instance()->render('items/edit_list_item.php');
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

	public function weekly_review($f3) {
		$actionModel = new \models\Action();

		$f3->mset([
			'inboxItems' => $actionModel->getInboxItemCount(),
			'actionsPastDue' => $actionModel->getItemsPastDue('a'),
			'waitingForsPastDue' => $actionModel->getItemsPastDue('w'),
			'projectsWithoutOutcomesCount' => $actionModel->getProjectsWithoutOutcomesCount(),
			'projectsMissingNextActions' => $actionModel->getProjectsMissingNextActionsCount()
			]);

		echo \View::instance()->render('main/weekly_review.php');
	}

}
