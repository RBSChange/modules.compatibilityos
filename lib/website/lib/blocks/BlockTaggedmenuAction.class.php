<?php

class website_BlockTaggedmenuAction extends website_BlockAction
{
	/**
	 * @see f_mvc_Action::getCacheDependencies()
	 *
	 * @return array<string>
	 */
	public function getCacheDependencies()
	{
		return array("tags/contextual_website_website_menu*");
	}

	/**
	 * @see website_BlockAction::execute()
	 *
	 * @param website_BlockActionRequest $request
	 * @param website_BlockActionResponse $response
	 * @return String
	 */
	function execute($request, $response)
	{
		$tag = $this->getConfigurationParameter('tag');
		if (empty($tag))
		{
			return null;
		}

		$wsModuleService = website_WebsiteModuleService::getInstance();
		$website = $wsModuleService->getCurrentWebsite();
		$menu = TagService::getInstance()->getDocumentByContextualTag($tag, $website, false);
		if ($menu === null)
		{
			$request->setAttribute('menuObject', false);
		}
		else 
		{
			$depth = $this->getConfigurationParameter('depth');
			if ($depth === null)
			{
				$depth = -1;
			}
			else
			{
				$depth = intval($depth);
			}

			$deployOnlyCurrentPath = $this->getConfigurationParameter('deployonlycurrentpath') == 'true';
			$numberOfCollapseLevel = $this->getConfigurationParameter('collapselevel', 1);

			$page = $this->getPage()->getPersistentPage();
			$request->setAttribute('currentPage', $page);
			$request->setAttribute('currentWebsite', $website);

			if ($deployOnlyCurrentPath)
			{
				$menuObject = $wsModuleService->getRestrictedMenu($menu, $depth, $numberOfCollapseLevel);
			}
			else
			{
				$menuObject = $wsModuleService->getMenu($menu, $depth);
			}
			$request->setAttribute('rootElement', $menu);
			$request->setAttribute('menuObject', $menuObject);
			$request->setAttribute('masterUlClass', $this->getConfigurationParameter('class'));
			$request->setAttribute('masterUlId', $this->getConfigurationParameter('id'));
			$request->setAttribute('separator', $this->getConfigurationParameter('separator'));
		}

		$template = $this->getConfigurationParameter('template');
		if (empty($template))
		{
			return $this->getTemplateByFullName("modules_website", "Website-Menu");
		}
		return ucfirst($template);
	}
}
