<?php
/**
 * @deprecated
 */
class generic_CheckConfigAction extends f_action_BaseAction
{
	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
	{
	    $rq = RequestContext::getInstance();

        $rq->beginI18nWork($rq->getUILang());

		$moduleName = $this->getModuleName($request);

		$missingRequiredTags = ModuleService::getInstance()->getRequiredTags($moduleName, true);

		if (count($missingRequiredTags) > 0)
		{
			$tagsXml = '<tags>';
			foreach ($missingRequiredTags as $tag => $tagInfo) {
				$tagsXml .= sprintf('<tag value="%s" icon="%s">%s</tag>', $tag, MediaHelper::getIcon($tagInfo["icon"], MediaHelper::SMALL, null, MediaHelper::LAYOUT_SHADOW), $tagInfo["label"]);
			}
			$tagsXml .= '</tags>';
			$request->setAttribute('contents', $tagsXml);

			$rq->endI18nWork();

			return self::getErrorView();
		}

		$rq->endI18nWork();

		return self::getSuccessView();
	}
}