<?php
/**
 * @deprecated
 */
class website_EditLinkSuccessView extends f_view_BaseView
{
    /**
	 * @param Context $context
	 * @param Request $request
	 */
    public function _execute($context, $request)
    {
        $rq = RequestContext::getInstance();

        $rq->beginI18nWork($rq->getUILang());

        // Set our template
        $this->setTemplateName('Page-EditLink-Success', K::XUL);

        $modules = array();
        $availableModules = ModuleService::getInstance()->getModules();
        foreach ($availableModules as $availableModuleName)
        {
            $modules[] = substr($availableModuleName, strpos($availableModuleName, '_') + 1);
        }

        foreach ($modules as $module)
        {
            if (($module == 'website')
            || ($module == 'uixul')
            || ($module == K::GENERIC_MODULE_NAME))
            {
                $this->getStyleService()
                    ->registerStyle('modules.' . $module . '.backoffice')
	                ->registerStyle('modules.' . $module . '.bindings');
            }
        }
        $this->setAttribute('cssInclusion', $this->getStyleService()->execute(K::XUL));

		$this->getJsService()->registerScript('modules.uixul.lib.default');
        $this->setAttribute('scriptInclusion', $this->getJsService()->executeInline(K::XUL));

        $languages = array();
		foreach (RequestContext::getInstance()->getSupportedLanguages() as $lang)
		{
		    $languages[$lang] = array(
		        'label' => f_Locale::translateUI('&modules.uixul.bo.languages.' . ucfirst($lang) . ';'),
		        'enabled' => true
		    );
		}

		$rq->endI18nWork();

        try
        {
            if ($request->hasParameter(K::COMPONENT_ACCESSOR))
            {
                $incomingTag = explode('>', $request->getParameter(K::COMPONENT_ACCESSOR));
                $incomingTag = $incomingTag[0];

                $langMatch = array();
                if (preg_match('/lang="([^"]+)"/i', $incomingTag, $langMatch))
                {
                    $language = strtolower(trim($langMatch[1]));
                }
                else
                {
                    $language = RequestContext::getInstance()->getLang();
                }

                $cmprefMatch = array();
                if (preg_match('/cmpref="([^"]+)"/i', $incomingTag, $cmprefMatch))
                {
                    $documentId = intval($cmprefMatch[1]);

                    $rq->beginI18nWork($language);

                    $document = DocumentHelper::getDocumentInstance($documentId);

                    if (f_util_ClassUtils::methodExists($document, 'getUrl'))
                    {
                        $url = f_util_ClassUtils::callMethodOn($document, 'getUrl');
                    }
                    else
                    {
                        $url = LinkHelper::getDocumentUrl($document, $language);
                    }

                    $this->setAttribute('documentId', $documentId);

                    $this->setAttribute('dynUrl', $url);

                    $langs = $document->getI18nInfo()->getLangs();

                    foreach ($languages as $lang => $language)
                    {
                        if (!in_array($lang, $langs))
                        {
                            $languages[$lang]['enabled'] = false;
                        }
                    }

                    $rq->endI18nWork();
                }
            }
        }
        catch (Exception $e)
        {
			Framework::exception($e);
        }

        $rq->beginI18nWork($rq->getUILang());

        $jsLanguages = array();
        foreach ($languages as $lang => $language)
        {
            if ($language['enabled'])
            {
                $jsLanguages[] = $lang . ': "' . $language['label'] . '"';
            }
            else
            {
                $jsLanguages[] = $lang . ': "!' . $language['label'] . '"';
            }
        }
        $this->setAttribute('languages', implode(', ', $jsLanguages));

        $this->setAttribute('object', f_Locale::translateUI('&modules.website.bo.general.edit-link.Object;'));

        $rq->endI18nWork();
    }
}
