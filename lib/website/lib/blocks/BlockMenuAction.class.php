<?php
class website_BlockMenuAction extends website_BlockAction
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
		$menu = $this->getDocumentParameter();
		if ($menu === null)
		{
			return website_BlockView::NONE;
		}
		$depth = $this->findParameterValue('depth');
		if ($depth === null || $depth === "")
		{
			$depth = - 1;
		}
		else
		{
			$depth = intval($depth);
		}
		$deployOnlyCurrentPath = $this->findParameterValue('deployonlycurrentpath') == 'true';
		$template = $this->findParameterValue('template');
		
		if (empty($template) || $template == website_BlockView::SUCCESS)
		{
			$template = website_BlockView::SUCCESS;
		}
		
		$page = $this->getPage()->getPersistentPage();
		$website = website_WebsiteModuleService::getInstance()->getCurrentWebsite();
		if ($deployOnlyCurrentPath)
		{
			
			$menuObject = website_WebsiteModuleService::getInstance()->getRestrictedMenu($menu, 
					$depth);
		}
		else
		{
			
			$menuObject = website_WebsiteModuleService::getInstance()->getMenu($menu, $depth);
		}
		$request->setAttribute('menuDocument', $menu);
		$request->setAttribute('menuObject', $menuObject);
		$request->setAttribute('currentPage', $page);
		$request->setAttribute('currentWebsite', $website);
		
		return ucfirst($template);
	}
}
