<?php

$current_mailout_file = './dd_current_mailout.txt';
$current_mailout = file_get_contents($current_mailout_file);
if ($current_mailout == '') exit();

// current mailout it set, carry on

// paths to email data
$content_path = "../assets/content/dd/";
$remove_path = '/email_management/dd_unsubscribe.php';
$subject_id = "[DIVE DIVE]";
$mailing_list_table = $current_mailout == "test" ? "test_mailing_list" : "dd_mailing_list";
$log_dir =  $current_mailout == "test" ? './logs/test/' : './logs/dd/';

// email variables
$from_name = "Dive Dive mailing list";

/* *** INCLUDES *** */

// cd /home/thesadso/theexactopposite.uk/private/mailout/API/; /usr/local/bin/php -q mailout.php(?dd=true)
require_once('includes/mailout_includes.php');
require_once('includes/do_mailout.php');