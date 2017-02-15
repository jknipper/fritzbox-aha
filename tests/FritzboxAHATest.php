<?php
declare(strict_types = 1);

namespace JanKnipper\FritzboxAHATest;

use \JanKnipper\FritzboxAHA\FritzboxAHA;

class FritzboxAHATest extends \PHPUnit_Framework_TestCase
{
    protected $data;

    public function testLogin1()
    {
        $data = $this->getData();

        $curl = $this->getMockBuilder("PHPCurl\CurlWrapper\Curl")
            ->setMethods(["exec"])
            ->getMock();

        $curl->expects($this->at(0))
            ->method("exec")
            ->will($this->returnValue($data[0]));

        $curl->expects($this->at(1))
            ->method("exec")
            ->will($this->returnValue($data[1]));

        $aha = new FritzboxAHA($curl);

        $aha->login("somehost", "someuser", "somepass", true, false);

        $this->assertEquals("bbfac33ab1e65841", $aha->getSid());
    }

    public function testLogin2()
    {
        $data = $this->getData();

        $curl = $this->getMockBuilder("PHPCurl\CurlWrapper\Curl")
            ->setMethods(["exec"])
            ->getMock();

        $curl->expects($this->once())
            ->method("exec")
            ->will($this->returnValue($data[1]));

        $aha = new FritzboxAHA($curl);

        $aha->login("somehost", "someuser", "somepass");

        $this->assertEquals("bbfac33ab1e65841", $aha->getSid());
    }

    public function testLogin3()
    {
        $data = $this->getData();

        $curl = $this->getMockBuilder("PHPCurl\CurlWrapper\Curl")
            ->setMethods(["exec"])
            ->getMock();

        $curl->expects($this->exactly(2))
            ->method("exec")
            ->will($this->returnValue($data[0]));

        $aha = new FritzboxAHA($curl);

        $aha->login("somehost", "someuser", "somepass");

        $this->assertEquals("0000000000000000", $aha->getSid());
    }


    public function testSetSid()
    {
        $aha = new FritzboxAHA(new \PHPCurl\CurlWrapper\Curl);
        $aha->setSid("xxxxxxxxxxxxxxxxxx");
        $this->assertEquals("xxxxxxxxxxxxxxxxxx", $aha->getSid());
    }

    public function testSendCommand()
    {
        $aha = new FritzboxAHA(new \PHPCurl\CurlWrapper\Curl);
        $aha->setSid("0000000000000000");
        $this->assertEquals(false, $aha->getDeviceList());
    }

    public function testGetDeviceList()
    {
        $data = $this->getData();

        $curl = $this->getMockBuilder("PHPCurl\CurlWrapper\Curl")
            ->setMethods(["exec"])
            ->getMock();

        $curl->expects($this->at(0))
            ->method("exec")
            ->will($this->returnValue($data[0]));

        $curl->expects($this->at(1))
            ->method("exec")
            ->will($this->returnValue($data[1]));

        $curl->expects($this->at(2))
            ->method("exec")
            ->will($this->returnValue($data[2]));

        $aha = new FritzboxAHA($curl);

        $aha->login("somehost", "someuser", "somepass", true, false);

        $list = $aha->getDeviceList();

        $this->assertEquals(simplexml_load_string($data[2]), $list);
    }

    public function testGetAllDevices()
    {
        $data = $this->getData();

        $curl = $this->getMockBuilder("PHPCurl\CurlWrapper\Curl")
            ->setMethods(["exec"])
            ->getMock();

        $curl->expects($this->at(0))
            ->method("exec")
            ->will($this->returnValue($data[0]));

        $curl->expects($this->at(1))
            ->method("exec")
            ->will($this->returnValue($data[1]));

        $curl->expects($this->at(2))
            ->method("exec")
            ->will($this->returnValue($data[2]));

        $aha = new FritzboxAHA($curl);

        $aha->login("somehost", "someuser", "somepass");

        $devices = $aha->getAllDevices();

        $this->assertEquals($data[3], $devices);
    }

    public function testSetTemperature()
    {
        $data = $this->getData();

        $curl = $this->getMockBuilder("PHPCurl\CurlWrapper\Curl")
            ->setMethods(["exec"])
            ->getMock();

        $curl->expects($this->at(0))
            ->method("exec")
            ->will($this->returnValue($data[0]));

        $curl->expects($this->at(1))
            ->method("exec")
            ->will($this->returnValue($data[1]));

        $curl->expects($this->at(2))
            ->method("exec")
            ->will($this->returnValue(true));

        $aha = new FritzboxAHA($curl);

        $aha->login("somehost", "someuser", "somepass");

        $this->assertEquals(true, $aha->setTemperature("xxxxx xxxxxxx", 18));
        $this->assertEquals(false, $aha->setTemperature("xxxxx xxxxxxx", 3));
    }

    public function testSetHeatingOn()
    {
        $data = $this->getData();

        $curl = $this->getMockBuilder("PHPCurl\CurlWrapper\Curl")
            ->setMethods(["exec"])
            ->getMock();

        $curl->expects($this->at(0))
            ->method("exec")
            ->will($this->returnValue($data[0]));

        $curl->expects($this->at(1))
            ->method("exec")
            ->will($this->returnValue($data[1]));

        $curl->expects($this->at(2))
            ->method("exec")
            ->will($this->returnValue(true));

        $aha = new FritzboxAHA($curl);

        $aha->login("somehost", "someuser", "somepass");

        $this->assertEquals(true, $aha->setHeatingOn("xxxxx xxxxxxx"));
    }

    public function testSetHeatingOff()
    {
        $data = $this->getData();

        $curl = $this->getMockBuilder("PHPCurl\CurlWrapper\Curl")
            ->setMethods(["exec"])
            ->getMock();

        $curl->expects($this->at(0))
            ->method("exec")
            ->will($this->returnValue($data[0]));

        $curl->expects($this->at(1))
            ->method("exec")
            ->will($this->returnValue($data[1]));

        $curl->expects($this->at(2))
            ->method("exec")
            ->will($this->returnValue(true));

        $aha = new FritzboxAHA($curl);

        $aha->login("somehost", "someuser", "somepass");

        $this->assertEquals(true, $aha->setHeatingOff("xxxxx xxxxxxx"));
    }

    public function getData()
    {
        if (!is_array($this->data)) {
            $data[0] = file_get_contents("tests/login1.xml");
            $data[1] = file_get_contents("tests/login2.xml");
            $data[2] = file_get_contents("tests/devices.xml");
            $data[3] = [
                [
                    "name" => "Wohnzimmer",
                    "aid"  => "11959 0378424",
                    "type" => "320",
                ],
                [
                    "name" => "Küche",
                    "aid"  => "11959 0242936",
                    "type" => "320",
                ]
            ];

            $this->data = $data;
        }

        return $this->data;
    }
}
