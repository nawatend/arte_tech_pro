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
        $totalTimeDiff = "00:00";
        if ($startTime->diff($endTime)->format("%H:%I") > 12) {
            $totalTimeDiff = (12 + date('h', strtotime($startTime->diff($endTime)->format("%H:%I"))))
                . ":" . (date('i', strtotime($startTime->diff($endTime)->format("%H:%I"))) / 60);

            // dd($totalTimeDiff);
            return $totalTimeDiff;
        } else {
            return $startTime->diff($endTime)->format("%H:%I");
        }
    }

    public function calculateTaskTotalCost($hourlyRate, $totalHours, $transportRate, $transportKM, $salaryType, $salaryTypeValue)
    {


        $totalTransportCost = $transportKM * $transportRate;
        //convert hour to multipleable with cash
        $xAbleHours = date('h', strtotime($totalHours));
        $xAbleMinutes = date('i', strtotime($totalHours)) / 60;
        //total(hour) multipleable with cash
        $percentHours = $xAbleHours + $xAbleMinutes;

        $mainHours = ($percentHours > 8) ? 8 : $percentHours;
        $extraHours = ($percentHours > 8) ? $percentHours - 8 : 0;

        $mainCost = 0;
        $extraCost = 0;

        //on weekday if with over-hours
        if ($salaryType === "WEEKDAY") {
            $mainCost = $mainHours * $hourlyRate;
            $extraCost = $extraHours * $hourlyRate * $salaryTypeValue;

        }

        if($salaryType == "SATURDAY" || $salaryType == "SUNDAY"){
            //on weekend
            $mainCost = $mainHours * $hourlyRate * $salaryTypeValue;
            $extraCost = $extraHours * $hourlyRate * $salaryTypeValue;
        }

        $totalWorkCost = $mainCost + $extraCost;

        //dd($xAbleHours . " - " . $xAbleMinutes);


        return $totalTransportCost + $totalWorkCost;
    }

    public function getDateFromTimestamp($timestamp)
    {
        return date("Y-m-d", strtotime('+1 days', ($timestamp / 1000)));
    }

    public function getTimeFromTimestamp($timestamp)
    {

        $date = new DateTime();
        //small cheat cuz timestamp is 1 hour late for my needs
        //360 0000 mili seconds = 1 hour
        return $date->setTimestamp((3600000 + $timestamp) / 1000);
    }

    public function getWeekType($date)
    {
        $weekType = "WEEKDAY";
        $weekDay = date('w', strtotime($date));

        if($weekDay == 0){
            $weekType = "SUNDAY";
        }
        if($weekDay == 6 )
        {
            $weekType = "SATURDAY";
        }




        return $weekType;
    }
}
