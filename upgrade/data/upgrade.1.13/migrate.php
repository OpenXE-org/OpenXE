<?php

/*
* OpenXE upgrade migration
* Must implement function upgrade_migrate();
* Output via echo_out();
* Return array ('result','message','dump')
*/

function upgrade_migrate() {

    $result = array(
        'result' => -1,
        'message' => '',
        'dump' => array()
    );

    // Dummy for test
    for ($i = 0;$i < 5;$i++) {
        echo_out("Migrating step ".($i+1)." of 5\n");
        sleep(1);
    }

    $result['result'] = 0;

    return($result);
}
