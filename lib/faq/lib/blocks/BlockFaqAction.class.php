<?php
/**
 * @deprecated
 */
class faq_BlockFaqAction extends abstractdirectory_BlockItemAction
{
	/**
	 * @deprecated
	 */
	public function initialize($context, $request)
	{
		parent::initialize($context, $request);
		$this->setModuleName('faq');
		$this->setComponentName('faq');
	}
}