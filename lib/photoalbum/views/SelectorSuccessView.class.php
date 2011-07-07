<?php
class photoalbum_SelectorSuccessView extends f_view_BaseView
{
	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
	{
		$this->setTemplateName('Photoalbum-Block-Album-Selector', K::HTML);
		$this->setAttribute('item', $request->getAttribute('item'));
	}
}