<?php

$current_mailout_file = './current_mailout.txt';
$current_mailout = file_get_contents($current_mailout_file);
if ($current_mailout == '') exit();

// current mailout it set, carry on

// paths to email data
$content_path = "../assets/content/teo/";
$remove_path = '/email_management/unsubscribe.php';
$subject_id = "[THE EXACT OPPOSITE]";
$mailing_list_table = $current_mailout == "test" ? "test_mailing_list" : "mailing_list";
$log_dir =  $current_mailout == "test" ? './logs/test/' : './logs/teo/';

// email variables
$from_name = "The Exact Opposite mailing list";

/* *** INCLUDES *** */

// cd /home/thesadso/theexactopposite.uk/private/mailout/api/; /usr/local/bin/php -q mailout.php(?dd=true)
require_once('includes/mailout_includes.php');
require_once('includes/do_mailout.php');