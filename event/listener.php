<?php namespace scfr\wphpbb\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class listener implements EventSubscriberInterface
{
  /** @var \phpbb\template\template */
  protected $template;
  /** @var \phpbb\user */
  protected $user;
  /** @param \phpbb\db\driver\driver_interface */
  protected $db;
  private $topic_set_up;
  /** @param \SCFR\main\controller\Topic */
  private $topic;

  /**
  * Constructor
  *
  * @param \phpbb\template\template             $template          Template object
  * @param \phpbb\user   $user             User object
  * @param \phpbb\db\driver\driver_interface   $db             Database object
  * @access public
  */
  public function __construct( \phpbb\template\template $template, \phpbb\user $user, \phpbb\db\driver\driver_interface $db) {
    $this->template = $template;
    $this->user = $user;
    $this->db = $db;
  }

  static public function getSubscribedEvents()
  {
    return array(
      'core.session_create_after' => 'session_create_after',
      'core.session_kill_after'   => 'session_kill_after',
      'core.user_add_after'       => "user_register_after",

    );
  }

  public function session_create_after($event) {

  }

  public function session_kill_after($event) {

  }

  public function user_register_after($event) {

  }

  

}
?>
