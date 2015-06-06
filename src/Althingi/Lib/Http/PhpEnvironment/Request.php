<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 28/05/15
 * Time: 6:07 AM
 */

namespace Althingi\Lib\Http\PhpEnvironment;

use Zend\Http\PhpEnvironment\Request as BaseRequest;

class Request extends BaseRequest
{
    const METHOD_LINK = 'LINK';
    const METHOD_UNLINK = 'UNLINK';
}
