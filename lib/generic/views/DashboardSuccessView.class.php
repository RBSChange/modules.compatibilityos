<?php
/**
 * @deprecated
 */
class generic_DashboardSuccessView extends f_view_BaseView
{
	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
	{
		$this->setTemplateName('Generic-Dashboard-Success', 'xml');
	}

	/**
	 * Sets the title of the widget.
	 *
	 * @param string $title
	 */
	public function setTitle($title)
	{
		$this->setAttribute('title', $title);
	}

	/**
	 * Sets the icon of the widget. Size is always SMALL.
	 *
	 * @param string $icon The icon name.
	 */
	public function setIcon($icon)
	{
		$this->setAttribute('icon', MediaHelper::getIcon($icon, MediaHelper::SMALL, null, MediaHelper::LAYOUT_SHADOW));
	}

	/**
	 * Sets the content of the widget.
	 *
	 * @param string $content HTML content to set into the widget.
	 */
	public function setContent($content)
	{
		$this->setAttribute('content', '<![CDATA['.$content.']]>');
	}
}