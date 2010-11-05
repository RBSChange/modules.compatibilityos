<?php

class website_BlockCopyrightsAction extends website_BlockAction
{
	public function getCacheSpecifications()
	{
		return array();
	}

	public function getCacheKeyParameters($request)
	{
		return array();
	}

	/**
	 * @param f_mvc_Request $request
	 * @param f_mvc_Response $response
	 * @return String
	 */
	function execute($request, $response)
	{
		try
		{
			$config = Framework::getConfiguration('modules/website/copyrights');
			$name = $config['name'];
			$url  = $config['url'];
		}
		catch (Exception $e)
		{
			$name = "RBS | Ready Business System";
			$url  = "http://www.rbs.fr";
		}
		$request->setAttribute('name', $name);
		$request->setAttribute('url', $url);
		return website_BlockView::SUCCESS;
	}
}