<?php
declare(strict_types = 1);

namespace JanKnipper\FritzboxAHATest;

use \JanKnipper\FritzboxAHA\FritzboxAHA;

class FritzboxAHATest extends \PHPUnit_Framework_TestCase
{
    public function testLogin1()
    {
        $data = $this->getXmlData();

        $curl1 = $this->getMockBuilder("PHPCurl\CurlWrapper\Curl")
            ->setMethods(["exec"])
            ->getMock();

        $curl2 = $this->getMockBuilder("PHPCurl\CurlWrapper\Curl")
            ->setMethods(["exec"])
            ->getMock();

        $curl3 = $this->getMockBuilder("PHPCurl\CurlWrapper\Curl")
            ->setMethods(["exec"])
            ->getMock();

        // Login
        $curl1->expects($this->at(0))
            ->method("exec")
            ->will($this->returnValue($data[0]));

        $curl1->expects($this->at(1))
            ->method("exec")
            ->will($this->returnValue($data[1]));

        $aha1 = new FritzboxAHA($curl1);

        $aha1->login("somehost", "someuser", "somepass", true, false);

        $this->assertEquals("bbfac33ab1e65841", $aha1->getSid());

        // Already logged in
        $curl2->expects($this->once())
            ->method("exec")
            ->will($this->returnValue($data[1]));

        $aha2 = new FritzboxAHA($curl2);

        $aha2->login("somehost", "someuser", "somepass");

        $this->assertEquals("bbfac33ab1e65841", $aha2->getSid());

        // Login failed
        $curl3->expects($this->exactly(2))
            ->method("exec")
            ->will($this->returnValue($data[0]));

        $aha3 = new FritzboxAHA($curl3);

        $aha3->login("somehost", "someuser", "somepass");

        $this->assertEquals("0000000000000000", $aha3->getSid());
    }


    public function testSetSid()
    {
        $aha = new FritzboxAHA(new \PHPCurl\CurlWrapper\Curl);
        $aha->setSid("xxxxxxxxxxxxxxxxxx");
        $this->assertEquals("xxxxxxxxxxxxxxxxxx", $aha->getSid());
    }

    public function testGetDeviceList()
    {
        $data = $this->getXmlData()[2];

        $curl = $this->getMockBuilder("PHPCurl\CurlWrapper\Curl")
            ->setMethods(["exec"])
            ->getMock();

        $curl->expects($this->once())
            ->method("exec")
            ->will($this->returnValue($data));

        $aha = new FritzboxAHA($curl);

        $aha->setSid("xxxxxxxxxxxxxxxxxx");

        $list = $aha->getDeviceList();

        $this->assertEquals(simplexml_load_string($data), $list);
    }

    public function getXmlData()
    {
        $data[0] = file_get_contents("tests/login1.xml");
        $data[1] = file_get_contents("tests/login2.xml");
        $data[2] = file_get_contents("tests/devices.xml");

        return $data;
    }
}
