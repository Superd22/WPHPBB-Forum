<?php namespace scfr\WPHPBB\migrations;

  class release_1_0_0 extends \phpbb\db\migration\migration {
    public function update_data() {
      return array(
        array('permission.add', array('f_wphpbb_cross_post', false)),
      );
    }

    static public function depends_on() {
      return array(
        '\phpbb\db\migration\data\v31x\v314',
      );
    }
  }
?>
