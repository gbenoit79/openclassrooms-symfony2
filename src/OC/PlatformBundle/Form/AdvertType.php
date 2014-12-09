<?php
namespace OC\PlatformBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AdvertType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('date',      'date')
      ->add('title',     'text')
      ->add('author',    'text')
      ->add('content',   'ckeditor')
      ->add('image',      new ImageType())
      ->add('categories', 'entity', array(
        'class'    => 'OCPlatformBundle:Category',
        'property' => 'name',
        'multiple' => true,
        'expanded' => false
      ))
      ->add('save',      'submit')
    ;

    // On ajoute une fonction qui va écouter l'évènement PRE_SET_DATA
    $builder->addEventListener(
      FormEvents::PRE_SET_DATA,
      function(FormEvent $event) {
        // On récupère notre objet Advert sous-jacent
        $advert = $event->getData();

        if (null === $advert) {
          return;
        }

        if (!$advert->getPublished() || null === $advert->getId()) {
          $event->getForm()->add('published', 'checkbox', array('required' => false));
        } else {
          $event->getForm()->remove('published');
        }
      }
    );
  }

  /**
   * @param OptionsResolverInterface $resolver
   */
  public function setDefaultOptions(OptionsResolverInterface $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => 'OC\PlatformBundle\Entity\Advert'
    ));
  }

  /**
   * @return string
   */
  public function getName()
  {
    return 'oc_platformbundle_advert';
  }
}