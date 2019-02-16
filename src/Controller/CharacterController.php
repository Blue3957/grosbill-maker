<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Character;
use App\Entity\Race;
use App\Entity\Job;
use App\Entity\Alignment;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class CharacterController extends AbstractController
{
    /**
     * @Route("/character", name="character_list")
     */
    public function list()
    {
        return $this->render('character/index.html.twig', [
            'controller_name' => 'CharacterController',
        ]);
    }

    /**
     * @Route("/character/new", name="character_create")
     * @Route("/character/{id<\d+>}/edit", name="character_edit")
     */
    public function edit(ObjectManager $manager, Request $req, Character $character = null)
    {
    	if(is_null($character))
    	{
    		$character = new Character();
    	}

    	$form = $this->createFormBuilder($character)
    				 ->add("name")
    				 ->add("race", EntityType::class, [
    				 	'class' => Race::class,
    				 	'choice_label' => 'name'
    				 	])
    				 ->add("level")
    				 ->add("job", EntityType::class, [
    				 	'class' => Job::class,
    				 	'choice_label' => 'name'
    				 	])
    				 ->add("alignment", EntityType::class, [
    				 	'class' => Alignment::class,
    				 	'choice_label' => 'name'
    				 	])
    				 ->add("description")
    				 ->add("bio")
    				 ->getForm();

    	$form->handleRequest($req);

    	if($form->isSubmitted())
    	{
    		$manager->persist($character);
    		$manager->flush();

    		return $this->redirectToRoute("character_list");
    	}

    	return $this->render('character/edit.html.twig', [
    		"formCharacter" => $form->createView(),
    		"isEditMode" => !is_null($character->getId())
    	]);

    }
}
