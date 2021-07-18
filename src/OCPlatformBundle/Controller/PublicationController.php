<?php 

namespace OCPlatformBundle\Controller;

use OCPlatformBundle\Entity\Advert;
use OCPlatformBundle\Entity\AdvertSkill;
use OCPlatformBundle\Entity\Image;
use OCPlatformBundle\Entity\Application;
use OCPlatformBundle\Form\AdvertType;
use OCPlatformBundle\Form\AdvertEditType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
* 
*/
class PublicationController extends Controller 
{

	
	public function indexAction($page){



		if ($page<1) {
			throw new NotFoundHttpException('page "'.$page.'" inexistante');
		}
		
		$nbPerPage = 3;
		
		$listAdverts  = $this->getDoctrine()->getRepository('OCPlatformBundle:Advert')
							 ->getAdverts($page,$nbPerPage);

		$nbPages = ceil(count($listAdverts)/ $nbPerPage );

		
		return $this->render('OCPlatformBundle:Advert:index.html.twig', 
		 array(
		 		
		 		'listAdverts' => $listAdverts,
		 		'nbPages'     => $nbPages,
		 		'page'        => $page 
		 	)
		);

	}

	public function viewAction($id/*, Request $request*/){


		$em = $this->getDoctrine()->getManager();

		$repository = $em->getRepository('OCPlatformBundle:Advert');
		$advert = $repository->find($id);

		$listApplications = $em->getRepository('OCPlatformBundle:Application')->findBy(array('advert' => $advert ));

		if (null === $advert) {
			

				throw new NotFoundHttpException("l'annonce d'id ".$id." n'existe pas");
			
		}

		$listAdvertSkills = $em->getRepository('OCPlatformBundle:AdvertSkill')->findByAdvert($advert);



		return $this->render( 'OCPlatformBundle:Advert:view.html.twig', array( 'advert' => $advert,
		                                                                       'listApplications' => $listApplications, 
		                                                                        'listAdvertSkills' => $listAdvertSkills  ) );
		
	}

	
	public function viewSlugAction($slug, $year, $format){
		 return new Response("Affichage du slug : ".$slug." crée en ".$year." et au format ".$format);
	}

	public function addAction(Request $request){


		if ( !$this->get('security.context')->isGranted('ROLE_AUTEUR')) {
			throw new AccessDeniedException('Accès limité aux auteurs');
		}

		$advert = new Advert();

		$form = $this->get('form.factory')->create(new AdvertType(), $advert);
		

		if ($form->handleRequest($request)->isValid()) {

			$em = $this->getDoctrine()->getManager();
			$em->persist($advert);
			$em->flush();

			$request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée');
			return $this->redirect($this->generateUrl( 'oc_platform_view', array('id' => $advert->getId() ) ) );
		}

		return $this->render('OCPlatformBundle:Advert:add.html.twig' , array('form' => $form->createView()) );
		
		
	}

	public function editAction($id, Request $request){

        $em = $this->getDoctrine()->getManager();
        $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);

        if (null === $advert) {
        	throw  $this->createNotFoundException("l'annonce d'id ".$id." n'existe pas");
        }

	
		$form = $this->get('form.factory')->create(new AdvertEditType(), $advert);
		

		if ($form->handleRequest($request)->isValid()) {

			$em->flush();

			$request->getSession()->getFlashBag()->add('notice', 'Annonce bien modifiée');
			return $this->redirect($this->generateUrl( 'oc_platform_view', array('id' => $advert->getId() ) ) );
		}

