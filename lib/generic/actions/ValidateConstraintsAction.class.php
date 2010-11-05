<?php
/**
 * @deprecated
 */
class generic_ValidateConstraintsAction extends f_action_BaseAction
{
	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
	{
		$data = $request->getParameter('data', '');
		$constraints = stripslashes($request->getParameter('constraints', ''));
		$name = $request->getParameter('name', null);
		if (is_string($data))
		{
			// FIXME: XML validator doesn't work with this. Was it useful for anything ?
			//$data = f_util_StringUtils::htmlToText(stripslashes(trim($data)), false);
			$data = f_util_Convert::fixDataType($data);
		}
		if (is_string($name))
		{
			$name = stripslashes($name);
		}
		$property   = new validation_Property($name, $data);
		$errors     = new validation_Errors();
		$constraintsParser = new validation_ContraintsParser();
		$validators = $constraintsParser->getValidatorsFromDefinition($constraints);


		foreach ($validators as $validator)
		{
			if ($validator instanceof validation_UniqueValidator)
			{
				if ($request->hasParameter(K::COMPONENT_ID_ACCESSOR))
				{
					$document = $this->getDocumentService()->getDocumentInstance(
						$request->getParameter(K::COMPONENT_ID_ACCESSOR)
					);

					$validator->setDocument($document);
				}
				else if (!is_null($request->getParameter('documentModel', null)))
				{
					$validator->setPersistentProvider($this->getPersistentProvider());
					$validator->setDocumentModelName($request->getParameter('documentModel'));
				}
				// else the validator will throw an Exception because it misses some pieces
				// of information.
				$documentProperty = $request->getParameter('documentProperty', null);
				$validator->setDocumentPropertyName($documentProperty);

				if ($request->hasParameter(K::PARENT_ID_ACCESSOR))
				{
					$validator->setParentNodeId($request->getParameter(K::PARENT_ID_ACCESSOR));
				}
			}
	
			try
			{
				$validator->validate($property, $errors);
			}
			catch (IllegalArgumentException $e)
			{
				$request->setAttribute('message', $e->getMessage());
				return self::getErrorView();
			}
		}

		if ($errors->isEmpty())
		{
			return self::getSuccessView();
		}
		$request->setAttribute('message', $errors[0]);
		return self::getErrorView();
	}
}