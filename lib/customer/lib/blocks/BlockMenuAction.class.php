<?php
/**
 * @deprecated
 */
class customer_BlockMenuAction extends website_BlockAction
{
	/**
	 * @deprecated
	 */
	function execute($request, $response)
	{
		if ($this->isInBackofficeEdition())
		{
			return website_BlockView::NONE;
		}
		
		$ws = website_WebsiteModuleService::getInstance();
		try
		{
			$menuaccount = $ws->getMenuByTag('modules_customer_menu-account', 1);
			$request->setAttribute('menuaccount', $menuaccount);
			$request->setAttribute('displayMenuaccount', count($menuaccount) > 0);
		}
		catch (TopicException $e)
		{
			Framework::exception($e);
		}

		try
		{
			$menuorders = $ws->getMenuByTag('modules_customer_menu-orders', 1);
			$request->setAttribute('menuorders', $menuorders);
			$request->setAttribute('displayMenuorders', count($menuorders) > 0);
		}
		catch (TopicException $e)
		{
			Framework::exception($e);
		}
		
		try
		{
			$menuservices = $ws->getMenuByTag('modules_customer_menu-services', 1);
			$request->setAttribute('menuservices', $menuservices);
			$request->setAttribute('displayMenuservices', count($menuservices) > 0);
		}
		catch (TopicException $e)
		{
			Framework::exception($e);
		}
		return website_BlockView::SUCCESS;
	}
}