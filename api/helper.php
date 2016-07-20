<?php namespace scfr\wphpbb\api;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;

class helper extends \phpbb\controller\helper {
  public function __construct(\phpbb\template\template $template, \phpbb\user $user, \phpbb\config\config $config, \phpbb\controller\provider $provider, \phpbb\extension\manager $manager, \phpbb\symfony_request $symfony_request, \phpbb\request\request_interface $request, \phpbb\filesystem $filesystem, $phpbb_root_path, $php_ext) {
    parent::__construct($template, $user, $config, $provider, $manager, $symfony_request, $request, $filesystem, $phpbb_root_path, $php_ext);
  }


  	/**
  	* Automate setting up the page and creating the response object.
  	* @param int $status_code The status code to be sent to the page header
  	* @return Response object containing rendered page
  	*/
  	public function render($_message, $status_code = 200)	{

      $this->template->assign_var('WPHPBB_JSON_MESSAGE', json_encode($_message));

  		$this->template->set_filenames(array(
  			'body'	=> "wphpbb_json_api.html",
  		));


  		$headers = array(
        'Content-Type' => 'application/json'
      );

  		return new Response($this->template->assign_display('body'), $status_code, $headers);
  	}

}
?>
