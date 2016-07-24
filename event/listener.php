<?php namespace scfr\WPHPBB\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class listener implements EventSubscriberInterface
{
  /** @var \phpbb\template\template */
  protected $template;
  /** @var \phpbb\user */
  protected $user;
  /** @param \phpbb\db\driver\driver_interface */
  protected $db;
  protected $wordpress;
  private $topic_set_up;
  /** @param \SCFR\main\controller\Topic */
  private $topic;
  private $creating_user;

  /**
  * Constructor
  *
  * @param \phpbb\template\template             $template          Template object
  * @param \phpbb\user   $user             User object
  * @param \phpbb\db\driver\driver_interface   $db             Database object
  * @access public
  */
  public function __construct( \phpbb\template\template $template, \phpbb\user $user, \phpbb\db\driver\driver_interface $db, $wordpress) {
    $this->template = $template;
    $this->user = $user;
    $this->db = $db;
    $this->wordpress = $wordpress;
  }

  static public function getSubscribedEvents()
  {
    return array(
      'core.session_create_after'                  => 'session_create_after',
      'core.session_kill_after'                    => 'session_kill_after',
      'core.user_add_after'                        => "user_register_after",
      'core.permissions'                           => 'permissions',
      'core.index_modify_page_title'               => 'try_debug',
      'core.ucp_profile_reg_details_sql_ary'       => 'ucp_profile_data',
      'core.user_add_after'                        => 'user_creating_after',
      'core.ucp_register_data_after'               => 'user_creating_password',
    );
  }

  public function try_debug($event) {

  }

  // When adding a phpbb_user we need to save its password for later use.
  // When creating the corresponding wp_user
  public function user_creating_password($event) {
    if($event["submit"]) {
      if($event["data"]["new_password"] != '' && $event["data"]["new_password"] === $event["data"]["password_confirm"]) {
        $this->set_creating_password($event["data"]["new_password"]);
      }
    }
  }

  private function set_creating_password($password) {
    if(!isset($this->creating_user)) $this->creating_user = new \scfr\WPHPBB\controller\User($this->wordpress);
    $this->creating_user->remember_password($password);
  }

  public function user_creating_after($event) {
    if(isset($this->creating_user) && $this->creating_user->has_saved_password()) {
      $this->creating_user->add_wp_user($event);
    }
    else throw new \Exception("NoPasswordSaved");
  }

  public function session_create_after($event) {
    if($event["session_data"]["session_user_id"] > 1) {
      $user = new \scfr\WPHPBB\controller\User($this->wordpress);
      $user->set_user_id($event["session_data"]["session_user_id"]);
      $user->do_wp_login($event["session_data"]["session_autologin"]);
    }
  }

  public function ucp_profile_data($event) {
    $user = new \scfr\WPHPBB\controller\User($this->wordpress);
    $user->set_user_id($this->user->data["user_id"]);
    $user->handle_ucp_profile_data_change($event["data"]);
  }

  public function session_kill_after($event) {
    $user = new \scfr\WPHPBB\controller\User($this->wordpress);
    $user->do_wp_logout();
  }

  public function user_register_after($event) {

  }

  public function permissions($event) {
    $temp = $event["permissions"];

    $categories = array_merge($event['categories'], array(
      'wphpbb'  => "ACL_WPHPBB",
    ));

    $temp["f_wphpbb_cross_post"] = array('lang' => 'ACL_F_WPHPBB_CROSSPOST', 'cat' => 'wphpbb');

    $event["permissions"] = $temp;
    $event["categories"] = $categories;

  }



}
?>
