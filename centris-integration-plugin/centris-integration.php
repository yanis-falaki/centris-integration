<?php
/*
Plugin Name: Centris Integration
Description: This plugin is used to integrate listing from centris to wordpress using Passerelle.
Version: 1.0
Author: Yanis Falaki
Author URI: https://webaradigital.com
*/

function cenris_integration_add_menu_page() {
  add_menu_page(
    'Centris Integration',
    'Centis Integration',
    'manage_options',
    'centris-integration',
    'centris_integration_display_settings_page',
    'dashicons-admin-plugins',
    5
  );
}

add_action('admin_menu', 'cenris_integration_add_menu_page');

function centris_integration_display_settings_page() {
  ?>
  <h1>Centris Integration Settings</h1>
  

  <?php
}
?>