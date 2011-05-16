<?php defined('SYSPATH') or die('No direct access allowed.');

abstract class callmanagerfunction_Driver
{
	private $requiredparams = array();

	public static function getActionLink($params, $linktype = 'image') {
		switch ($linktype)
		{
			case 'button':
				$value = $params['text'];
				$extra = '';
				if (isset($params['extra']))
				{
					$extra = $params['extra'];
				}
				$extra .= ' onClick="$.get(' . $params['href'] . '); return false;"';
				return html::button($params, $value, $extra);
				break;
			case 'text':
				return '<a id="' . $params['id'] . '" name="' . $params['name'] . '" class="' . $params['class'] . '" href="' . $params['href'] . '">' . $params['text'] . '</a>';
				break;
			case 'image':
			default:
				return '<a id="' . $params['id'] . '" name="' . $params['name'] . '" class="' . $params['class'] . '" href="' . $params['href'] . '"><img src="' . $params['image'] . '" class="' . $params['class'] . '" title="' . $params['text'] . '" alt="' . $params['text'] . '"/></a>';
				break;
		}
	}

	public function processAction(&$controller, $params) {}

	public function executeAction(&$eslCon, $params) {}

	public function assignEmptyParams(&$paramarr, $paramcount, $defaultvalues=null)
	{
		if (!is_array($paramarr))
		$paramarr = array();

		for ($i=0; $i<$paramcount; $i++)
		{
			if (isset($defaultvalues[$i]))
			$paramarr += array($i => $defaultvalues[$i]);
			else
			$paramarr += array($i => null);
		}
	}

	public function checkRequiredParams($funcname, $paramarr)
	{
		if (!isset($requiredparams[$funcname]))
		return true;
		$i = 0;
		foreach ($requiredparams[$funcname] as $attributes)
		{
			if (isset($attributes['required']) && $attributes['required'] == true)
			{
				if (!isset($paramarr[$i]) || $paramarr[$i] === null)
				$this->_throwError($funcname . ': ' . $attributes['message']);
			}
			$i++;
		}
	}

	public function getButtonLink($params)
	{
		$params['name'] = 'submit[' . $params['name'] . ']';
		$data = array('name' => 'submit[' .Bluebox_Controller::SUBMIT_CONFIRM .']', 'class' => 'save small_green_button');
		return form::button();
	}

	public function getTextLink($params)
	{
	}

	public function getImageLink($params)
	{
	}

	protected function _throwError($errorMessage, $errorLevel = -10)
	{
		throw new callmanagerException($errorMessage, $errorLevel);
	}
}
?>
