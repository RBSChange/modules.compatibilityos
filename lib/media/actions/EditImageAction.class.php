<?php
/**
 * @package modules.media
 */
class media_EditImageAction extends f_action_BaseAction
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