<?php

namespace App\Controller;


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

class EmployeeController extends AbstractController
{
    /**
     * @Route("/employees", name="employees", methods={"GET"})
     */
    public function index()
    {

        $em = $this->getDoctrine()->getRepository(User::class);
        $users = $em->findAll();

        $employees = [];

        //get all employees
        foreach ($users as $user) {
            if (in_array('ROLE_EMPLOYEE', $user->getRoles())) {
                $employees[] = $user;
            }
        }


        $username = $this->getUser()->getNickname();
        return $this->render('employee/index.html.twig', [
            'username' => $username, "employees" => $employees
        ]);
    }

    /**
     * @Route("/edit_employee{employeeId}", name="editEmployee" , methods={"GET"})
     * @param $employeeId
     * @return Response
     */
    public function edit($employeeId)
    {
        $employeeManager = $this->getDoctrine()->getRepository(User::class);
        $userManager = $this->getDoctrine()->getRepository(User::class);
        $employee = $employeeManager->find($employeeId);


        // dd($employee->getUser()->getPassword());

        $updateEmployeeForm = $this->createFormBuilder()
            ->add("employeeId", HiddenType::class, ['attr' => ["value" => $employee->getId()]])
            ->add("email", EmailType::class, ['disabled' => true, 'attr' => ["value" => $employee->getEmail()]])
            ->add("password", PasswordType::class, ['disabled' => true, 'attr' => ["value" => $employee->getPassword()]])
            ->add("nickname", TextType::class, ['attr' => ["value" => $employee->getNickname()]])
//            ->add("telephone", TextType::class,['attr'=> ["value"=>$employee->getTelephone()]])
//            ->add("transportCost", NumberType::class,['attr'=> ["value"=>$employee->getTransportCost()]])
//            ->add("hourlyRate", NumberType::class,['attr'=> ["value"=>$employee->getHourlyRate()]])
            ->add('save', SubmitType::class)
            // ->add('delete', ButtonType::class,['label'=>"Verwijder"])
            ->setAction($this->generateUrl('updateEmployee'))
            ->setMethod('POST')
            ->getForm();
        $username = $this->getUser()->getNickname();
        return $this->render("employee/create_employee.html.twig", ['username' => $username, 'form' => $updateEmployeeForm->createView(), 'user' => $employee]);
    }

    /**
     * @Route("/create_employee", name="createEmployee", methods={"GET"})
     */
    public function create()
    {

        $newEmployeeForm = $this->createFormBuilder()
            ->add("email", EmailType::class, ['attr' => ['autocomplete' => 'null']])
            ->add("password", PasswordType::class, ['attr' => ['autocomplete' => "new-password"]])
            ->add("nickname", TextType::class, ['attr' => ['autocomplete' =>"new-password"]])
//            ->add("telephone", TextType::class)
//            ->add("transportCost", NumberType::class)
//            ->add("hourlyRate", NumberType::class)
            ->add('save', SubmitType::class)
            ->setAction($this->generateUrl('saveEmployee'))
            ->setMethod('POST')
            ->getForm();
        $username = $this->getUser()->getNickname();
        return $this->render("employee/create_employee.html.twig", ['username' => $username, 'form' => $newEmployeeForm->createView()]);
    }


    /**
     * @Route("/employee/save", name="saveEmployee" , methods={"GET","POST"})
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return RedirectResponse|Response
     */
    public function save(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new User();

        if ($request->isMethod("POST")) {
            $employeeData = $request->request->get("form");
            //dd($userData);
            $em = $this->getDoctrine()->getManager();

            // create new user
            $user->setEmail($employeeData['email']);
            $user->setPassword($passwordEncoder->encodePassword($user, $employeeData['password']));
            $user->setNickname($employeeData['nickname']);
            $user->setRoles(['ROLE_EMPLOYEE']);
            $em->persist($user);
            $em->flush();


            return $this->redirectToRoute("employees");
        }

        return $this->redirectToRoute("employees");
    }

    /**
     * @Route("/employee/update", name="updateEmployee" , methods={"GET", "POST"})
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return RedirectResponse|Response
     */
    public function update(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $userManager = $this->getDoctrine()->getRepository(User::class);
        $user = $userManager->find($request->request->get('form')['employeeId']);


        if ($request->isMethod("POST")) {
            $employeeData = $request->request->get("form");
            $em = $this->getDoctrine()->getManager();

            //update employee(user)
              $user->setNickname($employeeData['nickname']);

            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute("employees");
        }

        return $this->redirectToRoute("employees");
    }


    /**
     * @Route("/employee/{user}/delete", name="deleteEmployee", methods={"GET"})
     * @param User $user
     * @return RedirectResponse
     */
    public function delete(User $user)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();
        return $this->redirectToRoute('employees');
    }

}
