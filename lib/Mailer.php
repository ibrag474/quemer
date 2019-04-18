<?php

namespace lib;

class Mailer {
	
	public function sendActLink($email, $activationKey) {
		$param = '{"hash":"' . $activationKey . '"}';
		$template = file_get_contents('app/views/design/templates/actmail.html');
		$link = "https://quemer.com/account/activate/?param=".$param;
		$template = str_replace('{{ link }}', $link, $template);
		mail($email, 'Quemer account activation', $template, "From: noreply@quemer.com\n" . 'MIME-Version: 1.0\n' . 'Content-type: text/html');
	}
	
	public function sendResetPswdLink($email, $activationKey) {
		$param = $activationKey;
		$template = file_get_contents('app/views/design/templates/resetPswdmail.html');
		$link = $param;
		$template = str_replace('{{ link }}', $link, $template);
		mail($email, 'Quemer account activation', $template, "From: noreply@quemer.com\n" . 'MIME-Version: 1.0\n' . 'Content-type: text/html');
	}
	
}
?>