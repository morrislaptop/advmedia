<?php
class FilterController extends AppController {

	var $name = 'Filter';
	var $uses = array();

	function beforeFilter() {

		App::import('Vendor', 'Media.Medium');
		list($file, $relativeFile) = $this->_file();
		$filterDirectory = MEDIA_FILTER;
		$relativeDirectory = DS . rtrim(dirname($relativeFile), '.');
		$createDirectory = true;
		$overwrite = true;

		$name = Medium::name($file);
		$filter = Configure::read('Media.filter.' . strtolower($name));
		$filter = array($this->params['action'] => $filter[$this->params['action']]); // only do this conversion

		foreach ($filter as $version => $instructions) {
			$directory = Folder::slashTerm($filterDirectory . $version . $relativeDirectory);
			$Folder = new Folder($directory, $createDirectory);

			if (!$Folder->pwd()) {
				$message  = "MediaBehavior::make - Directory `{$directory}` ";
				$message .= "could not be created or is not writable. ";
				$message .= "Please check the permissions.";
				trigger_error($message, E_USER_WARNING);
				continue;
			}

			if (!$Medium = Medium::make($file, $instructions)) {
				$message  = "MediaBehavior::make - Failed to make version `{$version}` ";
				$message .= "of file `{$file}`. ";
				trigger_error($message, E_USER_WARNING);
				continue;
			}

		}
		return true;

	}

	/**
	 * Gets the file path from the passed url
	 */
	function _file() {
		$path = implode('/', $this->params['pass']);
		return array(MEDIA . $path, $path);
	}
}
?>