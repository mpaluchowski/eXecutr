<?php
namespace helpers;

final class FlowChainController
{
	private $_flowConfig;

	public function __construct($f3) {
		include($f3->get('ROOT') . "/app/flows.php");
		$this->_flowConfig = $flows;
	}

	public function getNextActions($flow, $flowStep) {
		return $this->_flowConfig[$flow][$flowStep];
	}
}