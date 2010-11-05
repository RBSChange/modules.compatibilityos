<?php
class website_BlockHeaderSuccessView extends block_BlockView
{
	/**
	 * Mandatory execute method...
	 *
	 * @param block_BlockContext $context
	 * @param block_BlockRequest $request
	 */
    public function execute($context, $request)
    {
    	$this->setTemplateName('Website-Menu-Header');
    }
}