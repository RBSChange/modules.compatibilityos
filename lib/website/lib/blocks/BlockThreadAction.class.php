<?php
class website_BlockThreadOldAction extends website_BlockAction
{
	/**
	 * @see f_mvc_Action::getCacheDependencies()
	 *
	 * @return array<string>
	 */
	public function getCacheDependencies()
	{
		return array("modules_website/menu",
		 "modules_website/menu",
		 "modules_website/menuitem",
		 "modules_website/menuitemdocument",
		 "modules_website/menuitemfunction",
		 "modules_website/menuitemtext",
		 "modules_website/page",
		 "modules_website/pagegroup",
		 "modules_website/pageexternal",
		 "modules_website/pagereference",
		 "modules_website/pageversion",
		 "modules_website/topic",
		 "modules_website/website");
	}

	/**
	 * @param website_BlockActionRequest $request
	 * @return array<mixed>
	 */
	public function getCacheKeyParameters($request)
	{
		return array("context->id" => $this->getPage()->getId(),
			"lang->id" => RequestContext::getInstance()->getLang());
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
		$page = $this->getPage()->getPersistentPage();		
		$ws = website_WebsiteModuleService::getInstance();
		$currentWebsite = $ws->getCurrentWebsite();

		if ($currentWebsite && $currentWebsite->getIndexPage() && $page)
		{
			if ($currentWebsite->getIndexPage()->getId() == $page->getId())
			{
				$includeCurrentDocument = false;
			}
			else
			{
				$includeCurrentDocument = true;
			}
			$breadcrumb = $ws->getBreadcrumb($page, $includeCurrentDocument);
		}
		else
		{
			$breadcrumb = null;
		}

		if ($this->isInBackoffice() && !$breadcrumb)
		{
			$request->setAttribute('emptyBreadcrumb', true);
		}
		else
		{
			if (count($breadcrumb) > 0)
			{
				$homeMenuItem = $breadcrumb[0];
				$this->getPage()->addLink("home", "text/html", $homeMenuItem->getUrl(), $homeMenuItem->getLabel());
			}
			$request->setAttribute('breadcrumb', $breadcrumb);
		}
		return website_BlockView::SUCCESS;
	}
}