<?php
class website_BlockMainAction extends block_BlockAction
{
	public function getCacheSpecifications()
	{
		return array("modules_website/menu",
		 "modules_website/menu",
		 "modules_website/menuitem",
		 "modules_website/menuitemdocument",
		 "modules_website/menuitemfunction",
		 "modules_website/menuitemtext",
		 "modules_website/page",
		 "modules_website/pageexternal",
		 "modules_website/pagereference",
		 "modules_website/pageversion",
		 "modules_website/topic",
		 "modules_website/website",
		 "tags/contextual_website_website_menu-main");
	}

	public function getCacheKeyParameters()
	{
		return array("context->id" => $this->getHandler()->getContext()->getId(),
			"lang->id" => RequestContext::getInstance()->getLang());
	}

	/**
	 * Mandatory execute method...
	 *
	 * @param block_BlockContext $context
	 * @param block_BlockRequest $request
	 * @return String the view name
	 */
	public function execute($context, $request)
	{
		$menu = null;
		try
        {
	        $menu = website_WebsiteModuleService::getInstance()->getMenuByTag('menu-main', 0);
        }
        catch (TopicException $e)
        {
        	$menu = website_WebsiteModuleService::getInstance()->getContextMenu(null, 0);
        	Framework::debug("website_BlockMainAction: could not find tagged menu \"menu-main\": using first level topics.");
        }

        if ( is_null($menu) )
        {
	        return block_BlockView::ERROR;
        }

        $this->setParameter('menu', $menu);
		return block_BlockView::SUCCESS;
	}
}
