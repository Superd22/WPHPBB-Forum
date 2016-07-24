<?php namespace scfr\WPHPBB\controller;
class User {
  private $id;
  private $wordpress;
  private $wp_user;
  private $password;
  private $made_a_change;

  function __construct(\scfr\WPHPBB\controller\Wordpress &$wordpress) {
    $this->wordpress = $wordpress;
  }

  public function set_user_id($user_id) {
    $this->id = (integer) $user_id;
  }

  private function isset_user_id() {
    return (isset($this->id) && $this->id > 1);
  }

  public function handle_ucp_profile_data_change($data) {
    $this->wordpress->make_wordpress_env();

    if($this->isset_user_id()) {
      if(!$this->wp_user_is_okay()) $this->wp_user = $this->get_wp_user();
      if($this->wp_user_is_okay()) {

        if($this->wp_user->user_email != $data["email"])
        $this->handle_email_change($data["email"]);

        if($data["new_password"] != "" && $data["new_password"] === $data["password_confirm"])
        $this->handle_password_change($data["new_password"]);

        if($data["username"] != "" && $data["username"] !== $this->wp_user->user_login)
        $this->handle_username_change($data["username"]);

        if($this->made_a_change) {
          $this->made_a_change = false;
          $this->do_wp_logout();
          $this->do_wp_login();
        }
      }
    }
  }

  public function has_saved_password() {
    return (isset($this->password) && $this->password != '');
  }

  public function remember_password($password) {
    $this->password = $password;
  }

  // To do :
  // - add an event to handle $event["cp_data"]
  public function add_wp_user($event) {
      $this->wordpress->make_wordpress_env();
      $data = $event["user_row"];

      $new_user = wp_insert_user(array(
        'user_login' => $data["username"],
        'user_pass'  => $this->password,
        'user_email' => $data["user_email"],
        'role'       => 'author',
        'rich_editing' => true,
      ));


      if($new_user > 0) {
        \add_user_meta( $new_user , "_wphpbb_forum_user_id" , $event["user_id"] , true);
        $this->id = $event["user_id"];
        $this->do_wp_login();
      }


  }

  private function handle_password_change($password) {
    if($this->wp_user_is_okay()) wp_set_password($password, $this->wp_user->ID);
    $this->made_a_change = true;
  }


  private function handle_username_change($username) {
    if($this->wp_user_is_okay()) {
      global $wpdb;

      $user_nicename = str_replace(" ", "-", sanitize_user($username));

      $wpdb->update($wpdb->users, array(
        'user_login' => $username,
        'display_name' => $username,
        'user_nicename' => $user_nicename,
      ),
      array(
        'ID' => $this->wp_user->ID,
      )
    );

    $this->made_a_change = true;
  }
}

private function handle_email_change($email) {
  if($this->wp_user_is_okay()) {
    wp_update_user(array(
        "ID"         => $this->wp_user->ID,
        "user_email" => $email,
      )
    );

    $this->made_a_change = true;
  }
}

public function do_wp_login($auto_login = false) {
  $this->wordpress->make_wordpress_env();
  if($this->isset_user_id()) {
    if(!$this->wp_user_is_okay()) $this->wp_user = $this->get_wp_user();
    if($this->wp_user_is_okay()) {
      wp_clear_auth_cookie();
      wp_set_current_user( $this->wp_user->ID );
      wp_set_auth_cookie( $this->wp_user->ID, $auto_login );
    }
  }
}

public function wp_user_is_okay($wp_user = null) {
  if($wp_user === null) $wp_user = $this->wp_user;
  return ( !is_wp_error($wp_user) && isset($wp_user->ID) && ($wp_user->ID > 0) );
}

public function get_wp_user() {
  if($this->isset_user_id()) {
    $user = get_users(array('meta_key' => '_wphpbb_forum_user_id', 'meta_value' => $this->id));
    if(isset($user[0])) return $user[0];
    else return false;
  }
  else return false;
}

public function do_wp_logout() {
  $this->wordpress->make_wordpress_env();
  wp_logout();
}

}
?>
