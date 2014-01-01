<?php

namespace controllers;

class Items
{
	public function create_inbox_item($f3) {
		echo \View::instance()->render('items/create_inbox_item.php');
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

}