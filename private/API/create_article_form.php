<?php

include_once('includes/private-api-header.php');

if(!isset($_POST['card_id']))
  exit("unsupported card ID");

$id = $_POST['card_id'];

if(!preg_match('/^[a-zA-Z0-9]{1,32}$/', $id))
  exit("unsupported card ID format");

header ("HX-Trigger-After-Settle: articleLoaded");

echo $m->render('createArticle', ["card_id"=>$id]);