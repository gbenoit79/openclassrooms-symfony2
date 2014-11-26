<?php
namespace OC\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AdvertController extends Controller
{
    public function indexAction($page)
    {
        if ($page < 1) {
            throw $this->createNotFoundException("La page " . $page . " n'existe pas.");
        }

        // Ici je fixe le nombre d'annonces par page à 3
        // Mais bien sûr il faudrait utiliser un paramètre, et y accéder via $this->container->getParameter('nb_per_page')
        $nbPerPage = 3;

        // On récupère notre objet Paginator
        $listAdverts = $this->getDoctrine()
                            ->getManager()
                            ->getRepository('OCPlatformBundle:Advert')
                            ->getAdverts($page, $nbPerPage)
        ;

        // On calcule le nombre total de pages grâce au count($listAdverts) qui retourne le nombre total d'annonces
        $nbPages = ceil(count($listAdverts) / $nbPerPage);

        // Si la page n'existe pas, on retourne une 404
        if ($page > $nbPages) {
            throw $this->createNotFoundException("La page " . $page . " n'existe pas.");
        }

        // On donne toutes les informations nécessaires à la vue
        return $this->render('OCPlatformBundle:Advert:index.html.twig', array(
            'listAdverts' => $listAdverts,
            'nbPages' => $nbPages,
            'page' => $page,
        ));
    }

    public function viewAction($id)
    {
        // On récupère l'EntityManager
        $em = $this->getDoctrine()->getManager();

        // Pour récupérer une annonce unique : on utilise find()
        $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);

        // On vérifie que l'annonce avec cet id existe bien
        if ($advert === null) {
            throw $this->createNotFoundException("L'annonce d'id " . $id . " n'existe pas.");
        }

        // On récupère la liste des advertSkill pour l'annonce $advert
        $listAdvertSkills = $em->getRepository('OCPlatformBundle:AdvertSkill')->findByAdvert($advert);

        // Puis modifiez la ligne du render comme ceci, pour prendre en compte les variables :
        return $this->render('OCPlatformBundle:Advert:view.html.twig', array(
            'advert' => $advert,
            'listAdvertSkills' => $listAdvertSkills,
        ));
    }

    public function addAction(Request $request)
    {
        // La gestion d'un formulaire est particulière, mais l'idée est la suivante :

        if ($request->isMethod('POST')) {
            // Ici, on s'occupera de la création et de la gestion du formulaire

            $request->getSession()->getFlashBag()->add('info', 'Annonce bien enregistrée.');

            // Puis on redirige vers la page de visualisation de cet article
            return $this->redirect($this->generateUrl('oc_platform_view', array('id' => 1)));
        }

        // Si on n'est pas en POST, alors on affiche le formulaire
        return $this->render('OCPlatformBundle:Advert:add.html.twig');
    }

    public function editAction($id)
    {
        // On récupère l'EntityManager
        $em = $this->getDoctrine()->getManager();

        // On récupère l'entité correspondant à l'id $id
        $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);

        // Si l'annonce n'existe pas, on affiche une erreur 404
        if ($advert == null) {
            throw $this->createNotFoundException("L'annonce d'id " . $id . " n'existe pas.");
        }

        // Ici, on s'occupera de la création et de la gestion du formulaire

        return $this->render('OCPlatformBundle:Advert:edit.html.twig', array(
            'advert' => $advert,
        ));
    }

    public function deleteAction($id, Request $request)
    {
        // On récupère l'EntityManager
        $em = $this->getDoctrine()->getManager();

        // On récupère l'entité correspondant à l'id $id
        $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);

        // Si l'annonce n'existe pas, on affiche une erreur 404
        if ($advert == null) {
            throw $this->createNotFoundException("L'annonce d'id " . $id . " n'existe pas.");
        }

        if ($request->isMethod('POST')) {
            // Si la requête est en POST, on deletea l'article

            $request->getSession()->getFlashBag()->add('info', 'Annonce bien supprimée.');

            // Puis on redirige vers l'accueil
            return $this->redirect($this->generateUrl('oc_platform_home'));
        }

        // Si la requête est en GET, on affiche une page de confirmation avant de delete
        return $this->render('OCPlatformBundle:Advert:delete.html.twig', array(
            'advert' => $advert,
        ));
    }

    public function menuAction($limit = 3)
    {
        $listAdverts = $this->getDoctrine()
                            ->getManager()
                            ->getRepository('OCPlatformBundle:Advert')
                            ->findBy(
            array(), // Pas de critère
            array('date' => 'desc'), // On trie par date décroissante
            $limit, // On sélectionne $limit annonces
            0// À partir du premier
        );

        return $this->render('OCPlatformBundle:Advert:menu.html.twig', array(
            'listAdverts' => $listAdverts,
        ));
    }
}
