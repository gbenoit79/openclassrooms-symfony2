<?php

namespace OC\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('OCCoreBundle:Default:index.html.twig');
    }

    public function contactAction(Request $request)
    {
        // Ajouter un message flash du type "La page de contact n'est pas encore disponible"
        $request->getSession()->getFlashBag()->add('info', 'La page de contact n\'est pas encore disponible');

	    // Rediriger vers la page d'accueil
	    return $this->redirect($this->generateUrl('oc_core_homepage'));
    }
}
