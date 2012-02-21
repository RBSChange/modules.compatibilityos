<?php
/**
 * @deprecated
 */
class website_EditContentErrorView extends f_view_BaseView
{
    /**
	 * @deprecated
	 */
    public function _execute($context, $request)
    {
        $this->setTemplateName('Website-EditContent-Error', K::XUL);

        $this->setAttribute('error', LocaleService::getInstance()->transBO('m.website.bo.general.page-edition-error-message-' . $request->getAttribute('error'), array('ucf')));

        if ($request->hasAttribute('document'))
        {
        	$document = $request->getAttribute('document');
        	$content = self::normalizeContent($document->getContentForLang(RequestContext::getInstance()->getLang()));
        	$this->setAttribute('content', $content);
        }

        $modules = array('generic', 'uixul', 'website');
        foreach ($modules as $module)
        {
			$this->getStyleService()->registerStyle('modules.' . $module . '.backoffice');
        }
        $this->setAttribute('cssInclusion', $this->getStyleService()->execute(K::XUL));
    }

    /**
     * @deprecated
     */
    public static function normalizeContent($content)
    {
        $content = preg_replace('/blockwidth="[0-9]+%"/i', '', $content);
        $content = str_replace('&nbsp;', ' ', $content);
        $content = str_replace('class="preview"', '', $content);
        $content = str_replace('&', '&amp;', htmlentities($content));
        $content = str_replace('&amp;lt;', "&lt;", $content);
        $content = str_replace('&amp;gt;', '&gt;', $content);
        $content = str_replace('&amp;quot;', '&quot;', $content);
        $content = str_replace('&amp;nbsp;', ' ', $content);
        return $content;
    }
}