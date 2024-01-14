<?php
declare(strict_types = 1);

require("../vendor/autoload.php");

use \sgoettsch\FritzboxAHA\FritzboxAHA;

$aha = new FritzboxAHA();

$aha->login("fritz.box", "", "password");

echo "Session id: " . $aha->getSid() . "\n\n";

$switches = $aha->getAllSwitches();
foreach ($switches as $switch) {
    echo "Switch AIN: " . $switch . "\n";

    echo "Switch name: " . $aha->getSwitchName($switch) . "\n";

    echo "Switch is present: " . $aha->isSwitchPresent($switch) . "\n";

    echo "Switch state: " . $aha->getSwitchState($switch) . "\n";

    echo "Current power consumption: " . $aha->getSwitchPower($switch) . " mW\n";

    echo "Total power consumption: " . $aha->getSwitchEnergy($switch) . " Wh\n";

    echo "Turn on.\n";
    $aha->setSwitchOn($switch);
    sleep(2);

    echo "Turn off.\n";
    $aha->setSwitchOff($switch);
    sleep(2);

    echo "Toggle switch.\n\n";
    $aha->setSwitchToggle($switch);
    sleep(2);
}

$devs = $aha->getAllDevices();
foreach ($devs as $dev) {
    $ain = $dev["aid"];
    echo "AIN: " . $ain . "\n";
}
