<?php namespace scfr\wphpbb\controller;

class CrossPatcher {

  private $wp_root_path;
  protected $toExec;

  function __construct() {
    $this->wp_root_path = "C:\Users\david\OneDrive\Documents\GitHub\SCFR\\";
    define('ABSPATH', $this->wp_root_path);
  }

  public function can_connect_to_wp() {
    return @file_exists($this->wp_root_path . 'wp-settings.php');
  }

  private function prepare($code) {
    $this->toExec .= $this->strip_php_tags($code)."\n";
  }

  private function strip_php_tags($code) {
    if(strpos($code, "<?php") === 0)
      $code = substr_replace($code, "", 0, strlen("<?php"));

    return trim($code);
  }

  public function exec() {
    $code = $this->toExec;
    $this->toExec = "";

    return $code;
  }

  public function make_wp_compatible() {
    if($this->can_connect_to_wp()) {
      $wp_conf = file_get_contents($this->wp_root_path . 'wp-config.php');
      $wp_settings = file_get_contents($this->wp_root_path . 'wp-settings.php');

      if(function_exists("make_clickable"))
        $this->handle_make_clickable($wp_settings);
      if(function_exists("validate_username"))
        $this->handle_validate_username($wp_settings);

      $this->unrequire_settings($wp_conf);

      $this->prepare($wp_conf);
      $this->prepare($wp_settings);
    }
  }

  private function unrequire_settings(&$wp_conf) {
    $finds = array(
      "require_once(ABSPATH . 'wp-settings.php');",
      "require_once( ABSPATH . 'wp-settings.php');"
    );

    $wp_conf = str_replace($finds, "", $wp_conf);
  }

  private function handle_make_clickable(&$wp_settings) {
    $wp_formatting = file_get_contents($this->wp_root_path . 'wp-includes/formatting.php');

    $wp_formatting = '?'.'>'.trim(str_replace('function make_clickable', 'function wp_make_clickable', $wp_formatting));
    $replace = array(
      'require (ABSPATH . WPINC . ' . "'/formatting.php');",
      'require( ABSPATH . WPINC . ' . "'/formatting.php' );"
    );

    $wp_settings = str_replace($replace, $wp_formatting, $wp_settings);
  }

  private function handle_validate_username(&$wp_settings) {
    $wp_user = file_get_contents($this->wp_root_path . 'wp-includes/user.php');

    $wp_user = '?'.'>'.trim(str_replace("function validate_username", "function wp_validate_username", $wp_user));
    $replace = array(
      'require (ABSPATH . WPINC . ' . "'/user.php');",
      'require( ABSPATH . WPINC . ' . "'/user.php' );"
    );

    $wp_settings = str_replace($replace, $wp_user, $wp_settings);
  }


}

?>
