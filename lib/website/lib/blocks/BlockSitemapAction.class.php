<?php
class website_BlockSitemapAction extends website_BlockAction
{
	/**
	 * @see website_BlockAction::execute()
	 *
	 * @param website_BlockActionRequest $request
	 * @param website_BlockActionResponse $response
	 * @return String
	 */
	function execute($request, $response)
	{
        $ws = website_WebsiteModuleService::getInstance();
        $siteMap = $ws->getSitemap($ws->getCurrentWebsite());
        $request->setAttribute('sitemap', $siteMap);
        $column = $this->getConfiguration()->getColumn();        
        if ($column)
        {
        	return website_BlockView::SUCCESS . "Column";
        }
		return website_BlockView::SUCCESS;
	}
}