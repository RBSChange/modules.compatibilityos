<?php
/**
 * @deprecated
 */
class generic_GetSupportedLanguagesSuccessView extends f_view_BaseView
{
	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
	{
		$this->setTemplateName(ucfirst(K::GENERIC_MODULE_NAME).'-Response', K::XML, K::GENERIC_MODULE_NAME);

		$this->setStatus(self::STATUS_OK);

		$message = array();

		foreach ($request->getAttribute('supportedLanguages') as $lang)
		{
		    $message[] = sprintf(
		        '%s:%s',
		        $lang,
		        f_Locale::translate('&modules.uixul.bo.languages.' . ucfirst($lang) . ';', null, $lang)
		    );
		}

		$this->setAttribute('message', implode(' ', $message));
	}
}