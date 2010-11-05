<?php
/**
 * @deprecated
 */
class generic_ShowDocumentPropertiesInputView extends f_view_BaseView
{
	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
	{
		$this->sendHttpHeaders();
		$this->setTemplateName('Generic-ShowDocumentProperties', K::HTML);
	}
}