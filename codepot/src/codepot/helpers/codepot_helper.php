<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('base_url_make'))
{
	function base_url_make($path)
	{
		$CI =& get_instance();
		$url = $CI->config->slash_item('base_url');
		if (substr($url, -1) == '/')
		{
			for ($i = 0; substr($path, $i, 1) == '/'; $i++);
			return $url . substr($path, $i);
		}
		else return $url . $path;
	}
}

