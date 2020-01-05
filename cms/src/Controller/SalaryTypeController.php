<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\SalaryType;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SalaryTypeController extends AbstractController
{
    /**
     * @Route("/salary_type", name="salaryType")
     */
    public function index()
    {
        $username = $this->getUser()->getNickname();
        $salaryTypeRepo = $this->getDoctrine()->getRepository(SalaryType::class);
    $bonuses = $salaryTypeRepo->findAll();

        return $this->render('salary_type/index.html.twig', [
            'username' => $username, "bonuses" => $bonuses
        ]);
    }


    /**
     * @Route("/edit_salary_type", name="editSalaryType")
     */
    public function edit()
    {
        $username = $this->getUser()->getNickname();
        $salaryTypeRepo = $this->getDoctrine()->getRepository(SalaryType::class);
        $bonuses = $salaryTypeRepo->findAll();

        $errors = null;
        $updateSalaryTypeForm = $this->createFormBuilder()
            ->add('saturday', NumberType::class, [
                //'html5'=> true,
                'required' => true,
                'attr' => ['value' => $bonuses[0]->getBonusRate()]
            ])
            ->add('sunday', NumberType::class, [
                //'html5'=> true,
                'required' => true,
                'attr' => ['value' => $bonuses[1]->getBonusRate()]
            ])
            ->add('weekday', NumberType::class, [
                //'html5'=> true,
                'required' => true,
                'attr' => ['value' => $bonuses[2]->getBonusRate()]
            ])
            ->add('save', SubmitType::class)
            ->setAction($this->generateUrl('updateSalaryType'))
            ->setMethod('POST')
            ->getForm();

        return $this->render('salary_type/edit_salary_type.html.twig', [
            'username' => $username, 'form' => $updateSalaryTypeForm->createView(), "errors" =>$errors
        ]);
    }


    /**
     * @Route("/update_salary_type", name="updateSalaryType")
     * @param Request $request
     * @return RedirectResponse
     */
    public function update(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $salaryTypeRepo = $this->getDoctrine()->getRepository(SalaryType::class);
        $bonuses = $salaryTypeRepo->findAll();


        if ($request->isMethod('POST')) {

            $postData = $request->request->get("form");

            $bonuses[0]->setBonusRate($postData['saturday']);
            $em->persist($bonuses[0]);
            $em->flush();

            $bonuses[1]->setBonusRate($postData['sunday']);
            $em->persist($bonuses[1]);
            $em->flush();

            $bonuses[2]->setBonusRate($postData['weekday']);
            $em->persist($bonuses[2]);
            $em->flush();

            return $this->redirectToRoute("salaryType");
        }
        return $this->redirectToRoute("salaryType");
    }
}
