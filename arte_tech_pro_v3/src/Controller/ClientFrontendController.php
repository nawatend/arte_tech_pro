<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Period;
use App\Entity\Task;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ClientFrontendController extends AbstractController
{
    /**
     * @Route("/client_home", name="clientHome")
     */
    public function index()
    {
        $userId = $this->getUser();

        $clientRepo = $this->getDoctrine()->getRepository(Client::class);
        $periodRepo = $this->getDoctrine()->getRepository(Period::class);

        $client = $clientRepo->findBy(['user' => $userId]);
        $notConfirmedPeriods = $periodRepo->findBy(['client' => $client, 'isConfirm' => 0]);

        $username = $this->getUser()->getNickname();
        return $this->render('client_frontend/index.html.twig', [
            'username'=>$username, "periods" => $notConfirmedPeriods
        ]);
    }


    /**
     * @Route("/client_confirmed_periods", name="clientConfirmedPeriods")
     */
    public function getToConfirmPeriods()
    {
        $userId = $this->getUser();

        $clientRepo = $this->getDoctrine()->getRepository(Client::class);
        $periodRepo = $this->getDoctrine()->getRepository(Period::class);

        $client = $clientRepo->findBy(['user' => $userId]);
        $notConfirmedPeriods = $periodRepo->findBy(['client' => $client, 'isConfirm' => 1]);

        $username = $this->getUser()->getNickname();
        return $this->render('client_frontend/confirmed_periods.html.twig', [
            'username'=>$username, "periods" => $notConfirmedPeriods
        ]);
    }

    /**
     * @Route("/client_detail_period_{periodId}", name="clientDetailPeriod")
     * @param $periodId
     * @return Response
     */
    public function detailPeriod($periodId)
    {

        $error = null;
        if (isset($_GET['error'])) {
            $error = $_GET['error'];
        }

        $periodRepo = $this->getDoctrine()->getRepository(Period::class);
        $taskRepo = $this->getDoctrine()->getRepository(Task::class);
        $period = $periodRepo->find($periodId);
        $tasksOfPeriod = $taskRepo->findBy(['period' => $period], ['date' => 'ASC']);

        $totalCost = 0;

        foreach ($tasksOfPeriod as $task){
            $totalCost += $task->getTotalCost();
        }

        $confirmForm =$this->createFormBuilder()
            ->add("periodId", HiddenType::class, ['attr' => ["value" => $period->getId()]])
            ->add('toConfirm', TextType::class)
            ->add('save', SubmitType::class)
            ->setAction($this->generateUrl('confirmPeriod'))
            ->setMethod('POST')
            ->getForm();


        //dd($tasksOfPeriod);
        $username = $this->getUser()->getNickname();
        return $this->render('client_frontend/client_period_details.html.twig', [
            'error'=>$error,'form' => $confirmForm->createView(),"tasksOfPeriod" =>$tasksOfPeriod, "period"=>$period,'username'=>$username,'totalCostOfPeriod' =>$totalCost
        ]);
    }


    /**
     * @Route("/confirm_period", name="confirmPeriod", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function confirmPeriod(Request $request)
    {

        $periodRepo = $this->getDoctrine()->getRepository(Period::class);
        $em = $this->getDoctrine()->getManager();

        if ($request->isMethod("POST")) {
            $formData = $request->request->get('form');

            $currentPeriod = $periodRepo->find($formData["periodId"]);

            $currentPeriod->setIsConfirm(true);
            //dd($currentPeriod);

            if ($formData["toConfirm"] == "BEVESTIG"){
                $em->persist($currentPeriod);
                $em->flush();
            }else{
                $error = "Typ BEVESTIG juist in!";
                return $this->redirect($this->generateUrl('clientDetailPeriod',array( "periodId"=>$formData['periodId'],'error' => $error)), 301);
            }

            return $this->redirectToRoute("clientConfirmedPeriods");
        }
    }


}