		return $this->render('OCPlatformBundle:Advert:edit.html.twig' , array('form' => $form->createView(),
																			  'advert' => $advert 
																			) );
	}

	public function deleteAction($id, Request $request){

		$em = $this->getDoctrine()->getManager();

		$advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);

		if (null === $advert) {
			throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas ");	
		}

		$form = $this->createFormBuilder()->getForm();
		if ($form->handleRequest($request)->isValid() ) {

			$em->remove($advert);

			$em->flush();

			$request->getSession()->getFlashBag()->add('info', 'Annonce bien supprimée');

		
			return $this->redirect($this->generateUrl('oc_platform_home'));
		}		

		return $this->render('OCPlatformBundle:Advert:delete.html.twig' , array( 
			                                                             'advert' => $advert,
			                                                              'form'  => $form->createView() ));
		
	}

	public function menuAction($limit = 3)
  {

    $listAdverts = $this->getDoctrine()->getManager()->getRepository('OCPlatformBundle:Advert')->
    					findBy(
    							array(),
    							array('date' => 'DESC' ),
    							$limit,
    							0
    					);

    return $this->render('OCPlatformBundle:Advert:menu.html.twig', array(
 
      'listAdverts' => $listAdverts
    ));
  }

  public function shareAction($id)
  {
    $em = $this->getDoctrine()->getManager();

    $publication = $em->getRepository('PlatformBundle:Publication')->find($id);

    if ($publication === null) {
      throw $this->createNotFoundException("La publication d'id ".$id." n'existe pas.");
    }

    if ($publication->getOrigin() != null) {
      $publication =  $publication->getOrigin();
    }

    $listPublications = $em->getRepository('PlatformBundle:Publication')->findBy(array(
                                                                    'author' => $this->getUser(),
                                                                    'origin' => $publication ) ); //
    $request = $this->container->get('request');
    
    if($listPublications){

      $request->getSession()->getFlashBag()->add('notice', 'Vous avez déja partagé cette publication');
      return $this->redirectToRoute('publication_view', array('id' => $publication->getId() ));
    }
	$sharing = new Publication();
    $image = new Image(); 
    try {
      $sharing->setOrigin($publication);
      $sharing->setTitle( $this->getUser()->getFirstname() . "a partagé une publication de "
                                  . $publication->getAuthor()->getFirstname()  );
      $sharing->setAuthor($this->getUser());
      $sharing->setContent($publication->getContent());

      $image->setUrl( $publication->getImage()->getUrl() );
      $image->setAlt( $publication->getImage()->getAlt() );
      $sharing->setImage($image);
      $sharing->setLikes( $publication->getLikes() );

      $em->persist($sharing);
      $em->flush();

      $request->getSession()->getFlashBag()->add('notice', 'Votre partage est enregistré');
      return $this->redirectToRoute('publication_view', array('id' => $sharing->getId() ));

    } catch (Exception $e) {
       throw $this->createNotFoundException("La publication a été supprimée");
    }
  }

public function wallAction($page)
  {
    if ($page < 1) {
      throw new NotFoundHttpException('Page "'.$page.'" inexistante.');
    }

    $nbPerPage = 5;
    $em = $this->getDoctrine()->getManager();
    $user = $this->getUser();

    $listFriends1 = $user->getMyFriends()->toArray();
    $listFriends2 = $user->getFriendsWithMe()->toArray();
    $listFriends =  array_merge($listFriends1, $listFriends2);

    $listPublications = $em->getRepository('PlatformBundle:Publication')->getFriendsPubli($page, $nbPerPage ,$listFriends);
    $nbPages = ceil(count($listPublications)/$nbPerPage);
    
    return $this->render('PlatformBundle:Publication:wall.html.twig', array( 'listPublications' =>  $listPublications,
                                                                              'nbPages' => $nbPages,
                                                                              'page' => $page ) );
  }

  public function commentAction($id, Request $request)
  {
    if (!$request->isXmlHttpRequest()) {
        return new JsonResponse(array('message' => 'accessible uniquement avec Ajax!'), 400);
    }

    $em = $this->getDoctrine()->getManager();

    $publication = $em->getRepository('PlatformBundle:Publication')->find($id);

    if ($publication === null) {
    
      return new JsonResponse(array('message' => "La publication d'id ".$id." n'existe pas."), 400);
    }
    
    $comment = new UserComments();
    $form = $this->createCreateForm($comment,$id);

    if ($form->handleRequest($request)->isValid()) {

      $user = $this->getUser();
      $comment->setUser($user);
      $comment->setPublication($publication);
      $comment->setDate( new \DateTime() );
      
      $em->persist($comment);
      $em->flush();
      
      //return new JsonResponse(array('message' => 'Success' ), 200);
      
      return new JsonResponse(array('image'    => $user->getImage()->getUrl(),
                                    'firstname'=> $user->getFirstname(),    
                                    'lastname' => $user->getLastname(),
                                    'userlink' => $this->generateUrl('user_view', array('id' => $user->getId() )),
                                    'comment'  => $comment->getTextcomment(),
                                    'date'     => $comment->getdate()->format('d-m-Y H:i:s') ), 200);
    }

    $response = new JsonResponse(
                        array(
                              'message' => 'Error',
                              'form' => $this->renderView('PlatformBundle:Publication:comment.html.twig',
                                array(
                                      'entity' => $entity,
                                      'form' => $form->createView(),
                      ))), 400);
 
    return $response;

  }

    
  }

 ?>
