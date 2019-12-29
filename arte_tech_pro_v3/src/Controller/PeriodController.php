<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Helper;
use App\Entity\Period;
use App\Entity\Task;
use DateTime;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PeriodController extends AbstractController
{
    /**
     * @Route("/periods", name="periods", methods={"GET"})
     * @return Response
     */
    public function index()
    {
        $error = null;
        if (isset($_GET['error'])) {
            $error = $_GET['error'];
        }

        $periodRepo = $this->getDoctrine()->getRepository(Period::class);
        $periods = $periodRepo->findAll();
        $username = $this->getUser()->getNickname();
        return $this->render('period/index.html.twig', [
            'username'=>$username, "periods" => $periods, 'error' => $error
        ]);
    }

    /**
     * @Route("/create_period", name="createPeriod", methods={"GET"})
     */
    public function create()
    {
        $errors = '';
        $newPeriodForm = $this->createFormBuilder()
            ->add('clients', EntityType::class, [
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
            ->add("startDate", DateType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'yy-MM-dd',
                //'input'  =>  'datetime_immutable',
                // 'placeholder'=> "Datepicker",
                'required' => true,
            ])
            ->add("endDate", DateType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'yy-MM-dd',
                //'input'  =>  'datetime_immutable',
                // 'placeholder'=> "Datepicker",
                'required' => true,
            ])
            ->add('save', SubmitType::class)
            ->setAction($this->generateUrl('savePeriod'))
            ->setMethod('POST')
            ->getForm();

        $username = $this->getUser()->getNickname();
        return $this->render("period/create_period.html.twig", ['username'=>$username,'form' => $newPeriodForm->createView(), 'errors' => $errors]);
    }

    /**
     * @Route("/period/save", name="savePeriod", methods={"GET","POST"})
     * @param Request $request
     * @return RedirectResponse
     * @throws \Exception
     */
    public function save(Request $request)
    {
        $helper = new Helper();
        $em = $this->getDoctrine()->getManager();

        $periodRepo = $this->getDoctrine()->getRepository(Period::class);
        $clientRepo = $this->getDoctrine()->getRepository(Client::class);
        $taskRepo = $this->getDoctrine()->getRepository(Task::class);
        $newPeriod = new Period();
        $error = "";
        if ($request->isMethod('POST')) {

            $periodData = $request->request->get('form');
            $client = $clientRepo->find($periodData['clients']);

            //set date good and get all task between dates
            $startDate = new DateTime($helper->convertToGoodDateForDB($periodData['startDate']));
            $endDate = new DateTime($helper->convertToGoodDateForDB($periodData['endDate']));

            $qb = $em->createQuery(
                'SELECT task
                FROM App\Entity\Task task
                WHERE task.date BETWEEN :startDate AND :endDate AND task.period IS NULL AND task.client = :client'
            )->setParameter('startDate', $startDate)
                ->setParameter('endDate', $endDate)
                ->setParameter('client', $client);
            $tasks = $qb->getResult();

            //dd(empty($tasks));
            if (!empty($tasks)) {
                dd(empty($tasks));
                $newPeriod->setClient($client);
                $newPeriod->setStartDate($startDate);
                $newPeriod->setEndDate($endDate);
                $em->persist($newPeriod);
                $em->flush();

                foreach ($tasks as $task) {
                    // $updateTask = $taskRepo->find($task->getId());
                    $task->setPeriod($newPeriod);
                    $em->persist($task);
                    $em->flush();
                }
                return $this->redirectToRoute("periods");
            } else {
                $error = "Geen nieuwe period: Alle Prestaties zitten al in Period.";
            }
        }
        //return $this->redirectToRoute("periods",["error" =>$error]);

        $username = $this->getUser()->getNickname();
        return $this->redirect($this->generateUrl('periods', array('error' => $error)), 301);
    }

    /**
     * @Route("/edit_period{periodId}", name="editPeriod", methods={"GET"})
     */

    public function edit($periodId)
    {
        $period = $this->getDoctrine()->getRepository(Period::class)->find($periodId);
        $errors = '';
        $updatePeriodForm = $this->createFormBuilder()
            ->add('periodId', HiddenType::class, [
                'attr' => ["value" => $period->getId()]
            ])
            ->add('clients', EntityType::class, [
                'class' => Client::class,
                'choice_label' => function (Client $client) {
                    return sprintf('%d. %s', $client->getId(), $client->getCompanyName());
                },
                'required' => true,
                'choice_value' => function (Client $client = null) {
                    return $client ? $client->getId() : '';
                },
                'placeholder' => 'Kies een klant',
                'data' => $period->getClient()
            ])
            ->add("startDate", DateType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'M/d/y',
                //'input'  =>  'datetime_immutable',
                // 'placeholder'=> "Datepicker",
                'required' => true,
                'data' => $period->getStartDate()
            ])
            ->add("endDate", DateType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'M/d/y',
                //'input'  =>  'datetime_immutable',
                // 'placeholder'=> "Datepicker",
                'required' => true,
                'data' => $period->getEndDate()
            ])
            ->add('save', SubmitType::class)
            ->setAction($this->generateUrl('updatePeriod'))
            ->setMethod('POST')
            ->getForm();
        $username = $this->getUser()->getNickname();
        return $this->render("period/create_period.html.twig", ['username'=>$username,'form' => $updatePeriodForm->createView(), 'period' => $period, 'errors' => $errors]);
    }


    /**
     * @Route("/update_period", name="updatePeriod" , methods={"GET","POST"})
     * @param Request $request
     * @return RedirectResponse|Response
     * @throws \Exception
     */
    public function update(Request $request)
    {

        $helper = new Helper();
        $em = $this->getDoctrine()->getManager();

        $periodRepo = $this->getDoctrine()->getRepository(Period::class);
        $clientRepo = $this->getDoctrine()->getRepository(Client::class);
        $taskRepo = $this->getDoctrine()->getRepository(Task::class);
        $oldPeriod = $periodRepo->find($request->request->get('form')['periodId']);

        if ($request->isMethod('POST')) {

            $periodData = $request->request->get('form');
            $client = $clientRepo->find($periodData['clients']);

            //get old tasks
            $qbOld = $em->createQuery(
                'SELECT task
                FROM App\Entity\Task task
                WHERE task.date BETWEEN :startDate AND :endDate AND task.period IS NOT NULL AND  task.client = :client'
            )->setParameter('startDate', $oldPeriod->getStartDate())
                ->setParameter('endDate', $oldPeriod->getEndDate())
                ->setParameter('client', $client);
            $oldTasks = $qbOld->getResult();
            //dd($oldTasks);

            //reset old Tasks of period to null
            foreach ($oldTasks as $task) {
                // $updateTask = $taskRepo->find($task->getId());
                $task->setPeriod(null);
                $em->persist($task);
                $em->flush();
            }

            //set date good and get all task between new dates
            $startDate = new DateTime($helper->convertToGoodDateForDB($periodData['startDate']));
            $endDate = new DateTime($helper->convertToGoodDateForDB($periodData['endDate']));

            $qb = $em->createQuery(
                'SELECT task
                FROM App\Entity\Task task
                WHERE task.date BETWEEN :startDate AND :endDate AND task.client = :client'
            )->setParameter('startDate', $startDate)
                ->setParameter('endDate', $endDate)
                ->setParameter('client', $client);
            $tasks = $qb->getResult();

            //update new info to period
            $oldPeriod->setClient($client);
            $oldPeriod->setStartDate($startDate);
            $oldPeriod->setEndDate($endDate);
            $em->persist($oldPeriod);
            $em->flush();

            //set period to tasks for new scope of dates
            foreach ($tasks as $task) {
                // $updateTask = $taskRepo->find($task->getId());
                $task->setPeriod($oldPeriod);
                $em->persist($task);
                $em->flush();
            }
            return $this->redirectToRoute("periods");
        }
        return $this->render("period/index.html.twig");
    }

    /**
     * @Route("/period/{period}/delete", name="deletePeriod" , methods={"GET"})
     * @param Period $period
     * @return RedirectResponse
     */
    public function delete(Period $period)
    {
        $em = $this->getDoctrine()->getManager();
        $tasks = $period->getTasks();

        //reset tasks of this period to null
        foreach ($tasks as $task) {
            $task->setPeriod(null);
            $em->persist($task);
            $em->flush();
        }

        $em->remove($period);
        $em->flush();
        return $this->redirectToRoute('periods');
    }
}
