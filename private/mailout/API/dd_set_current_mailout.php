<?php

try {
    $fp = fopen('dd_current_mailout.txt', 'w');
    fwrite($fp, $_GET['mailout']);
    fclose($fp);
}
catch (Exception $e) {
    echo "ERROR";
    exit();
}

echo "SUCCESS";

?>