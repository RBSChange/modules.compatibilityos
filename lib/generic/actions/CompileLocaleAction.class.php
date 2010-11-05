<?php
/**
 * @deprecated
 */
class generic_CompileLocaleAction extends f_action_BaseAction
{
	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
	{
		LocaleService::getInstance()->regenerateLocales();
		return self::getSuccessView();
	}
}