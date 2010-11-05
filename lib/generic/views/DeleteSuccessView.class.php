<?php
/**
 * @deprecated
 */
class generic_DeleteSuccessView extends f_view_BaseView
{
	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
	{
		$this->setTemplateName('Generic-Response', K::XML);
		$this->setStatus(self::STATUS_OK);

		$this->setAttribute('id', implode(',', $request->getAttribute('ids')));

		$this->setAttribute('workinglang', RequestContext::getInstance()->getLang());
		$this->setAttribute('uilang', RequestContext::getInstance()->getUILang());

	}
}