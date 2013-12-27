<?php
$flows = [
	'inboxItemProcess' => [
		/* Step 0, create the item */
		[
			[
				'label' => 'CreateAndProcessInboxButton',
				'action' => '/main/process_inbox',
				'default' => true
			],[
				'label' => 'CreateButton'
			]
		]
	],
	
	'inboxItemProjectProcess' => [
		/* Step 0, create the project */
		[
			[
				'label' => 'CreateAndNextActionButton',
				'action' => '/items/create_action',
				'default' => true
			],[
				'label' => 'CreateAndWaitingForButton',
				'action' => '/items/create_waiting_for'
			],[
				'label' => 'CreateAndProcessInboxButton',
				'action' => '/main/process_inbox'
			],[
				'label' => 'CreateButton'
			]
		],
		/* Step 1, create the project action or waiting for */
		[
			[
				'label' => 'CreateAndProcessInboxButton',
				'action' => '/main/process_inbox',
				'default' => true
			],[
				'label' => 'CreateButton'
			]
		]
	],
	
	'projectCreate' => [
		/* Step 0, create the project */
		[
			[
				'label' => 'CreateAndNextActionButton',
				'action' => '/items/create_action',
				'default' => true
			],[
				'label' => 'CreateAndWaitingForButton',
				'action' => '/items/create_waiting_for'
			],[
				'label' => 'CreateButton'
			]
		],
		/* Step 1, crete the project action or waiting for */
		[
			[
				'label' => 'CreateButton',
				'default' => true
			]
		]
	]
];