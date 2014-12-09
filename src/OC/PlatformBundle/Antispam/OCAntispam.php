<?php
namespace OC\PlatformBundle\Antispam;

class OCAntispam extends \Twig_Extension
{
    private $mailer;
    private $locale;
    private $nbForSpam;

    public function __construct(\Swift_Mailer $mailer, $nbForSpam)
    {
        $this->mailer = $mailer;
        $this->nbForSpam = (int) $nbForSpam;
    }

    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * Vérifie si le texte est un spam ou non
     *
     * @param string $text
     * @return bool
     */
    public function isSpam($text)
    {
        return strlen($text) < $this->nbForSpam;
    }

    // Twig va exécuter cette méthode pour savoir quelle(s) fonction(s) ajoute notre service
    public function getFunctions()
    {
        return array(
            'checkIfSpam' => new \Twig_Function_Method($this, 'isSpam'),
        );
    }

    // La méthode getName() identifie votre extension Twig, elle est obligatoire
    public function getName()
    {
        return 'OCAntispam';
    }
}
