<?php

/**
 * config
 */
namespace MTsung{

	abstract class config{
		const SMTP = [
			'SMTPSecure' => "ssl",
			'Host' => "smtp.gmail.com",
			'Port' => "465",
			'Username' => "",
			'Password' => "",
			'senderEmail' => "",
			'senderName' => "",
		];
	}

}
