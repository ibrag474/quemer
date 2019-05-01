<?php

namespace libs\Mailer;

class Mailer {
	
	public function sendActLink($email, $activationKey) {
		$param = '{"hash":"' . $activationKey . '"}';
		$template = file_get_contents('libs/mailer/actmail.html');
		$link = "https://quemer.com/account/activation/?p=".$param;
		$template = str_replace('{{ link }}', $link, $template);
		mail($email, 'Quemer account activation', $template, "From: noreply@quemer.com\n" . 'MIME-Version: 1.0\n' . 'Content-type: text/html');
	}
	
	public function sendResetPswdLink($email, $activationKey) {
		$param = $activationKey;
		$template = file_get_contents('libs/mailer/resetPswdmail.html');
		$link = $param;
		$template = str_replace('{{ link }}', $link, $template);
		mail($email, 'Quemer account activation', $template, "From: noreply@quemer.com\n" . 'MIME-Version: 1.0\n' . 'Content-type: text/html');
	}
	
}
?>