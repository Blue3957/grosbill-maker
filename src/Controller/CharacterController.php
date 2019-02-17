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
use App\Repository\CharacterRepository;

class CharacterController extends AbstractController
{
    /**
     * @Route("/character", name="character_list")
     * @Route("/character/by_{field}", name="character_list_sorted")
     */
    public function list(CharacterRepository $repo, $field = null)
    {
        $characterList = $repo->findAll($field);

        dump($characterList);
        return $this->render('character/list.html.twig', [
            'characterList' => $characterList
        ]);
    }

    /**
     * @Route("/character/{id<\d+>}", name="character_detail")
     */
    public function detail(Character $character)
    {
        if(is_null($character))
        {
            return $this->redirectToRoute("character_list");
        }

        return $this->render('character/detail.html.twig', [
            'character' => $character
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

        dump($form->createView());
    	return $this->render('character/edit.html.twig', [
    		"formCharacter" => $form->createView(),
    		"isEditMode" => !is_null($character->getId())
    	]);

    }
}
