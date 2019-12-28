<?php

namespace App\Entity;
use DateTime;
use DateTimeZone;

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
}
