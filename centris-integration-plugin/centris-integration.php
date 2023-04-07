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
  <h1>Centris Integration</h1>
  <form method="post" action="">
    <label for="folder-path">Folder Path:</label>
    <input type="text" id="folder-path" name="folder_path" value="<?php echo get_user_input()["path"] ?>">

    <label for="frequency">Frequency:</label>
    <select id="frequency" name="frequency">
      <option value="daily" <?php if ('daily' == get_user_input()["frequency"]) echo 'selected'; ?>>Daily</option>
      <option value="weekly" <?php if ('weekly' == get_user_input()["frequency"]) echo 'selected'; ?>>Weekly</option>
    </select>

    <input type="submit" name="submit" value="Submit">
  </form>
  <section>
    <h2>Last Updates:</h2>
  </section>
  <?php
}

function save_user_input() {
  if (isset($_POST['frequency'])) {
      $frequency = sanitize_text_field($_POST['frequency']);
      update_option('frequency_option', $frequency);
  }
  if (isset($_POST['folder_path'])) {
    $path = sanitize_text_field($_POST['folder_path']);
    update_option('passerelle_path_option', $path);
  }
}

function get_user_input() {
  $frequency = get_option('frequency_option', '');
  $path = get_option('passerelle_path_option', '');
  return ["frequency" => $frequency, "path" => $path];
}

if (isset($_POST['submit'])) {
  save_user_input();
}
?>