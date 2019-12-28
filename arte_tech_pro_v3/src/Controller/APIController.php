<?php

namespace App\Controller;

use DateTime;
use App\Entity\Client;
use App\Entity\Helper;
use App\Entity\Task;
use Doctrine\Common\Annotations\AnnotationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\FreelancerRate;
use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

class APIController extends AbstractController
{
    /**
     * @Route("/api/tasksbyuser", name="api_getTasksByUser", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     * @throws AnnotationException
     * @throws ExceptionInterface
     */
    public function getTasksByUser(Request $request)
    {
        $userManager = $this->getDoctrine()->getRepository(User::class);
        $taskManager = $this->getDoctrine()->getRepository(Task::class);
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $norm = [new DateTimeNormalizer(), new ObjectNormalizer($classMetadataFactory),];
        $encoders = [new JsonEncoder()];
        $serializer = new Serializer($norm, $encoders);


        if ($request->isMethod('POST')) {
            $postData = json_decode($request->getContent());
            $user = $userManager->findOneBy(['email' => $postData->email]);


            $tasks = $taskManager->findBy(['user' => $user->getId()]);

            $tasks = $serializer->normalize($tasks, 'json', ['groups' => 'taskInfo']);
            return $this->json($tasks);
        }
    }

    /**
     * @Route("/api/clients", name="api_getClients", methods={"POST"})
     */
    public function getClients()
    {
        return $this->json(["client" => "nawnag"]);
    }

    /**
     * @Route("/api/getclientsbyuser", name="api_getClientsByUser", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function getClientsByUser(Request $request)
    {
        return $this->json(["client_byUser" => "nawnasssg"]);
    }


    /**
     * @Route("/api/saverate", name="api_updateRate", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function updateRate(Request $request)
    {
        return $this->json(["client_byUser" => "nawnassg"]);
    }

    /**
     * @Route("/api/getuserinfo", name="api_getUserInfo", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     * @throws AnnotationException
     * @throws ExceptionInterface
     */
    public function getUserInfo(Request $request)
    {

        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $norm = [new DateTimeNormalizer(), new ObjectNormalizer($classMetadataFactory),];
        $encoders = [new JsonEncoder()];
        $ser = new Serializer($norm, $encoders);

        $userManager = $this->getDoctrine()->getRepository(User::class);

        if ($request->isMethod('POST')) {
            $postData = json_decode($request->getContent());
            $user = $userManager->findOneBy(['email' => $postData->email]);

            $userObj = $ser->normalize($user, 'json', ['groups' => 'userInfo', ObjectNormalizer::ENABLE_MAX_DEPTH => true]);

            return $this->json($userObj);
        }

    }

    /**
     * @Route("/api/savetask", name="api_saveTask", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function saveTask(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $clientRepo = $this->getDoctrine()->getRepository(Client::class);
        $userRepo = $this->getDoctrine()->getRepository(User::class);
        $newTask = new Task();
        $helper = new Helper();

        if ($request->isMethod("POST")){

            $postData = json_decode($request->getContent());



            //client id
            //user id
            //date
            //start time - timestamp
            //end time - timestamp
            //description
            //Used
            //km

            $client = $clientRepo->find($postData->clientId);
            $employeeUser = $userRepo->find($postData->workerId);
            $dateFormatted = $helper->convertToGoodDateForDB($postData->date);
           // dd($helper->getTimeFromTimestamp($postData->startTime));

            $startTime = $helper->getTimeFromTimestamp($postData->startTime);
            $endTime = $helper->getTimeFromTimestamp($postData->endTime);
            $newTask->setClient($client);
            $newTask->setUser($employeeUser);
            $newTask->setDate(new DateTime($helper->getDateFromTimestamp($postData->date)));

            $newTask->setStartTime($startTime);
            $newTask->setEndTime($endTime);
            $newTask->setTotalHours($helper->getHoursDifference($startTime,$endTime));

            $newTask->setTotalCost($helper->calculateTaskTotalCost($client->getHourlyRate(),$helper->getHoursDifference($startTime,$endTime),$client->getTransportCost(),$postData->km));
            $newTask->setDescription($postData->description);
            $newTask->setUsed($postData->used);
            $newTask->setTransportKM($postData->km);


            $em->persist($newTask);
            $em->flush();

        }
        return $this->json("Saved");
    }
}
