<?php namespace scfr\wphpbb\controller;
class Wordpress {

  private $cross_patcher;

  function __construct($crossPatcher) {
    $this->cross_patcher = $crossPatcher;
  }

  public function make_wordpress_env() {
    global $request;

    if(!defined("WPINC")) {
      $request->enable_super_globals();

      $this->cross_patcher->make_wp_compatible();
      $code = $this->cross_patcher->exec();

      eval($code);
    }
  }


}
?>
