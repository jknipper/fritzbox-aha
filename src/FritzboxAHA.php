<?php
declare(strict_types=1);

namespace JanKnipper\FritzboxAHA;

use \PHPCurl\CurlWrapper\CurlInterface;
use \PHPCurl\CurlWrapper\Curl;

/**
 * Class FritzboxAHA
 * @package JanKnipper\FritzboxAHA
 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
 */
class FritzboxAHA
{
    private $loginUrl = "http://%s/login_sid.lua";
    private $ahaUrl = "http://%s/webservices/homeautoswitch.lua?switchcmd=%s&sid=%s";
    //protected $aha_url = "http://%s/webservices/homeautoswitch.lua?switchcmd=%s&sid=%s&ain=%s&param=%s";
    private $curl;
    private $host;
    private $useSsl;
    private $checkCert;
    private $user;
    private $password;
    private $sid;

    /**
     * FritzboxAHA constructor.
     * @param CurlInterface $curl
     */
    public function __construct(
        CurlInterface $curl = null
    ) {
        if ($curl === null) {
            $curl = new Curl;
        }

        $this->curl = $curl;
    }

    /**
     * @param $host
     * @param $user
     * @param $password
     * @param bool $useSsl
     * @param bool $checkCert
     */
    public function login(
        $host,
        $user,
        $password,
        $useSsl = false,
        $checkCert = true
    ) {
        $this->host = $host;
        $this->user = $user;
        $this->password = $password;
        $this->useSsl = $useSsl;
        $this->checkCert = $checkCert;
        $this->sid = $this->getSessionId();
    }

    /**
     * @param $challenge
     * @return string
     */
    public function getChallengeResponse($challenge)
    {
        $response = $challenge . "-" .
            md5(
                mb_convert_encoding(
                    $challenge . "-" . $this->password,
                    "UTF-16LE",
                    "UTF-8"
                )
            );

        return $response;
    }

    /**
     * @return \SimpleXMLElement[]
     */
    private function getSessionId()
    {
        $url = sprintf($this->loginUrl, $this->host);

        if ($this->useSsl) {
            $url = preg_replace("/^http:/", "https:", $url);
        }

        $this->curl->init($url);
        $this->curl->setOpt(CURLOPT_RETURNTRANSFER, 1);

        if ($this->useSsl && !$this->checkCert) {
            $this->curl->setOpt(CURLOPT_SSL_VERIFYHOST, 0);
            $this->curl->setOpt(CURLOPT_SSL_VERIFYPEER, 0);
        }

        $resp = $this->curl->exec();
        $sess = simplexml_load_string($resp);

        if ($sess->SID == "0000000000000000") {
            $challenge = $sess->Challenge;
            $response = $this->getChallengeResponse($challenge);
            $this->curl->setOpt(CURLOPT_POSTFIELDS, "username={$this->user}&response={$response}&page=/login_sid.lua");
            $login = $this->curl->exec();
            $sess = simplexml_load_string($login);
        }

        $this->sid = $sess->SID;

        return $this->sid;
    }

    /**
     * Set session id
     *
     * @return mixed
     */
    public function getSid()
    {
        return $this->sid;
    }

    /**
     * Set session id
     *
     * @param $sid
     */
    public function setSid($sid)
    {
        $this->sid = $sid;
    }

    /**
     * @param $cmd
     * @param string $ain
     * @param string $param
     * @return bool|string
     */
    private function sendCommand($cmd, $ain = "", $param = "")
    {
        if ($this->sid && $this->sid != "0000000000000000") {
            $url = sprintf($this->ahaUrl, $this->host, $cmd, $this->sid, $ain, $param);

            if ($this->useSsl) {
                $url = preg_replace("/^http:/", "https:", $url);
            }

            if ($ain) {
                $url .= sprintf("&ain=%s", $ain);
            }

            if ($param) {
                $url .= sprintf("&param=%d", $param);
            }

            $this->curl->init($url);
            $this->curl->setOpt(CURLOPT_RETURNTRANSFER, 1);

            if ($this->useSsl && !$this->checkCert) {
                $this->curl->setOpt(CURLOPT_SSL_VERIFYHOST, 0);
                $this->curl->setOpt(CURLOPT_SSL_VERIFYPEER, 0);
            }

            $resp = $this->curl->exec();

            if (!is_bool($resp)) {
                $resp = trim($resp);
            }

            return $resp;
        }

        return false;
    }

    /**
     * Returns information for all known devices
     *
     * @return bool|\SimpleXMLElement
     */
    public function getDeviceList()
    {
        $resp = $this->sendCommand("getdevicelistinfos");

        if ($resp) {
            return simplexml_load_string($resp);
        }

        return false;
    }

