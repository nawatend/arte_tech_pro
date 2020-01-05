<?php

namespace App\Controller;

use App\Entity\Client;
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
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ClientController extends AbstractController
{
    /**
     * @Route("/clients", name="clients", methods={"GET"})
     */
    public function index()
    {

        $em = $this->getDoctrine()->getRepository(Client::class);
        $clients = $em->findAll();
        $username = $this->getUser()->getNickname();
        return $this->render('client/index.html.twig', [
            'username'=>$username, 'clients' => $clients
        ]);
    }

    /**
     * @Route("/edit_client{clientId}", name="editClient" , methods={"GET"})
     * @param $clientId
     * @return Response
     */
    public function edit($clientId)
    {
        $clientManager = $this->getDoctrine()->getRepository(Client::class);
        $client = $clientManager->find($clientId);
        $errors = '';


        $updateClientForm = $this->createFormBuilder()
            ->add("clientId", HiddenType::class, ['attr' => ["value" => $client->getId()]])
            ->add("email", EmailType::class, ['disabled' => true, 'attr' => ["value" => $client->getUser()->getEmail()]])
            ->add("password", PasswordType::class, ['disabled' => true, 'attr' => ["value" => $client->getUser()->getPassword()]])
            ->add("companyName", TextType::class, ['attr' => ["value" => $client->getCompanyName()]])
            ->add("telephone", TextType::class, ['attr' => ["value" => $client->getTelephone()]])
            ->add("transportCost", NumberType::class, ['attr' => ["value" => $client->getTransportCost()]])
            ->add("hourlyRate", NumberType::class, ['attr' => ["value" => $client->getHourlyRate()]])
            ->add('save', SubmitType::class)
            // ->add('delete', ButtonType::class,['label'=>"Verwijder"])
            ->setAction($this->generateUrl('updateClient'))
            ->setMethod('POST')
            ->getForm();
        $username = $this->getUser()->getNickname();
        return $this->render("client/create_client.html.twig", ['form' => $updateClientForm->createView(), 'client' => $client, 'errors'=>$errors,'username'=>$username]);
    }


    /**
     * @Route("/create_client", name="createClient" , methods={"GET"})
     *
     */
    public function create()
    {
        $errors = '';
        $newClientForm = $this->createFormBuilder()
            ->add("email", EmailType::class)
            ->add("password", PasswordType::class,['attr' => ['autocomplete' => 'new-password']])
            ->add("companyName", TextType::class)
            ->add("telephone", TextType::class)
            ->add("transportCost", NumberType::class)
            ->add("hourlyRate", NumberType::class)
            ->add('save', SubmitType::class)
            ->setAction($this->generateUrl('saveClient'))
            ->setMethod('POST')
            ->getForm();
        $username = $this->getUser()->getNickname();
        return $this->render("client/create_client.html.twig", ['username'=>$username,'form' => $newClientForm->createView(), 'errors' => $errors]);
    }

    /**
     * @Route("/client/save", name="saveClient" , methods={"POST", "GET"})
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return RedirectResponse|Response
     */
    public function save(Request $request, UserPasswordEncoderInterface $passwordEncoder, ValidatorInterface $validator)
    {
        $user = new User();
        $client = new Client();

        if ($request->isMethod("POST")) {

            $clientData = $request->request->get("form");

            //dd($userData);
            $em = $this->getDoctrine()->getManager();

            // create new user
            $user->setEmail($clientData['email']);
            $user->setPassword($passwordEncoder->encodePassword($user, $clientData['password']));
            $user->setNickname($clientData['companyName']);
            $user->setRoles(['ROLE_CLIENT']);

            $errors = $validator->validate($user);
            //if there is error, go back to create with error message
            if (count($errors) > 0) {
                $newClientForm = $this->createFormBuilder()
                    ->add("email", EmailType::class)
                    ->add("password", PasswordType::class)
                    ->add("companyName", TextType::class)
                    ->add("telephone", TextType::class)
                    ->add("transportCost", NumberType::class)
                    ->add("hourlyRate", NumberType::class)
                    ->add('save', SubmitType::class)
                    ->setAction($this->generateUrl('saveClient'))
                    ->setMethod('POST')
                    ->getForm();

                return $this->render('client/create_client.html.twig', [
                    'form' => $newClientForm->createView(), 'errors' => $errors,
                ]);
            } else {
                $em->persist($user);
                $em->flush();

                //create new client based on new user
                $client->setUser($user);
                $client->setCompanyName($clientData['companyName']);

                $client->setHourlyRate($clientData['hourlyRate']);
                $client->setTransportCost($clientData['transportCost']);
                $client->setTelephone($clientData['telephone']);

                $em->persist($client);
                $em->flush();
                //return $this->render("client/index.html.twig");
                return $this->redirectToRoute("clients");
            }
        }
    }


    /**
     * @Route("/client/update", name="updateClient",  methods={"GET","POST"})
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param ValidatorInterface $validator
     * @return RedirectResponse|Response
     */
    public function update(Request $request , UserPasswordEncoderInterface $passwordEncoder, ValidatorInterface $validator)
    {
        $clientManager = $this->getDoctrine()->getRepository(Client::class);
        $client = $clientManager->find($request->request->get('form')['clientId']);
        $userRepo = $this->getDoctrine()->getRepository(User::class);

        if ($request->isMethod("POST")) {
            $clientData = $request->request->get("form");
            $em = $this->getDoctrine()->getManager();

            //create new client based on new user
            $client->setCompanyName($clientData['companyName']);
            $client->setHourlyRate($clientData['hourlyRate']);
            $client->setTransportCost($clientData['transportCost']);
            $client->setTelephone($clientData['telephone']);

            $em->persist($client);
            $em->flush();

            //dd($clientData);

            $user = $userRepo->find($client->getUser());
            //only able to update company name
            $user->setNickname($clientData['companyName']);
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute("clients");
        }

        return $this->redirectToRoute("clients");
    }


    /**
     * @Route("/client/{client}/delete", name="deleteClient",  methods={"GET"})
     * @param Client $client
     * @return RedirectResponse
     */
    public function delete(Client $client)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($client);
        $em->flush();
        return $this->redirectToRoute('clients');
    }
}
