<?php

include_once('includes/private-api-header.php');

include_once('../../api/get-cards.php');

$output = get_cards($db);

echo $m->render('cards', $output);