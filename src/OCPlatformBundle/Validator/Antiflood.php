<?php

namespace OCPlatformBundle\Validator;

use Symfony\Component\Validator\Constraint;


/**
* @Annotation
*/
class Antiflood extends Constraint{
	
	public $message= "vous avez déjà posté un message il ya 15 secondes, merci d'attendre un peu";


	public function validatedBy()
	{
		return 'oc_platform_antiflood';
	}

}