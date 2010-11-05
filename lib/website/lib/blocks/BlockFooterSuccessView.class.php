<?php
class website_BlockFooterSuccessView extends block_BlockView
{
	/**
	 * @param block_BlockContext $context
	 * @param block_BlockRequest $request
	 */
    public function execute($context, $request)
    {
    	$this->setTemplateName('Website-Menu-Footer');
        $this->setAttribute('currentPage', WebsiteHelper::getCurrentPageAttributeForMenu($context)); // FIXME intsimoa : still needed ?
    }
}
