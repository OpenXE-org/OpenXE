<?php

/*
* OpenXE upgrade check
* Must implement function upgrade_migrate();
* Output via echo_out();
* Return array ('result','message','dump')
*/

function upgrade_check() {

    $result = array(
        'result' => -1,
        'message' => '',
        'dump' => array()
    );

    if (PHP_VERSION_ID < 80300) {
        $result['message'] = 'PHP 8.3 or higher is required.';
        $result['dump'] = $_SERVER;
    } else {
        $result['result'] = 0;
    }

    return($result);
}
