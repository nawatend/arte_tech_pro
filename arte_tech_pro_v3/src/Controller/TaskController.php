<?php

namespace App\Controller;

use App\Entity\FreelancerRate;
use DateTime;
use App\Entity\Client;
use App\Entity\Task;
use App\Entity\Helper;
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
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\HttpFoundation\JsonResponse;


class TaskController extends AbstractController
{

    /**
     * @Route("/tasks", name="tasks", methods={"GET"})
     */
    public function index()
    {

        $helper = new Helper();
        $em = $this->getDoctrine()->getRepository(Task::class);
        $tasks = $em->findAll();
        //update all tasks when new properties added
        $em = $this->getDoctrine()->getManager();

//        foreach ($tasks as $task){
//            $task->setTotalCost($helper->calculateTaskTotalCost($task->getClient()->getHourlyRate(),$task->getTotalHours(),$task->getClient()->getTransportCost(),$task->getTransportKM()));
//
//            $em->persist($task);
//            $em->flush();
//        }
        $username = $this->getUser()->getNickname();
        //dd($username);
        return $this->render('task/index.html.twig', [
            'controller_name' => 'TaskController', "tasks" => $tasks, 'username' => $username
        ]);
    }

    /**
     * @Route("/create_task", name="createTask", methods={"GET"})
     */
    public function create()
    {

        $errors = null;
        if (isset($_GET['error'])) {
            $errors = $_GET['error'];
        }
        $newTaskForm = $this->createFormBuilder()
            ->add("clients", EntityType::class, [
                'class' => Client::class,
                'choice_label' => function (Client $client) {
                    return sprintf('%d. %s', $client->getId(), $client->getCompanyName());
                },
                'required' => true,
                'choice_value' => function (Client $client = null) {
                    return $client ? $client->getId() : '';
                },
                'placeholder' => 'Kies een klant',
            ])
            ->add('users', EntityType::class, [
                'class' => User::class,
                'choice_label' => function (User $user) {
                    if (in_array("ROLE_FREELANCER", $user->getRoles())) {
                        return sprintf('%d. %s', $user->getId(), $user->getNickname() . " - Freelancer");
                    } else {
                        return sprintf('%d. %s', $user->getId(), $user->getNickname());
                    }
                },
                'choice_value' => function (User $user = null) {
                    return $user ? $user->getId() : '';
                },
                'placeholder' => 'Kies een werknemer'
            ])
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'yy-MM-dd',
                //'input'  =>  'datetime_immutable',
                // 'placeholder'=> "Datepicker",
                'required' => true,
            ])
            ->add('startTime', TimeType::class, [
                'input' => 'array',
                'widget' => 'single_text',

                'placeholder' => [
                    'hour' => 'Uur', 'minute' => 'Minuut',
                ]
            ])
            ->add('endTime', TimeType::class, [
                'input' => 'array',
                'widget' => 'single_text',

                'placeholder' => [
                    'hour' => 'Uur', 'minute' => 'Minuut',
                ],
                'required' => true,
            ])
            ->add('description', TextareaType::class, ['required' => true])
            ->add('used', TextareaType::class, ['required' => true])
            ->add('km', NumberType::class, [
                //'html5'=> true,
                'required' => true,
            ])
            ->add('save', SubmitType::class)
            ->setAction($this->generateUrl('saveTask'))
            ->setMethod('POST')
            ->getForm();

        $username = $this->getUser()->getNickname();
        return $this->render("task/create_task.html.twig", ['form' => $newTaskForm->createView(), 'errors' => $errors, 'username' => $username]);
    }

    /**
     * @Route("/task/save", name="saveTask", methods={"GET","POST"})
     * @throws \Exception
     */

    public function save(Request $request)
    {


        //TODO: check freelancer rate
        $helper = new Helper();
        $clientRepo = $this->getDoctrine()->getRepository(Client::class);
        $userRepo = $this->getDoctrine()->getRepository(User::class);
        $rateRepo = $this->getDoctrine()->getRepository(FreelancerRate::class);
        $newTask = new Task();


        if ($request->isMethod('POST')) {
            $em = $this->getDoctrine()->getManager();

            $taskData = $request->request->get("form");

            $client = $clientRepo->find($taskData['clients']);
            $workerUser = $userRepo->find($taskData['users']);
            $dateFormatted = $helper->convertToGoodDateForDB($taskData['date']);

            //dd($taskData['endTime']);

            $newTask->setClient($client);
            $newTask->setUser($workerUser);
            $newTask->setDate(new DateTime($dateFormatted));
            $newTask->setStartTime(new DateTime($taskData['startTime']));
            $newTask->setEndTime(new DateTime($taskData['endTime']));
            $newTask->setTotalHours($helper->getHoursDifference(new DateTime($taskData['startTime']), new DateTime($taskData['endTime'])));

            $newTask->setTotalCost($helper->calculateTaskTotalCost($client->getHourlyRate(), $helper->getHoursDifference(new DateTime($taskData['startTime']), new DateTime($taskData['endTime'])), $client->getTransportCost(), $taskData['km']));
            $newTask->setDescription($taskData['description']);
            $newTask->setUsed($taskData['used']);
            $newTask->setTransportKM($taskData['km']);

            if (in_array("ROLE_FREELANCER", $workerUser->getRoles())) {
                //check rate of freelancer

                $freelancerRate = $rateRepo->findOneBy(["user" => $workerUser->getId()]);
              // dd($freelancerRate->getHourRate());

                //10% goes to Arte Tech Company
                if ($freelancerRate->getHourRate() <= ($client->getHourlyRate() * 0.9)) {
                    $em->persist($newTask);
                    $em->flush();
                } else {
                    //return $this->redirectToRoute("tasks");
                    $error = "Freelancer heeft hogere uurtarief dan klant";
                    return $this->redirect($this->generateUrl('createTask', array('error' => $error)), 301);
                }


            } else {
                $em->persist($newTask);
                $em->flush();
            }


            return $this->redirectToRoute("tasks");
        }
        return $this->redirectToRoute("tasks");
    }

    /**
     * @Route("/edit_task{taskId}", name="editTask" , methods={"GET"})
     */

    public function edit($taskId)
    {
        $task = $this->getDoctrine()->getRepository(Task::class)->find($taskId);

        $helper = new Helper();
        $errors = '';
        $updateTaskForm = $this->createFormBuilder()
            ->add('taskId', HiddenType::class, [
                'attr' => ["value" => $task->getId()]
            ])
            ->add("clients", EntityType::class, [
                'class' => Client::class,
                'choice_label' => function (Client $client) {
                    return sprintf('%d. %s', $client->getId(), $client->getCompanyName());
                },
                'required' => true,
                'choice_value' => function (Client $client = null) {
                    return $client ? $client->getId() : '';
                },
                'placeholder' => 'Kies een klant',
                'data' => $task->getClient(),
            ])
            ->add('users', EntityType::class, [
                'class' => User::class,
                'choice_label' => function (User $user) {
                    return sprintf('%d. %s', $user->getId(), $user->getEmail());
                },
                'choice_value' => function (User $user = null) {
                    return $user ? $user->getId() : '';
                },
                'placeholder' => 'Kies een werknemer',
                'data' => $task->getUser(),
            ])
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'M/d/y',
                //'input'  =>  'datetime_immutable',
                // 'placeholder'=> "Datepicker",
                'required' => true,
                'data' => $task->getDate()
            ])
            ->add('startTime', TimeType::class, [
                'input' => 'array',
                'widget' => 'single_text',
                'placeholder' => [
                    'hour' => 'Uur', 'minute' => 'Minuut',
                ],
                'data' => ['hour' => $task->getStartTime()->format('H'),
                    'minute' => $task->getStartTime()->format('i')]
            ])
            ->add('endTime', TimeType::class, [
                'input' => 'array',
                'widget' => 'single_text',
                'placeholder' => [
                    'hour' => 'Uur', 'minute' => 'Minuut',
                ],
                'required' => true,
                'data' => ['hour' => $task->getEndTime()->format('H'),
                    'minute' => $task->getEndTime()->format('i')]
            ])
            ->add('description', TextareaType::class, ['required' => true,
                'data' => $task->getDescription()
            ])
            ->add('used', TextareaType::class, ['required' => true,
                'data' => $task->getUsed()
            ])
            ->add('km', NumberType::class, [
                //'html5'=> true,
                'required' => true,
                'attr' => ['value' => $task->getTransportKM()]
                , 'data' => $task->getTransportKM()
            ])
            ->add('save', SubmitType::class)
            ->setAction($this->generateUrl('updateTask'))
            ->setMethod('POST')
            ->getForm();

        $username = $this->getUser()->getNickname();
        return $this->render("task/create_task.html.twig", ['form' => $updateTaskForm->createView(), 'task' => $task, 'errors' => $errors, 'username' => $username,]);
    }

    /**
     * @Route("/update_task", name="updateTask", methods={"GET","POST"})
     */
    public function update(Request $request)
    {

        $helper = new Helper();
        $clientManager = $this->getDoctrine()->getRepository(Client::class);
        $userManager = $this->getDoctrine()->getRepository(User::class);
        $taskManager = $this->getDoctrine()->getRepository(Task::class);
        $oldTask = $taskManager->find($request->request->get('form')['taskId']);

        if ($request->isMethod('POST')) {
            $em = $this->getDoctrine()->getManager();

            $taskData = $request->request->get("form");
            $client = $clientManager->find($taskData['clients']);
            $employeeUser = $userManager->find($taskData['users']);
            $dateFormatted = $helper->convertToGoodDateForDB($taskData['date']);

            $oldTask->setClient($client);
            $oldTask->setUser($employeeUser);
            $oldTask->setDate(new DateTime($dateFormatted));
            $oldTask->setStartTime(new DateTime($taskData['startTime']));
            $oldTask->setEndTime(new DateTime($taskData['endTime']));
            $oldTask->setTotalHours($helper->getHoursDifference(new DateTime($taskData['startTime']), new DateTime($taskData['endTime'])));
            $oldTask->setTotalCost($helper->calculateTaskTotalCost($client->getHourlyRate(), $helper->getHoursDifference(new DateTime($taskData['startTime']), new DateTime($taskData['endTime'])), $client->getTransportCost(), $taskData['km']));
            $oldTask->setDescription($taskData['description']);
            $oldTask->setUsed($taskData['used']);
            $oldTask->setTransportKM($taskData['km']);
            $em->persist($oldTask);
            $em->flush();
            return $this->redirectToRoute("tasks");
        }
        return $this->redirectToRoute("tasks");
    }

    /**
     * @Route("/task/{task}/delete", name="deleteTask", methods={"GET"})
     * @param Task $task
     * @return RedirectResponse
     */
    public function delete(Task $task)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($task);
        $em->flush();
        return $this->redirectToRoute('tasks');
    }

    /**
     * @Route("/tasksbyuserr", name="tasksByUser", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function getTasksByUser(Request $request)
    {
        $helper = new Helper();
        $clientManager = $this->getDoctrine()->getRepository(Client::class);
        $userManager = $this->getDoctrine()->getRepository(User::class);
        $taskManager = $this->getDoctrine()->getRepository(Task::class);

        if ($request->isMethod('POST')) {
            $em = $this->getDoctrine()->getManager();

            $postData = json_decode($request->getContent());
            $user = $userManager->findBy(['email' => $postData->username]);
            //dd($user[0]->getId());

            $tasks = $taskManager->findBy(['user' => $user[0]->getId()]);
            // dd($tasks);

            return new JsonResponse($tasks);
        }

    }


}
