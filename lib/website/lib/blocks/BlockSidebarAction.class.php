<?php
class website_BlockSidebarAction extends block_BlockAction
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
		 "modules_website/website");
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
	    $this->setParameter(
        	'menu',
        	website_WebsiteModuleService::getInstance()->getContextMenu(DocumentHelper::getDocumentInstance($context->getId()), 3)
        	);
		return block_BlockView::SUCCESS;
	}
}
