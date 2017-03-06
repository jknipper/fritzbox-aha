<?php
declare(strict_types = 1);

namespace JanKnipper\FritzboxAHATest;

use \JanKnipper\FritzboxAHA\FritzboxAHA;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
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

    public function testGetTemperatureSoll()
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
            ->will($this->returnValue("32"));

        $aha = new FritzboxAHA($curl);

        $aha->login("somehost", "someuser", "somepass");

        $this->assertEquals("16", $aha->getTemperatureSoll("xxxxx xxxxxxx"));
    }

    public function testGetTemperatureLow()
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
            ->will($this->returnValue("253"));

        $aha = new FritzboxAHA($curl);

        $aha->login("somehost", "someuser", "somepass");

        $this->assertEquals("off", $aha->getTemperatureLow("xxxxx xxxxxxx"));
    }

    public function testGetTemperatureComfort()
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
            ->will($this->returnValue("254"));

        $aha = new FritzboxAHA($curl);

        $aha->login("somehost", "someuser", "somepass");

        $this->assertEquals("on", $aha->getTemperatureComfort("xxxxx xxxxxxx"));
    }

    public function testGetTemperature()
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
            ->will($this->returnValue("210"));

        $aha = new FritzboxAHA($curl);

        $aha->login("somehost", "someuser", "somepass");

        $this->assertEquals("21", $aha->getTemperature("xxxxx xxxxxxx"));
    }

    public function testGetAllGroups()
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

        $groups = $aha->getAllGroups();
        $groups = json_decode(json_encode($groups), true);

        $this->assertEquals($data[4], $groups);
    }

    public function testGetAllSwitches()
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
            ->will($this->returnValue($data[5]));

        $aha = new FritzboxAHA($curl);

        $aha->login("somehost", "someuser", "somepass");

        $switches = $aha->getAllSwitches();

        $this->assertEquals($data[6], $switches);
    }

    public function testSetSwitchOn()
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
            ->will($this->returnValue("1"));

        $aha = new FritzboxAHA($curl);

        $aha->login("somehost", "someuser", "somepass");

        $switch = $aha->setSwitchOn("xxxxx xxxxxxx");

        $this->assertEquals("1", $switch);
    }

    public function testSetSwitchOff()
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
            ->will($this->returnValue("0"));

        $aha = new FritzboxAHA($curl);

        $aha->login("somehost", "someuser", "somepass");

        $switch = $aha->setSwitchOff("xxxxx xxxxxxx");

        $this->assertEquals("0", $switch);
    }

    public function testSetSwitchToggle()
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
            ->will($this->returnValue("1"));

        $aha = new FritzboxAHA($curl);

        $aha->login("somehost", "someuser", "somepass");

        $switch = $aha->setSwitchToggle("xxxxx xxxxxxx");

        $this->assertEquals("1", $switch);
    }

    public function testGetSwitchState()
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
            ->will($this->returnValue("1"));

        $aha = new FritzboxAHA($curl);

        $aha->login("somehost", "someuser", "somepass");

        $switch = $aha->getSwitchState("xxxxx xxxxxxx");

        $this->assertEquals("1", $switch);
    }

    public function testIsSwitchPresent()
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

        $switch = $aha->isSwitchPresent("xxxxx xxxxxxx");

        $this->assertEquals(true, $switch);
    }

    public function testGetSwitchPower()
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
            ->will($this->returnValue("150"));

        $aha = new FritzboxAHA($curl);

        $aha->login("somehost", "someuser", "somepass");

        $switch = $aha->getSwitchPower("xxxxx xxxxxxx");

        $this->assertEquals("150", $switch);
    }

    public function testGetSwitchEnergy()
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
            ->will($this->returnValue("1500"));

        $aha = new FritzboxAHA($curl);

        $aha->login("somehost", "someuser", "somepass");

        $switch = $aha->getSwitchEnergy("xxxxx xxxxxxx");

        $this->assertEquals("1500", $switch);
    }

    public function testGetSwitchName()
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
            ->will($this->returnValue("Küche"));

        $aha = new FritzboxAHA($curl);

        $aha->login("somehost", "someuser", "somepass");

        $switch = $aha->getSwitchName("xxxxx xxxxxxx");

        $this->assertEquals("Küche", $switch);
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
            $data[4] = [
                '@attributes' => [
                    'identifier' => 'B5:BF:65-900',
                    'id' => '900',
                    'functionbitmask' => '4160',
                    'fwversion' => '1.0',
                    'manufacturer' => 'AVM',
                    'productname' => '',
                ],
                'present' => '1',
                'name' => '3. OG',
                'hkr' => [
                    'tist' => '43',
                    'tsoll' => '32',
                    'absenk' => '32',
                    'komfort' => '41',
                    'lock' => '0',
                    'devicelock' => '0',
                    'errorcode' => '0',
                    'batterylow' => '0',
                    'nextchange' => [
                        'endperiod' => '1486792800',
                        'tchange' => '41',
                    ],
                ],
                'groupinfo' => [
                    'masterdeviceid' => '0',
                    'members' => '16,17',
                ],
            ];
            $data[5] = "1,2,3,4,5\n";
            $data[6] = ["1", "2", "3", "4", "5"];

            $this->data = $data;
        }

        return $this->data;
    }
}
