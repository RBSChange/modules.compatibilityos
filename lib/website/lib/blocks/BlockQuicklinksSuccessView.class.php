<?php
class website_BlockQuicklinksSuccessView extends block_BlockView
{
	/**
	 * @param block_BlockContext $context
	 * @param block_BlockRequest $request
	 */
    public function execute($context, $request)
    {
    	$this->setTemplateName('Website-Menu-Quicklinks');
    }
}
