<?php
interface website_WizardBean extends f_mvc_Bean
{
	/**
	 * @return Integer
	 */
	function getStepCount ();
	
	/**
	 * @param Integer $step
	 * @return String[]
	 */
	function getPropertyNamesByStep($step);
}
