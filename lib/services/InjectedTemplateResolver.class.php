<?php
class compatibilityos_InjectedTemplateResolver extends TemplateResolver
{
	/**
	 * Return the path of the researched resource
	 * @param string $templateName Name of researched template
	 * @return string Path of resource
	 */
	public function getPath($templateName)
	{
		$path = parent::getPath($templateName);
		if ($path === null)
		{
			list($p1, $p2) = explode('_', $this->getPackageName());
			$fullName = $templateName.'.all.all.'.$this->getMimeContentType();
			$path = f_util_FileUtils::buildWebeditPath('modules', 'compatibilityos', 'templates', $p1, $p2, $this->getDirectory(), $fullName);
			return (file_exists($path)) ? $path : null;
		}	
		return $path;
	}
}