<?php
class uixul_EditLocaleInputView extends f_view_BaseView
{

    /**
	 * @param Context $context
	 * @param Request $request
	 */
    public function _execute($context, $request)
    {
        $moduleName = $request->getAttribute('moduleName');

        // Template :
        $this->setTemplateName('Uixul-EditLocale-Input', K::XUL);

        // Styles :
        $styles = array(
            'modules.generic.backoffice',
            'modules.generic.bindings',
            'modules.uixul.backoffice',
            'modules.uixul.bindings',
            'modules.uixul.editlocale'
        );

        foreach ($styles as $style)
        {
            $this->getStyleService()->registerStyle($style);
        }

        $this->setAttribute('cssInclusion', $this->getStyleService()->execute(K::XUL));

        // Scripts :
		$this->getJsService()->registerScript('modules.uixul.lib.default');

        $this->setAttribute('scriptInclusion', $this->getJsService()->executeInline(K::XUL));

        // Misc. data :
        $this->setAttribute(
            'windowTitle',
            f_Locale::translateUI(
                '&modules.uixul.bo.editlocale.EditingModule;',
                array('moduleName' => f_Locale::translateUI('&modules.' . $moduleName . '.bo.general.Module-Name;'))
            )
        );

        $this->setAttribute(
            'moduleLabel',
            f_Locale::translateUI('&modules.' . $moduleName . '.bo.general.Module-Name;')
        );
		$cModule = ModuleService::getInstance()->getModule($moduleName);
		
        $icon = $cModule->getIconName();
        $this->setAttribute('moduleIcon', MediaHelper::getIcon($icon, MediaHelper::SMALL));

        $this->setAttribute('defaultLanguages', f_util_StringUtils::php_to_js(RequestContext::getInstance()->getSupportedLanguages()));

        $this->setAttribute('moduleName', $moduleName);
    }
}
