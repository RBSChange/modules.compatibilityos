<?php
/**
 * @deprecated
 */
class website_EditLinkAction extends f_action_BaseAction
{
	/**
	 * @param Context $context
	 * @param Request $request
	 */
    public function _execute($context, $request)
    {
		return View::SUCCESS;
	}
}