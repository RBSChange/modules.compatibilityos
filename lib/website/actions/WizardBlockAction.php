<?php
abstract class website_WizardBlockAction extends website_BlockAction
{
	/**
	 * @var array
	 */
	private $wizardData;

	/**
	 * @var String
	 */
	private $sessionKey;

	/**
	 * @var website_WizardBean
	 */
	private $bean;

	/**
	 * @see website_BlockAction::execute()
	 *
	 * @param website_BlockActionRequest $request
	 * @param website_BlockActionResponse $response
	 * @return String
	 */
	function execute($request, $response)
	{
		if ($this->isInBackoffice())
		{
			return null;
		}
		$nextStep = $this->getNextStep($request);

		if ($nextStep === null)
		{
			$this->wizardData["currentStep"] = 0;
		}
		else
		{
			$this->wizardData["currentStep"] = $nextStep;
		}

		$session = $this->getSession();
		$key = $this->getSessionKey($request);
		$session->setAttribute($key, $this->wizardData);
		if ($this->wizardData["currentStep"] == $this->getStepCount($this->bean))
		{
			$endView = $this->executeEnd($request, $response, $this->bean);
			$session->removeAttribute($key);
			return $endView;
		}
		else if ($this->wizardData["currentStep"] == -1)
		{
			$cancelView = $this->executeCancel($request, $response, $this->bean);
			$session->removeAttribute($key);
			return $cancelView;
		}
		return $this->getPrefixViewName($this->bean) . $this->wizardData["currentStep"];
	}

	/**
	 * @return String
	 */
	function getInputViewName()
	{
		if (!isset($this->wizardData["currentStep"]))
		{
			return $this->getPrefixViewName($this->bean) . '0';
		}
		return $this->getPrefixViewName($this->bean) . $this->wizardData["currentStep"];
	}

	/**
	 * @param website_BlockActionRequest $request
	 * @param website_BlockActionResponse $response
	 * @return Boolean
	 */
	function validateInput($request, $response)
	{
		if ($this->isInBackoffice())
		{
			return true;
		}

		$data = $this->getWizardData($request);
		$nextStep = $this->getNextStep($request);
		$bean = $this->getNewWizardInstance($request);
		$invalidProperties = BeanUtils::populate($bean, array_merge($data["data"], $request->getParameters()));
		$allPropertiesValid = f_util_ArrayUtils::isEmpty($invalidProperties);
		if (!$allPropertiesValid)
		{
			foreach ($invalidProperties as $propertyName => $rawValue)
			{
				$array = array('field' => f_Locale::translate(BeanUtils::getBeanProperyInfo($bean, $propertyName)->getLabelKey()), 'value' => $rawValue);
				$this->addError(f_Locale::translate('&framework.validation.validator.InvalidValue;', $array));
			}
			$request->setAttribute("invalidProperties", $invalidProperties);
		}

		$this->bean = $bean;
		$this->wizardData["data"] = BeanUtils::getSerializableProperties($bean);
		$request->setAttribute($this->getWizardName(), $bean);

		if ($nextStep === null || $nextStep < $data["currentStep"])
		{
			return true && $allPropertiesValid;
		}

		return $this->validateInputByStep($request, $data["currentStep"], $bean) && $allPropertiesValid;
	}

	// protected methods


	/**
	 * @param website_BlockActionRequest $request
	 * @param website_BlockActionResponse $response
	 * @param website_WizardBean $bean
	 * @return String
	 */
	abstract protected function executeEnd($request, $response, $bean);

	/**
	 * Does nothing by default.
	 * Session data are cleared after executeCancel execution.
	 *
	 * @param website_BlockActionRequest $request
	 * @param website_BlockActionResponse $response
	 * @param website_WizardBean $bean
	 * @return String
	 */
	protected function executeCancel($request, $response, $bean)
	{
		// Nothing to do by default.
	}

	/**
	 * @param website_BlockActionRequest $request
	 * @return website_WizardBean
	 */
	abstract protected function getNewWizardInstance($request);

	/**
	 * @return String
	 */
	abstract protected function getWizardName();

	/**
	 * @param website_BlockActionRequest $request
	 * @param Integer $step
	 * @param website_WizardBean $bean
	 * @return Array
	 */
	protected function validateInputByStep($request, $step, $bean)
	{
		$rules = BeanUtils::getBeanValidationRules(get_class($bean), $bean->getPropertyNamesByStep($step));
		return $this->processValidationRules($rules, $request, $bean);
	}

	/**
	 * @param website_WizardBean $bean
	 * @return String
	 */
	protected function getPrefixViewName($bean = null)
	{
		return 'Form';
	}

	/**
	 * @param website_WizardBean $bean
	 * @return Integer
	 */
	protected function getStepCount($bean)
	{
		return $bean->getStepCount();
	}

	// private methods


	/**
	 * @param website_BlockActionRequest $request
	 */
	protected final function getNextStep($request)
	{
		if ($request->hasParameter('goToPreviousStep'))
		{
			if (!$request->hasNonEmptyParameter("previousStep"))
			{
				return null;
			}
			return (int) $request->getParameter("previousStep");
		}
		// Here we do not check that the used submit is really 'goToNextStep'
		// to work with old blocks.
		else
		{
			if (!$request->hasNonEmptyParameter("nextStep"))
			{
				return null;
			}
			return (int) $request->getParameter("nextStep");
		}
	}

	/**
	 * @param website_BlockActionRequest $request
	 */
	private function getSessionKey($request)
	{
		if ($this->sessionKey !== null)
		{
			return $this->sessionKey;
		}
		$sessionKey = null;
		if ($request->hasNonEmptyParameter("wizard_process_id"))
		{
			$requestSessionKey = $request->getParameter("wizard_process_id");
			if ($this->getSession()->hasAttribute($requestSessionKey))
			{
				$sessionKey = $requestSessionKey;
			}
		}

		if ($sessionKey === null)
		{
			$session = $this->getSession();
			do
			{
				$id = uniqid("wizard_");
			} while ($session->hasAttribute($id));
			$sessionKey = $id;
			$session->setAttribute($sessionKey, array("currentStep" => 0 , "data" => array()));
		}
		$request->setAttribute("wizard_process_id", $sessionKey);
		$this->sessionKey = $sessionKey;
		return $this->sessionKey;
	}

	/**
	 * @param website_BlockActionRequest $request
	 */
	protected final function getWizardData($request)
	{
		if ($this->wizardData !== null)
		{
			return $this->wizardData;
		}
		$session = $this->getSession();
		$key = $this->getSessionKey($request);
		$this->wizardData = $session->getAttribute($key);
		return $this->wizardData;
	}

	protected final function setWizardData($data)
	{
		$this->wizardData = $data;
	}
}