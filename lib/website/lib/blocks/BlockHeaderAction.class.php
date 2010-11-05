<?php
class website_BlockHeaderAction extends block_BlockAction
{
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
	        $menu = website_WebsiteModuleService::getInstance()->getMenuByTag('menu-header');
        }
        catch (TopicException $e)
        {
        	$menu = website_WebsiteModuleService::getInstance()->getContextMenu();
        }

        if ( is_null($menu) )
        {
	        return block_BlockView::ERROR;
        }

        $this->setParameter('menu', $menu);
		return block_BlockView::SUCCESS;
	}
}