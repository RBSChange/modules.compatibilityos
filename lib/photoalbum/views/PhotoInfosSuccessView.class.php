<?php
class photoalbum_PhotoInfosSuccessView extends f_view_BaseView
{
	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
	{
		$this->setTemplateName('Photoalbum-Block-Photo-Infos', K::HTML);
		$this->setAttribute('album', $request->getAttribute('album'));
	}
}