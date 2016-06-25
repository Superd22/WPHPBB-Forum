<?php namespace scfr\wphpbb\controller;
class API {
    public function __construct($user, $db, $helper) {
      $this->user = $user;
      $this->db = $db;
      $this->helper = $helper;
    }

    public function get_cross_postable_forums() {
      return $this->helper->message('test');
    }
}
?>
