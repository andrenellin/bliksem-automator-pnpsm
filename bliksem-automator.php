<?php
/**
 * Plugin Name: Bliksem Automator
 * Version: 1.0.0
 * Plugin URI: https://github.com/andrenellin/bliksem-automator
 * Description: Adds functionality to Automator plugin
 * Author: Andre Nell
 * Author URI: http://www.andrenell.me/
 *
 *
 * @package WordPress
 * @author Andre Nell
 * @since 1.0.0
 */

/*
 * Includes
 * =========
 *
 */

include_once dirname(__FILE__) . '/includes/mp-recurringsubscriptionpaused.php';
include_once dirname(__FILE__) . '/includes/mp-recurringsubscriptionresumed.php';

// function bliksem_custom_logs($message)
// {
//     if (is_array($message)) {
//         $message = json_encode($message);
//     }
//     $file = fopen("../custom_logs.log", "a");
//     echo fwrite($file, "\n" . date('Y-m-d h:i:s') . " :: " . $message);
//     fclose($file);
// }
