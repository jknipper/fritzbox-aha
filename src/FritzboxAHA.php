<?php
declare(strict_types = 1);

namespace JanKnipper\FritzboxAHA;

use \PHPCurl\CurlWrapper\CurlInterface;

/**
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

    public function __construct(
        CurlInterface $curl
    ) {
        $this->curl = $curl;
    }

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

    public function getSid()
    {
        return $this->sid;
    }

    public function setSid($sid)
    {
        $this->sid = $sid;
    }

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

            return $resp;
        }

        return false;
    }

    public function getDeviceList()
    {
        $resp = $this->sendCommand("getdevicelistinfos");

        if ($resp) {
            return simplexml_load_string($resp);
        }

        return false;
    }

    public function getTemperature($ain)
    {
        return $this->sendCommand("gettemperature", $ain) / 10;
    }

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

    public function getTemperatureSoll($ain)
    {
        return $this->getTemperatureHkr($ain, "gethkrkomfort");
    }

    public function getTemperatureComfort($ain)
    {
        return $this->getTemperatureHkr($ain, "gethkrkomfort");
    }

    public function getTemperatureLow($ain)
    {
        return $this->getTemperatureHkr($ain, "gethkrabsenk");
    }

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

    public function setHeatingOn($ain)
    {
        return $this->setTemperature($ain, 254);
    }

    public function setHeatingOff($ain)
    {
        return $this->setTemperature($ain, 253);
    }

    public function getAllDevices()
    {
        $devices = $this->getDeviceList();
        return $devices->device;
    }

    public function getAllGroups()
    {
        $devices = $this->getDeviceList();
        return $devices->group;
    }
}
