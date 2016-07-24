<?php namespace scfr\WPHPBB\controller;
class permission {
  public static function add_perms(&$permissions) {
    $temp = $permissions;

    $temp["f_wphpbb_crosspost"] = array('lang' => 'ACL_F_WPHPBB_CROSSPOST', 'cat' => 'polls');

    return $temp;
  }
}
 ?>
