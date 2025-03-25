<?php

class Modelo {
	
	static function existeUsuario($username, $password) {
		
		$usuario = array();
	
		if (($username == "user") && ($password == "password")) {
		
			$usuario = array(
				"userID" => substr(bin2hex(openssl_random_pseudo_bytes(ceil(3))), 0, 6),
				"username" => $username,
				"password" => $password,
				"name" => "Carlos",
				"lastname" => "Ballester",
				"avatar" => "avatar.png"
			);
		}	
	
		return $usuario;
	}
	
	static function obtenerMensajes($userID) {
		
		$mensajes = array(
			array(
				"date" => "10-12-2022",
				"subject" => "Phasellus tempor lacinia arcu",
				"body" => "Donec eleifend tincidunt risus, id cursus nisi accumsan vel."
			),
			array(
				"date" => "03-01-2023",
				"subject" => "Proin at odio rutrum, scelerisque dolor at",
				"body" => "Maecenas congue, nulla sed posuere tristique, diam massa sagittis eros, pharetra porttitor dolor sapien et sem."
			),
			array(
				"date" => "11-01-2023",
				"subject" => "Aenean faucibus nec ex in commodo",
				"body" => "Nunc quis pellentesque nunc. In convallis egestas elit, et aliquet arcu placerat at."
			)
    	);

		
		return $mensajes;	
	}
}

class Controlador {
	
	function OnPeticion() {
	
		$peticion = json_decode($_POST['peticion'], true);
		
		$respuesta = array();
		
		if ($peticion['action'] == 'login') {
			
			if ($usuario = Modelo::existeUsuario($peticion["data"]["username"], $peticion["data"]["password"])) {
				
				$mensajes = Modelo::obtenerMensajes($usuario["userID"]);
				$respuesta = $this->getRespuestaOk($usuario, $mensajes);
			}
			else {
				
				$respuesta = $this->getRespuestaError(4, "Credenciales incorrectas");
			}
		}
		else {
			
			$respuesta = $this->getRespuestaError(1, "Acción incorrecta");
		}
		
		echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
	}
	
	function getRespuestaOk($usuario, $mensajes) {
	
		$respuesta = array(
			"success" => true,
			"data" => array(
				"userID" => $usuario["userID"], 
				"username" => $usuario["username"],
				"name" => $usuario["name"],
				"lastname" => $usuario["lastname"],
				"avatar" => base64_encode(file_get_contents($usuario["avatar"]))
			),
			"messages" => $mensajes
		);
		
		return $respuesta;
	}

	function getRespuestaError($code, $message) {
		
		$respuesta = array(
			"success" => false,
			"data" => array(
				"error" => $code,
				"message" => $message
			)
		);
		
		return $respuesta;
	}
	
	function OnError() {
		
		$respuesta = $this->getRespuestaError(0, "Error de acceso");
		
		echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
	}
}



class App {
	
	private $controlador = null;
	private $eventos = array();
	
	function __construct() {
		
		$this->eventos = array(
			'peticion' => 'OnPeticion',
		);
	}
	
	function despachar() {
		
		$manejador = 'OnError';
		
		foreach ($this->eventos as $event => $handler) {

			if (isset($_POST[$event])) {

				$manejador = $handler;
				break;
			}
		}
		
		$this->controlador->$manejador();
	}
	
	function init() {
		
		$this->controlador = new Controlador();
		$this->despachar();
	}
}

$app = new App();

$app->init();
?>