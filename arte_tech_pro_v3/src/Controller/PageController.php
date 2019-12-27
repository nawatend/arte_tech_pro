<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PageController extends AbstractController
{

    /**
     * @Route("/", name="home", methods={"GET","POST"})
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return RedirectResponse|Response
     */
    public function index(Request $request, UserPasswordEncoderInterface $passwordEncoder)
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
            }else{
                //dd(in_array("ROLE_CLIENT", $user->getRoles()));
                $username = $this->getUser()->getNickname();
                return $this->render('page/index.html.twig', [
                    'username'=>$username, 'form' => $loginForm->createView()
                ]);
            }
        }

       // return $this->redirectToRoute("app_login");
    }
}
