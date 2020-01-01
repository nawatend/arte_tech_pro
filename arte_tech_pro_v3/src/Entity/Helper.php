<?php

namespace App\Entity;
use DateTime;
use DateTimeZone;
use App\Entity\Client;
use App\Entity\Complain;
use App\Entity\Period;
use App\Entity\Task;
use FontLib\Table\Type\name;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Helper
{

    public function convertToGoodDateForDB($date)
    {
        return date('Y-m-d', strtotime($date));
    }

    public function getHoursDifference($startTime, $endTime)
    {
        return $startTime->diff($endTime)->format("%H:%I");
    }

    public function calculateTaskTotalCost($hourlyRate, $totalHours, $transportRate, $transportKM){


        $xAbleHours = date('h',strtotime($totalHours));
        $xAbleMinutes = date('i',strtotime($totalHours))/60;


        return $hourlyRate * ($xAbleHours + $xAbleMinutes) + ($transportRate * $transportKM);
    }

    public function getDateFromTimestamp($timestamp){

        return date("Y-m-d", $timestamp/1000);

    }

    public function getTimeFromTimestamp($timestamp){

        $date = new DateTime();
        //small cheat cuz timestamp is 1 hour late for my needs
        //360 0000 miliseconds = 1 hour
        return $date->setTimestamp(( 3600000 + $timestamp)/1000);
    }


    /**
     * @param $tasksOfPeriod
     * @param $period
     * @param $totalCost
     */
    public function sendMail($tasksOfPeriod,$period,$totalCost){




        $message = new \Swift_Message('Hello Email');
        $templateParams = [
            "name" => "nawang", "tasksOfPeriod" => $tasksOfPeriod, "period" => $period, 'totalCostOfPeriod' => $totalCost
        ];

        //setup message for email
        $message
            ->setFrom('artetechpro@gmail.com')
            ->setTo('n.tendar@gmail.com')
            ->setBody(
                $this->renderView(
                // templates/emails/registration.html.twig
                    'emails/period.html.twig',
                    $templateParams
                ),
                'text/html'
            );



        //send mail now
        $mailer->send($message);
    }
}
