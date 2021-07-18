<?php

namespace OCPlatformBundle\Antispam;

/**
* 
*/
class OCAntispam
{
	private $mailer;
	private $locale;
	private $minLength;
	
    public function __construct(\Swift_Mailer $mailer, $locale, $minLength){

    	$this->mailer = $mailer; 
    	$this->locale = $locale;  
    	$this->minLength = $minLength;
    }

	public function isSpam($text)
	{
		return strlen($text) < $this->minLength;
	}
}



  ?>