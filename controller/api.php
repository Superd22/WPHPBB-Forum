<?php namespace scfr\wphpbb\controller;
class API {
    public function __construct($user, $db, $helper, $template) {
      $this->user = $user;
      $this->db = $db;
      $this->helper = $helper;
      $this->template = $template;
    }

    public function get_cross_postable_forums() {
      //WPHPBB_JSON_MESSAGE
      return $this->helper->render(null);
    }
}
?>
