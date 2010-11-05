<?php
class website_BlockCopyrightAction extends block_BlockAction
{
	public function getCacheSpecifications()
	{
		// everything in the template : no data retrieved here
		return array();
	}
	
	public function getCacheKeyParameters()
	{
		// no parameter used in the template ? or return the entire request (let default ?)
		return array();
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
		return block_BlockView::SUCCESS;
	}
}