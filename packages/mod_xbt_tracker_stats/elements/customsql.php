<?php
/**
 * @version			3.3.2-dev
 * @package			Joomla
 * @subpackage	mod_xbt_tracker_latest
 * @copyright	Copyright (C) 2007 - 2015 Hugo Carvalho (www.visigod.com). All rights reserved.
 * @license			GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

class JFormFieldCUSTOMSQL extends JFormField {

	var	$type = 'CUSTOMSQL';

	public function getInput(){
		$db	 = JFactory::getDBO();
		$db->setQuery($this->element['query']);
		$key = ($this->element['key_field'] ? $this->element['key_field'] : 'value');
		$val = ($this->element['value_field'] ? $this->element['value_field'] : $this->name);
		return JHtml::_('select.genericlist',  $db->loadObjectList(), $this->name, 'multiple="multiple" size="5"', $key, $val, $this->value, $this->id);
	}
}