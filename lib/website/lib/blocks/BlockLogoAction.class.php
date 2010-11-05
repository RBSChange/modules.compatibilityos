<?php
/**
 * website_BlockIframeAction
 * @package modules.website.lib.blocks
 */
class website_BlockLogoAction extends website_BlockAction
{
	/**
	 * @see website_BlockAction::execute()
	 *
	 * @param f_mvc_Request $request
	 * @param f_mvc_Response $response
	 * @return String
	 */
	function execute($request, $response)
	{
		$request->setAttribute('home', website_WebsiteModuleService::getInstance()->getCurrentWebsite()->getIndexPage());
		return website_BlockView::SUCCESS;
	}
}