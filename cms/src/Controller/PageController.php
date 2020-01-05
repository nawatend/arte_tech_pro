<?php

namespace App\Controller;

use App\Entity\Period;
use App\Entity\Task;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PageController extends AbstractController
{

    /**
     * @Route("/", name="home", methods={"GET","POST"})
     * @return RedirectResponse|Response
     */
    public function index()
    {

        $currentUser = $this->getUser();


        $userRepo = $this->getDoctrine()->getRepository(User::class);
        $users = $userRepo->findAll();

        $taskRepo = $this->getDoctrine()->getRepository(Task::class);
        $tasks = $taskRepo->findAll();


        $periodRepo = $this->getDoctrine()->getRepository(Period::class);
        $periods = $periodRepo->findAll();



        $employees = [];
        $freelancers = [];
        $clients = [];



        //get all employees
        foreach($users as $user){
            if (in_array('ROLE_EMPLOYEE', $user->getRoles())) {
                $employees[] = $user;
            }
            if (in_array('ROLE_FREELANCER', $user->getRoles())) {
                $freelancers[] = $user;
            }

            if (in_array('ROLE_CLIENT', $user->getRoles())) {
                $clients[] = $user;
            }
        }


        $totalEmployee = count($employees);
        $totalFreelancer = count($freelancers);
        $totalClient = count($clients);


        if ($currentUser == null) {
            return $this->redirectToRoute("app_login");
        } else {


            if (in_array("ROLE_CLIENT", $currentUser->getRoles())) {
               // dd($currentUser->getRoles());
                return $this->redirectToRoute("clientHome");
            }

            if (in_array("ROLE_EMPLOYEE", $currentUser->getRoles())) {
               // dd($currentUser->getRoles());
                return $this->redirectToRoute("app_logout");
            }

            if (in_array("ROLE_FREELANCER", $currentUser->getRoles())) {
              //  dd($currentUser);
                return $this->redirectToRoute("app_logout");
            }
            if (in_array("ROLE_ADMIN",$currentUser->getRoles())) {
                $username = $this->getUser()->getNickname();

                //dd($currentUser->getRoles());
                return $this->render('page/index.html.twig', [
                    'username' => $username, "totalEmployee" =>$totalEmployee
                    , "totalFreelancer" =>$totalFreelancer
                    , "totalClient"=> $totalClient
                    ,'totalTask' =>count($tasks)
                    , 'totalPeriod' => count($periods)
                ]);
            }

        }

    }

    /**
     * @Route("/create_admin", name="createAdmin", methods={"GET","POST"})
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return RedirectResponse|Response
     */
    public function createAdmin(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {

        $user = new User();
        $loginForm = $this->createFormBuilder($user)
            //->add("username", TextType::class, ["label" => "Vul in gebruikersnaam"])
            ->add("email", EmailType::class)
            ->add("password", PasswordType::class)
            ->add('save', SubmitType::class)
            ->getForm();


        if ($request->isMethod("post")) {

            $userData = $request->request->get("form");

            //dd($userData);
            $em = $this->getDoctrine()->getManager();

            // $user->setUsername($userData['username']);
            $user->setEmail($userData['email']);
            $user->setPassword($passwordEncoder->encodePassword($user, $userData['password']));
            //$user->setNickname("Random naam hahah");
            $user->setRoles(['ROLE_ADMIN']);

            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute("home");
        }


        $user = $this->getUser();


        //dd($user);
        if ($user == null) {
            return $this->redirectToRoute("app_login");

        } else {
            if (in_array("ROLE_CLIENT", $user->getRoles())) {
                //dd($user->getRoles());
                return $this->redirectToRoute("clientHome");
            } else {
                //dd(in_array("ROLE_CLIENT", $user->getRoles()));
                $username = $this->getUser()->getNickname();
                return $this->render('page/create_admin.html.twig', [
                    'username' => $username, 'form' => $loginForm->createView()
                ]);
            }
        }

    }
}
