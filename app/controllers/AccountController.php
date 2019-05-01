<?php

namespace app\controllers;

use core\Controller;
use lib\Mailer;

class AccountController extends Controller {
	
	private $obj;
	
	public function loginAction($params) {
		$this->view->layout = 'account';
		$this->view->render('Login | Quemer.com'/*, $vars*/);
	}
	
	public function registerAction($params) {
		$this->view->layout = 'account';
		$this->view->render('Register | Quemer.com');
	}
	
	public function restoreAction($params) {
		$this->view->layout = 'account';
		$this->view->render('Restore | Quemer.com');
	}
	
	public function activationAction($params) {
		$this->view->layout = 'account';
		$this->view->render('Account activation | Quemer.com');
	}
		
}

?>
