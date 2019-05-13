<?php

namespace core;

use core\Model;
use libs\jwt\JWT;

abstract class Controller {
	
	public $route;
	public $model;
	private $renewJwt = null;
	
	public function __construct($param) {
		$this->route = $param;
		$this->model = $this->loadModel();
	}
	
	protected function loadModel() {
		$path = 'models\\'. ucfirst($this->route[2]);
		if (class_exists($path)) {
			return new $path;
		}
	}
	
	private function sanitizeString(&$item) {
		if (isset($item)) {
			$item = html_entity_decode($item, ENT_QUOTES);
			$item = filter_var($item, FILTER_SANITIZE_STRING);
			$item = html_entity_decode($item, ENT_QUOTES);
		}
	}
	
	public function getJSON() {
		if (isset($_GET['json'])) {
			$array = $_GET['json'];
			$json = json_decode($array, true);
		 	array_walk_recursive($json, array($this, 'sanitizeString'));
			return $json;
		} 
		return false;
	}
	
	public function sendJSON($array) {
		header('Content-type: application/json; charset=utf-8');
		//refresh jwt
		if ($this->renewJwt != null) {
			$data = array(
				"id" => $this->renewJwt->data->id,
				"name" => $this->renewJwt->data->name,
				"email" => $this->renewJwt->data->email
			);
			$jwt = $this->genJwt($data);
			header('Authorization: ' . $jwt);
			$this->renewJwt = null;
		}
		$json = json_encode($array);
		echo $json;
	}
	
	public function error($code, $message) {
		http_response_code($code);
		$this->sendJSON($message);
	}
	
	public function validateJWT() {
		$headers = getallheaders();
		$jwt = isset($headers["Authorization"]) ? $headers["Authorization"] : "";
		if ($jwt) {
			include_once 'config/jwt.php';
			try {
				$decoded = JWT::decode($jwt, $key, array('HS256'));
				if (($decoded->exp - time()) <= 300) $this->renewJwt = $decoded;
				return $decoded;
			} catch (\Exception $e) {
				$this->error(401, array(
					"message" => "Access denied.",
					"error" => $e->getMessage()
				));
				return false;
			}
		} else {
			$this->error(401, array(
					"message" => "Access denied. JWT is not provided."
				));
			return false;
		}
	}
	
	public function genJwt($data) {
		$user = $data;
		include 'config/jwt.php';
		$token = array(
			"exp" => $exp,
			"data" => array(
				"id" => $user['id'],
				"name" => $user['name'],
				"email" => $user['email']
			)
		);
		// generate jwt
   		$jwt = JWT::encode($token, $key);
		return $jwt;
	}
	
}
?>