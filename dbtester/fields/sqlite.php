<?php
/**
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;


/**
 * 
 * Provides input button for sqlite-testing
 * @author manish
 *
 */
class JFormFieldSqlite extends JFormField
{
	 
	protected function getInput()
	{
		// Load the javascript and css
		JHtml::_('behavior.framework');
		JHtml::_('behavior.modal');

		$html = Array();
		
		$html[] = '<span class="input-append">
						<a 	class="btn btn-primary" 
							onclick="SqueezeBox.fromElement(this, {handler:\'iframe\', size: {x: 600, y: 450}, url:\''.JRoute::_('index.php?option=plg_dbtesting&dbtest=sqlite&tmpl=component').'\'})">
							<i class="icon-list icon-white"></i> Sqlite
						</a>
					</span>';

		return implode("\n", $html);
		
	}
	
}