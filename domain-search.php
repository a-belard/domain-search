<?php

/**
 * @pacjage DomainSearch
 */
/* 
Plugin Name: Domain Search
Plugin URI: akadomo.rw
Description: Domain Search Plugin
Version: 1.0.0
Author: Belix
Author URI: https://esicia.com
License: GPLv2 or later
Text Domain: domain-search
*/
/*
  This file is part of the Esicia Kpay plugin for WordPress

  (c) Esicia

  For the full copyright and license information,
  please view the LICENSE file that was distributed with this source code.
*/

defined('ABSPATH') or die('Hey, you can\t access this file!');

if (!function_exists('add_action')) {
  echo 'Hey, you can\t access this file!';
  exit;
}

class DomainSearch
{
  public $plugin;

  function __construct()
  {

    $this->plugin = plugin_basename(__FILE__);

    // load assets (js, css)
    add_action('admin_enqueue_scripts', array($this, 'load_assets'));

    // add shortcode
    add_shortcode('domain-search', array($this, 'load_form'));
  }

  function activate()
  {
    // generate a CPT
    // flush rewrite rules
  }

  function deactivate()
  {
    // flush rewrite rules

  }

  function uninstall()
  {
    // delete CPT
    // delete all the plugin data from the DB

  }


  function load_assets()
  {
    wp_enqueue_style(
      'kpay-plugin',
      plugin_dir_url(__FILE__) . 'css/kpay.css',
      array(),
      '1',
      'all'
    );

    wp_enqueue_script(
      'kpay-plugin',
      plugin_dir_url(__FILE__) . 'js/kpay.js',
      array('jquery'),
      '1',
      true
    );
  }

  function load_form()
  {
    ob_start();
    echo '<link rel="stylesheet" href="' . plugin_dir_url(__FILE__) . 'css/domain_search.css">';
    require_once('domain-search-form.php');
    echo '<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>';
    return ob_get_clean();
  }
}

if (class_exists("DomainSearch")) {
  $domainSearch = new DomainSearch();
}

// Activation
register_activation_hook(__FILE__, array($domainSearch, 'activate'));

// Deactivation
register_deactivation_hook(__FILE__, array($domainSearch, 'deactivate'));

// Uninstall
// register_uninstall_hook(__FILE__, array($kpayPlugin, 'uninstall'));
