<?php
/**
 * @deprecated
 */
class generic_ShowDocumentPropertiesSuccessView extends f_view_BaseView
{
	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
	{
		$document = $request->getAttribute('document');
		$props = DocumentHelper::getPropertiesOf($document);
		
		echo '<table border="1">';
		foreach ($props as $name => $value)
		{
			if ($value instanceof f_persistentdocument_PersistentDocumentArray)
			{
				$label = array();
				foreach ($value as $v)
				{
					$label[] = $v->__toString();
				}
				$value = 'Child documents:<ul><li>' . join('</li><li>', $label) . '</li>';
			}
			if (empty($value))
			{
				$value = '&nbsp;';
			}
			echo '<tr><th scope="row">'.$name.'</td><td>'.strval($value).'</td></tr>';
		}
		echo '</table>';
	}
}