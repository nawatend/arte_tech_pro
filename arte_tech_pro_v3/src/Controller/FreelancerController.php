<?php

namespace App\Controller;

use App\Entity\FreelancerRate;
use App\Entity\User;
use Doctrine\Common\Annotations\AnnotationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;


class FreelancerController extends AbstractController
{
    /**
     * @Route("/freelancers", name="freelancers", methods={"GET"})
     */
    public function index()
    {
        $rateRepo = $this->getDoctrine()->getRepository(FreelancerRate::class);
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
        //dd($freelancer);

        $updateFreelancerForm = $this->createFormBuilder()
            ->add("freelancerId", HiddenType::class, ['attr' => ["value" => $freelancer->getUser()->getId()]])
            ->add("email", EmailType::class, ['attr' => ["value" => $freelancer->getUser()->getEmail()]])
            ->add("password", PasswordType::class, ['attr' => ["value" => $freelancer->getUser()->getPassword()]])
            ->add("transportCost", NumberType::class, ['attr' => ["value" => $freelancer->getTransportCost()]])
            ->add("hourRate", NumberType::class, ['attr' => ["value" => $freelancer->getHourRate()]])
            ->add('save', SubmitType::class)
            // ->add('delete', ButtonType::class,['label'=>"Verwijder"])
            ->setAction($this->generateUrl('updateFreelancer'))
            ->setMethod('POST')
            ->getForm();
        $username = $this->getUser()->getNickname();
        return $this->render("freelancer/create_freelancer.html.twig", ['username' => $username, 'form' => $updateFreelancerForm->createView(), 'user' => $freelancer]);
    }


    /**
     * @Route("/create_freelancer", name="createFreelancer", methods={"GET"})
     */
    public function create()
    {

        $newFreelancerForm = $this->createFormBuilder()
            ->add("email", EmailType::class)
            ->add("password", PasswordType::class)
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
        $rate = $rateRepo->findOneBy(['user'=>$user]);

        //dd($rate);
        if ($request->isMethod("POST")) {
            $freelancerData = $request->request->get("form");
            $em = $this->getDoctrine()->getManager();

            //update freelancer(user)
            $user->setEmail($freelancerData['email']);
            $user->setPassword($passwordEncoder->encodePassword($user, $freelancerData['password']));


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
        $em->remove($user);
        $em->flush();
        return $this->redirectToRoute('freelancers');
    }

    /**
     * @Route("/user_{email}", name="apiGetUseByEmail", methods={"GET"})
     * @param $email
     * @return Response
     * @throws AnnotationException
     * @throws ExceptionInterface
     */
    public function getUserInfo($email)
    {

        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $norm = [new DateTimeNormalizer(), new ObjectNormalizer($classMetadataFactory),];
        $encoders = [new JsonEncoder()];
        $ser = new Serializer($norm, $encoders);


        $userManager = $this->getDoctrine()->getRepository(User::class);
        $user = $userManager->findOneBy(['email' => $email]);

        // $userObj = $ser->serialize($user,'json',[ObjectNormalizer::ENABLE_MAX_DEPTH => true]);
        $userObj = $ser->normalize($user, 'json', ['groups' => 'worker', ObjectNormalizer::ENABLE_MAX_DEPTH => true]);
        //dd($userObj);
        //$userObj = json_encode($userObj);


        //dd($userObj);
        return $this->json($userObj);
    }




}
