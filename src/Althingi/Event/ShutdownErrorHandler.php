<?php
/**
 * Created by PhpStorm.
 * User: einarvalur
 * Date: 6/06/15
 * Time: 11:23 AM
 */

namespace Althingi\Event;

class ShutdownErrorHandler
{
    public function __invoke()
    {
        //http_response_code(500);
        //header('Content-type: application/json');
        if ($error = error_get_last()) {
            echo json_encode(error_get_last());
            exit;
        }
    }
}
