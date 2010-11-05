<?php
/**
 * @deprecated
 */
class generic_GetSupportedLanguagesAction extends f_action_BaseAction
{
	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
	{
		$request->setAttribute('supportedLanguages', RequestContext::getInstance()->getSupportedLanguages());

		return View::SUCCESS;
	}
}