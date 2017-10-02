<?php

class CollibraController extends AppController {
	public $uses = ['CollibraAPI'];

	public function jobChange5($queryString) {
		$this->autoRender = false;

		$formResp = $this->CollibraAPI->post(
			'workflow/c69524a5-a62e-4626-ae31-78dae1259673/start',
			'taskInformation='.$queryString
		);
	}

	public function jobChangeProd($queryString) {
		$this->autoRender = false;

		$formResp = $this->CollibraAPI->post(
			'workflow/94d2486a-90ba-4edf-9cb7-789e4d2bb064/start',
			'taskInformation='.$queryString
		);
	}
}
