<?php
class FilterController extends AppController {

	var $name = 'Filter';
	var $uses = array();

	function beforeFilter() {

		App::import('Vendor', 'Media.Medium');
		list($file, $relativeFile) = $this->_file();
		$name = Medium::name($file);
		$filter = Configure::read('Media.filter.' . strtolower($name));
		$advfilter = Configure::read('Advmedia.filter.' . strtolower($name));
		$action = $this->params['action'];
		if ( isset($filter[$action]) ) {
			$filter = array($action => $filter[$action]); // only do this conversion
		}
		else if ( isset($advfilter[$action]) ) {
			$filter = array($action => $advfilter[$action]); // only do this conversion
		}
		else {
			// this isnt a stored favourite, lets see.
			$filter = $this->_actionToFilter();
		}

		// These would usually be in the settings
		$filterDirectory = MEDIA_FILTER;
		$relativeDirectory = DS . rtrim(dirname($relativeFile), '.');
		$createDirectory = true;
		$overwrite = true;
				
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
			
			// Try and guess where a file would be if we are doing a conversion
			if ( array_key_exists('convert', $instructions) ) {
				$ext = MimeType::guessExtension($instructions['convert']);
				if ( defined('PATHINFO_FILENAME') ) {
					$filename = pathinfo($file,PATHINFO_FILENAME);
				}
				else {
					$filename = substr($file, 0, strrpos($file,'.'));
				}
				$path = $directory . $filename . '.' . $ext;
				if ( file_exists($path) ) {
					$final = $path;
					break;
				}	
			}
			
			// Make conversion
			if (!$Medium = Medium::make($file, $instructions)) {
				$message  = "MediaBehavior::make - Failed to make version `{$version}` ";
				$message .= "of file `{$file}`. ";
				trigger_error($message, E_USER_WARNING);
				continue;
			}
			$final = $Medium->store($directory . basename($file), $overwrite);
		}

		// redirect to the file again.
		$redirect = '/' . str_replace(WWW_ROOT, '', $final);
		$this->redirect($redirect);
		return true;
	}

	function _actionToFilter() {
		$action = $this->params['action'];
		$filter = explode(',', $action);
		$filters = array();
		foreach ($filter as &$act) {
			list($method, $params) = explode('-', $act);
			$params = explode('.', $params);
			$filters[$method] = $params;
		}
		$filters = array($action => $filters);
		return $filters;
	}

	/**
	 * Gets the file path from the passed url
	 */
	function _file() {
		$path = implode('/', $this->params['pass']);
		if ( !empty($this->params['url']['ext']) ) {
			$path .= '.' . $this->params['url']['ext'];
		}
		return array(MEDIA . $path, $path);
	}
}
?>