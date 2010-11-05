<?php


class solrsearch_BlockSearchformSuccessView extends block_BlockView
{

	/**
     * @param block_BlockContext $context
     * @param block_BlockRequest $request
     */
	public function execute($context, $request)
	{
		$this->setTemplateName('Solrsearch-Block-Searchform');
		$this->setAttributes($this->getParameters());
	}
}