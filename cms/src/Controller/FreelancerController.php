<?php

namespace App\Controller;

use App\Entity\FreelancerRate;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;



class FreelancerController extends AbstractController
{
    /**
     * @Route("/freelancers", name="freelancers", methods={"GET"})
     */
    public function index()
    {
        $rateRepo = $this->getDoctrine()->getRepository(FreelancerRate::class);
        //join two table by user id available in FreelancerRate table
        $queryRates = $rateRepo
            ->createQueryBuilder('p')
            ->innerJoin("p.user", 'c');

        $freelancers = $queryRates->getQuery()->getResult();
        //dd($freelancers[0] );
        $username = $this->getUser()->getNickname();
        return $this->render('freelancer/index.html.twig', [
            'username' => $username, "freelancers" => $freelancers
        ]);
    }

    /**
     * @Route("/edit_freelancer{freelancerId}", name="editFreelancer" , methods={"GET"})
     * @param $freelancerId
     * @return Response
     */
    public function edit($freelancerId)
    {
        $rateRepo = $this->getDoctrine()->getRepository(FreelancerRate::class);

        $userManager = $this->getDoctrine()->getRepository(User::class);
        $queryRates = $rateRepo
            ->createQueryBuilder('p')
            ->innerJoin("p.user", 'c')
            ->where('c.id = :userId')
            ->setParameter('userId', $freelancerId);

        $freelancer = $queryRates->getQuery()->getResult()[0];
        //dd($freelancer->getUser());

        $updateFreelancerForm = $this->createFormBuilder()
            ->add("freelancerId", HiddenType::class, ['attr' => ["value" => $freelancer->getUser()->getId()]])
            ->add("email", EmailType::class, ['disabled' => true, 'attr' => ["value" => $freelancer->getUser()->getEmail()]])
            ->add("password", PasswordType::class, ['disabled' => true, 'attr' => ["value" => $freelancer->getUser()->getPassword()]])
            ->add("nickname", TextType::class, ['attr' => ["value" => $freelancer->getUser()->getNickname()]])
            ->add("transportCost", NumberType::class, ['attr' => ["value" => $freelancer->getTransportCost()]])
            ->add("hourRate", NumberType::class, ['attr' => ["value" => $freelancer->getHourRate()]])
            ->add('save', SubmitType::class)
            ->setAction($this->generateUrl('updateFreelancer'))
            ->setMethod('POST')
            ->getForm();
        $username = $this->getUser()->getNickname();
        return $this->render("freelancer/create_freelancer.html.twig", ['username' => $username, 'form' => $updateFreelancerForm->createView(), 'user' => $freelancer->getUser()]);
    }


    /**
     * @Route("/create_freelancer", name="createFreelancer", methods={"GET"})
     */
    public function create()
    {

        $newFreelancerForm = $this->createFormBuilder()
            ->add("email", EmailType::class, ['attr' => ['autocomplete' => 'null']])
            ->add("password", PasswordType::class, ['attr' => ['autocomplete' => "new-password"]])
            ->add("nickname", TextType::class, ['attr' => ['autocomplete' => "new-password"]])
            ->add("transportCost", NumberType::class)
            ->add("hourRate", NumberType::class)
            ->add('save', SubmitType::class)
            ->setAction($this->generateUrl('saveFreelancer'))
            ->setMethod('POST')
            ->getForm();
        $username = $this->getUser()->getNickname();
        return $this->render("freelancer/create_freelancer.html.twig", ['username' => $username, 'form' => $newFreelancerForm->createView()]);
    }

    /**
     * @Route("/freelancer/save", name="saveFreelancer" , methods={"GET","POST"})
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return RedirectResponse|Response
     */
    public function save(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new User();
        $rate = new FreelancerRate();
        if ($request->isMethod("POST")) {
            $freelancerData = $request->request->get("form");
            //dd($freelancerData);
            $em = $this->getDoctrine()->getManager();

            // create new user
            $user->setEmail($freelancerData['email']);
            $user->setPassword($passwordEncoder->encodePassword($user, $freelancerData['password']));
            $user->setNickname($freelancerData['nickname']);
            $user->setRoles(['ROLE_FREELANCER']);
            $em->persist($user);
            $em->flush();

            $rate->setUser($user);
            $rate->setHourRate($freelancerData['hourRate']);
            $rate->setTransportCost($freelancerData['transportCost']);

            $em->persist($rate);
            $em->flush();

            return $this->redirectToRoute("freelancers");
        }
        return $this->redirectToRoute("freelancers");
    }

    /**
     * @Route("/freelancer/update", name="updateFreelancer" , methods={"GET", "POST"})
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return RedirectResponse|Response
     */
    public function update(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $userManager = $this->getDoctrine()->getRepository(User::class);
        $user = $userManager->find($request->request->get('form')['freelancerId']);

        $rateRepo = $this->getDoctrine()->getRepository(FreelancerRate::class);
        $rate = $rateRepo->findOneBy(['user' => $user]);

        //dd($rate);
        if ($request->isMethod("POST")) {
            $freelancerData = $request->request->get("form");
            $em = $this->getDoctrine()->getManager();

            //update freelancer(user)
            $user->setNickname($freelancerData['nickname']);

            $em->persist($user);
            $em->flush();


            $rate->setUser($user);
            $rate->setHourRate($freelancerData['hourRate']);
            $rate->setTransportCost($freelancerData['transportCost']);
            $em->persist($rate);
            $em->flush();
            return $this->redirectToRoute("freelancers");
        }

        return $this->redirectToRoute("freelancers");
    }


    /**
     * @Route("/freelancer/{user}/delete", name="deleteFreelancer", methods={"GET"})
     * @param User $user
     * @return RedirectResponse
     */
    public function delete(User $user)
    {
        $em = $this->getDoctrine()->getManager();
        $removeRate = $this->getDoctrine()->getRepository(FreelancerRate::class)->findOneBy(['user'=>$user]);

        $em->remove($removeRate);
        $em->flush();

        $em->remove($user);
        $em->flush();
        return $this->redirectToRoute('freelancers');
    }

}
