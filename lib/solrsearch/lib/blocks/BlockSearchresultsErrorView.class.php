<?php

class solrsearch_BlockSearchresultsErrorView extends block_BlockView 
{
	public function initialize($context, $request)
	{
		$this->disableCache();
	}

	public function execute($context, $request)
	{	
		$tpl = 'Solrsearch-Block-Searchresults-Error';
		if ($this->hasParameter('module') && $this->hasParameter('document') && $this->hasParameter('form'))
        {
        	$tpl = ucfirst($this->getParameter('module')). '-Block-' . $this->getParameter('form').ucfirst($this->getParameter('document')) . '-Searchresults-Error';
        }
        $this->setTemplateName($tpl);
		$this->setAttributes($this->getParameters());
	}
}