<?php
/**
 * @version			2.5.0
 * @package			Joomla
 * @subpackage	com_tracker
 * @copyright		Copyright (C) 2007 - 2012 Hugo Carvalho (www.visigod.com). All rights reserved.
 * @license			GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die('Restricted access');

// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

class TrackerModelTorrent extends JModelAdmin {

	protected function allowEdit($data = array(), $key = 'fid') {
		// Check specific edit permission then general edit permission.
		return JFactory::getUser()->authorise('core.edit', 'com_tracker.torrent.'.((int) isset($data[$key]) ? $data[$key] : 0)) or parent::allowEdit($data, $key);
	}

	public function getTable($type = 'Torrent', $prefix = 'TrackerTable', $config = array()) {
		return JTable::getInstance($type, $prefix, $config);
	}

	public function getForm($data = array(), $loadData = true) {
		$form = $this->loadForm('com_tracker.torrent', 'torrent', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) return false;
		return $form;
	}

	protected function loadFormData() {
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_tracker.edit.torrent.data', array());
		if (empty($data)) $data = $this->getItem();
		return $data;
	}

	public function delete($itemIds) {
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		
		// Sanitize the ids.
		$itemIds = array_unique($itemIds);
		JArrayHelper::toInteger($itemIds);

		// Update the flag type of all torrents (when flag = 1, torrent will be deleted).
		$query->clear();
		$query->update($db->quoteName('#__tracker_torrents'));
		$query->set($db->quoteName('flags') . ' = 1');
		$query->where($db->quoteName('fid') . ' IN (' . implode(',', $itemIds) . ')');
		$db->setQuery($query);
		try {
			$result = $db->query();
		} catch (Exception $e) {
			return false;
		}

		//Delete the torrent thanks
		$query->clear();
		$query->delete($db->quoteName('#__tracker_torrent_thanks'));
		$query->where($db->quoteName('torrentID') . ' IN (' . implode(',', $itemIds) . ')');
		$db->setQuery($query);
		try {
			$result = $db->query();
		} catch (Exception $e) {
			return false;
		}

		// Delete the reported torrent
		$query->clear();
		$query->delete($db->quoteName('#__tracker_reported_torrents'));
		$query->where($db->quoteName('fid') . ' IN (' . implode(',', $itemIds) . ')');
		$db->setQuery($query);
		try {
			$result = $db->query();
		} catch (Exception $e) {
			return false;
		}

		// Delete the reseed requested torrent
		$query->clear();
		$query->delete($db->quoteName('#__tracker_reseed_request'));
		$query->where($db->quoteName('fid') . ' IN (' . implode(',', $itemIds) . ')');
		$db->setQuery($query);
		try {
			$result = $db->query();
		} catch (Exception $e) {
			return false;
		}
		
		return true;
	}
}