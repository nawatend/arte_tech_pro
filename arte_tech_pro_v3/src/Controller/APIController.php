<?php

namespace App\Controller;

use App\Entity\Client;
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
        $norm = [ new DateTimeNormalizer(), new ObjectNormalizer($classMetadataFactory), ];
        $encoders = [new JsonEncoder()];
        $serializer = new Serializer($norm,$encoders);



        if ($request->isMethod('POST')) {
            $postData = json_decode($request->getContent());
            $user = $userManager->findOneBy(['email'=>$postData->email]);


            $tasks = $taskManager->findBy(['user'=>$user->getId()]);

            $tasks = $serializer->normalize($tasks,'json',['groups'=>'taskInfo']);
            return $this->json($tasks);
        }
    }

    /**
     * @Route("/api/clients", name="api_getClients", methods={"POST"})
     */
    public function getClients()
    {
        return $this->json(["client"=>"nawnag"]);
    }

    /**
     * @Route("/api/getclientsbyuser", name="api_getClientsByUser", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function getClientsByUser(Request $request)
    {
        return $this->json(["client_byUser"=>"nawnasssg"]);
    }


    /**
     * @Route("/api/savetask", name="api_saveNewTask", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function saveNewTask(Request $request)
    {
        return $this->json(["client_byUser"=>"nawnasssg"]);
    }

    /**
     * @Route("/api/saverate", name="api_updateRate", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function updateRate(Request $request)
    {
        return $this->json(["client_byUser"=>"nawnassg"]);
    }


}