    /**
     * Gets current temperature for device or group
     *
     * @param $ain
     * @return float|int
     */
    public function getTemperature($ain)
    {
        return $this->sendCommand("gettemperature", $ain) / 10;
    }

    /**
     * @param $ain
     * @param $type
     * @return float|int|string
     */
    private function getTemperatureHkr($ain, $type)
    {
        $temp = $this->sendCommand($type, $ain);

        if ($temp == 254) {
            return "on";
        }

        if ($temp == 253) {
            return "off";
        }

        return $temp / 2;
    }

    /**
     * Gets aimed temperature for device or group
     *
     * @param $ain
     * @return float|int|string
     */
    public function getTemperatureSoll($ain)
    {
        return $this->getTemperatureHkr($ain, "gethkrtsoll");
    }

    /**
     * Gets temperature for comfort-heating interval
     *
     * @param $ain
     * @return float|int|string
     */
    public function getTemperatureComfort($ain)
    {
        return $this->getTemperatureHkr($ain, "gethkrkomfort");
    }

    /**
     * Gets temperature for non-heating interval
     *
     * @param $ain
     * @return float|int|string
     */
    public function getTemperatureLow($ain)
    {
        return $this->getTemperatureHkr($ain, "gethkrabsenk");
    }

    /**
     * Sets temperature for device or group
     *
     * @param $ain
     * @param $temp
     * @return bool|string
     */
    public function setTemperature($ain, $temp)
    {
        if ($temp >= 8 && $temp <= 28) {
            $param = floor($temp * 2);
            return $this->sendCommand("sethkrtsoll", $ain, $param);
        }

        if ($temp == 253 || $temp == 254) {
            return $this->sendCommand("sethkrtsoll", $ain, $temp);
        }

        return false;
    }

    /**
     * Turns heating on for device or group
     *
     * @param $ain
     * @return bool|string
     */
    public function setHeatingOn($ain)
    {
        return $this->setTemperature($ain, 254);
    }

    /**
     * Turns heating off for device or group
     *
     * @param $ain
     * @return bool|string
     */
    public function setHeatingOff($ain)
    {
        return $this->setTemperature($ain, 253);
    }

    /**
     * Returns all known devices
     *
     * @return array
     */
    public function getAllDevices()
    {
        $devices = $this->getDeviceList();
        $ret = [];

        foreach ($devices->device as $device) {
            $ret[] = [
                "name" => (string)$device->name,
                "aid" => (string)$device["identifier"],
                "type" => (string)$device["functionbitmask"],
            ];
        }

        return $ret;
    }

    /**
     * Returns all known device groups
     *
     * @return \SimpleXMLElement[]
     */
    public function getAllGroups()
    {
        $devices = $this->getDeviceList();
        return $devices->group;
    }

    /**
     * Returns AIN/MAC of all known switches
     *
     * @return array
     */
    public function getAllSwitches()
    {
        $switches = $this->sendCommand("getswitchlist");
        return explode(",", $switches);
    }

    /**
     * Turn switch on
     *
     * @param $ain
     * @return bool|string
     */
    public function setSwitchOn($ain)
    {
        return $this->sendCommand("setswitchon", $ain);
    }

    /**
     * Turn switch off
     *
     * @param $ain
     * @return bool|string
     */
    public function setSwitchOff($ain)
    {
        return $this->sendCommand("setswitchoff", $ain);
    }

    /**
     * Toggle switch state
     *
     * @param $ain
     * @return bool|string
     */
    public function setSwitchToggle($ain)
    {
        return $this->sendCommand("setswitchtoggle", $ain);
    }

    /**
     * Get power state of switch
     *
     * @param $ain
     * @return 0|1|inval
     */
    public function getSwitchState($ain)
    {
        return $this->sendCommand("getswitchstate", $ain);
    }

    /**
     * Is the switch connected
     *
     * @param $ain
     * @return bool
     */
    public function isSwitchPresent($ain)
    {
        return (bool)$this->sendCommand("getswitchpresent", $ain);
    }

    /**
     * Get current power consumption in mW
     *
     * @param $ain
     * @return float|inval
     */
    public function getSwitchPower($ain)
    {
        return $this->sendCommand("getswitchpower", $ain);
    }

    /**
     * Get total power consumption
     * since last reset in Wh
     *
     * @param $ain
     * @return float|inval
     */
    public function getSwitchEnergy($ain)
    {
        return $this->sendCommand("getswitchenergy", $ain);
    }

    /**
     * Get switch name
     *
     * @param $ain
     * @return bool|string
     */
    public function getSwitchName($ain)
    {
        return $this->sendCommand("getswitchname", $ain);
    }
}
