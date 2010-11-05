<?php
class website_BlockQuicklinksAction extends block_BlockAction
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
   	    $displayParameter = $this->getParameters();

        try
        {
	        $menu = website_WebsiteModuleService::getInstance()->getMenuByTag('menu-quicklinks');
	        $this->setParameter('menu', $menu);
        }
        catch (TagException $e)
        {
			return block_BlockView::ERROR;
        }

		return block_BlockView::SUCCESS;
	}
}