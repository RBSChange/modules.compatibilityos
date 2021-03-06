<?php
class uixul_InitializeSuccessView extends f_view_BaseView
{
	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
    {
        $rq = RequestContext::getInstance();

        $rq->beginI18nWork($rq->getUILang());

        $this->setTemplateName('Uixul-Initialize-Success', K::HTML);
        $this->setAttribute(
            'styles',
            $this->getStyleService()
    	    	  ->registerStyle('modules.uixul.dialog')
    	    	  ->execute(K::HTML)
        );

        $rq->endI18nWork();
    }
}