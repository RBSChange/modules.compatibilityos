<?php
class contactcard_BlockDetailDummyView extends block_BlockView
{
	/**
	 * @param block_BlockContext $context
	 * @param block_BlockRequest $request
     * @return void
	 */
	public function execute($context, $request)
	{
		$this->setTemplateName('dummy');
	}
}