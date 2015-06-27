<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 8/06/15
 * Time: 10:46 AM
 */

namespace Althingi\Lib\Http;

use PHPUnit_Framework_TestCase;
use Zend\Http\Header\ContentType;
use Zend\Http\PhpEnvironment\Request;

class MultiPartTest extends PHPUnit_Framework_TestCase
{
    public function testSimpleBody()
    {
        $contentType = $this->buildContentType();

        $request = new Request();
        $request->setContent(
            "------WebKitFormBoundaryZMTC4uNAHMo4lWMs\r\n".
            "Content-Disposition: form-data; name=\"from\"\r\n\r\n2016-01-01\r\n\r".
            "------WebKitFormBoundaryZMTC4uNAHMo4lWMs--"
        );
        $request->getHeaders()->addHeader($contentType);

        $response = (new MultiPart())->parse($request);

        $this->assertInternalType('array', $response);
        $this->assertArrayHasKey('from', $response);
        $this->assertEquals('2016-01-01', $response['from']);
    }

    public function testLessSimpleBody()
    {
        $contentType = $this->buildContentType();

        $request = new Request();
        $request->setContent(
            "------WebKitFormBoundaryZMTC4uNAHMo4lWMs\r\n".
            "Content-Disposition: form-data; name=\"to\"\r\n\r\n2016-01-01\r\n".
            "------WebKitFormBoundaryZMTC4uNAHMo4lWMs\r\n".
            "Content-Disposition: form-data; name=\"from\"\r\n\r\n2016-01-01\r\n\r".
            "------WebKitFormBoundaryZMTC4uNAHMo4lWMs--"
        );
        $request->getHeaders()->addHeader($contentType);

        $response = (new MultiPart())->parse($request);

        $this->assertInternalType('array', $response);
        $this->assertArrayHasKey('from', $response);
        $this->assertArrayHasKey('to', $response);
        $this->assertEquals('2016-01-01', $response['from']);
        $this->assertEquals('2016-01-01', $response['to']);
    }

    public function testNonMultiPart()
    {
        $request = new Request();
        $request->setContent(http_build_query(['from' => '2010-01-01']));

        $response = (new MultiPart())->parse($request);
        $this->assertInternalType('array', $response);
        $this->assertEquals('2010-01-01', $response['from']);
    }

    private function buildContentType()
    {
        $contentType = new ContentType();
        $contentType->setMediaType('multipart/form-data');
        $contentType->setParameters([
            'boundary' => '----WebKitFormBoundaryZMTC4uNAHMo4lWMs'
        ]);
        return $contentType;
    }
}
