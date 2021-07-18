<?php

namespace OCPlatformBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManagerInteface;


class AntifloodValidator extends ConstraintValidator{

	private $requestStack;
	private $em;

    public function __construct(RequestStack $requestStack, EntityManagerInteface $em ){
    		$this->requestStack = $requestStack;
    		$this->em = $em;
    }
	
	public function validate($value, Constraint $constraint )
	{

		$ip = $this->requestStack->getCurrentRequest()->getClientIp();

		$isFlood = $this->em->getRepository('OCPlatformBundle:Application')->isFlood($ip,15);

		if( $isFlood ) {
			$this->context->addViolation($constraint->message);
		}
	}

}