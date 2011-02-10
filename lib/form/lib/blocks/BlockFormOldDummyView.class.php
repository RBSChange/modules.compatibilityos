<?php
/**
 * @deprecated (will be removed in 4.0)
 */
class form_BlockFormOldDummyView extends block_BlockView
{

	/**
	 * @param block_BlockContext $context
	 * @param block_BlockRequest $request
	 */
    public function execute($context, $request)
    {
    	$this->setTemplateName('Form-Dummy');
		$form = $this->getParameter('form');
		$this->setAttribute('form', $form);
    }
}