<?php
declare(strict_types = 1);

require("../vendor/autoload.php");

use \JanKnipper\FritzboxAHA\FritzboxAHA;
use \PHPCurl\CurlWrapper\Curl;

$aha = new FritzboxAHA(new Curl);

$aha->login("fritz.box", "", "password");

echo "Session id: " . $aha->getSid() . "\n";

$devs = $aha->getAllDevices();
foreach ($devs as $dev) {
    $ain = $dev["identifier"];
    echo "Current temperature on device " . $ain . ": " . $aha->getTemperature($ain) . "\n";
    echo "Soll temperature for device " . $ain . ": " . $aha->getTemperatureSoll($ain) . "\n";
    echo "Comfort temperature for device " . $ain . ": " . $aha->getTemperatureComfort($ain) . "\n";
    echo "Low temperature for device " . $ain . ": " . $aha->getTemperatureLow($ain) . "\n";
}

$groups = $aha->getAllGroups();
foreach ($groups as $group) {
    echo "Group found: " . $group["identifier"] . "\n";
    $aha->setTemperature($group["identifier"], 18);
    echo "Setting temperature to 18 degrees for group " . $group["identifier"] . "\n";
}
