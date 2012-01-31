<?php
class compatibilityos_PHPTAL_CHANGE
{
	/**
	 * @param PHPTAL_Namespace_CHANGE $namespaceCHANGE
	 */
	public static function addAttributes($namespaceCHANGE)
	{
		$namespaceCHANGE->addAttribute(new PHPTAL_NamespaceAttributeSurround('docattr', 31));
		$namespaceCHANGE->addAttribute(new PHPTAL_NamespaceAttributeSurround('propattr', 31));
		$namespaceCHANGE->addAttribute(new PHPTAL_NamespaceAttributeReplace('edit', 31));
		$namespaceCHANGE->addAttribute(new PHPTAL_NamespaceAttributeSurround('create', 31));
		
		$namespaceCHANGE->addAttribute(new PHPTAL_NamespaceAttributeSurround('menu', 9));
		$namespaceCHANGE->addAttribute(new PHPTAL_NamespaceAttributeSurround('menuitem', 9));
		
		$namespaceCHANGE->addAttribute(new PHPTAL_NamespaceAttributeSurround('currentlink', 30));
	}
}