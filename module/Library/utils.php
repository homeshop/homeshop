<?php

class utils
{
	static public function addslashes_array($value)
	{
		if (empty($value)) {
			return $value;
		}
		else if (is_array($value)) {
			foreach ($value as $k => $v ) {
				if (is_array($v)) {
					$value[$k] = self::addslashes_array($v);
				}
				else {
					$value[$k] = addslashes($v);
				}
			}

			return $value;
		}
		else {
			return addslashes($value);
		}
	}

	static public function stripslashes_array($value)
	{
		if (empty($value)) {
			return $value;
		}
		else if (is_array($value)) {
			$tmp = $value;

			foreach ($tmp as $k => $v ) {
				$k = stripslashes($k);
				$value[$k] = $v;

				if (is_array($v)) {
					$value[$k] = self::stripslashes_array($v);
				}
				else {
					$value[$k] = stripslashes($v);
				}
			}

			return $value;
		}
		else {
			return stripslashes($value);
		}
	}

	static public function _apath(&$array, $path, &$ret)
	{
		$key = array_shift($path);
		if (($p1 = strpos($key, '[')) && ($p2 = strrpos($key, ']'))) {
			$predicates = substr($key, $p1 + 1, $p2 - $p1 - 1);
			$key = substr($key, 0, $p1);
		}

		if (is_array($array) && array_key_exists($key, $array)) {
			$next = $array[$key];
			if (isset($predicates) && is_array($next)) {
				switch (true) {
				case $predicates == 'first()':
					$next = reset($next);
					break;

				case $predicates == 'last()':
					$next = end($next);
					break;

				case is_numeric($predicates):
					$next = $next[$predicates];
					break;

				default:
					list($k, $v) = explode('=', $key);

					if ($v) {
						foreach ($next as $item ) {
							if (isset($item[$k]) && ($item[$k] == $v)) {
								$nextrst = $item;
								break;
							}
						}
					}
					else {
						foreach ($next as $item ) {
							if (isset($item[$k])) {
								$nextrst = $item;
								break;
							}
						}
					}

					if (isset($nextrst)) {
						$next = $nextrst;
					}
					else if ($predicates == 'default') {
						$next = reset($next);
					}
					else {
						return false;
					}

					break;
				}
			}

			if (!$path) {
				$ret = $next;
				return true;
			}
			else {
				return self::_apath($next, $path, $ret);
			}
		}
		else {
			return false;
		}
	}

	static public function apath(&$array, $map)
	{
		if (self::_apath($array, $map, $ret) !== false) {
			return $ret;
		}
		else {
			return false;
		}
	}

	static public function unapath(&$array, $col, $path, &$ret)
	{
		if (!array_key_exists($col, $array)) {
			return false;
		}

		$ret = '';
		$arrKey = '';
		$tmpArr = NULL;
		$pathCount = count($path);
		$pathItem = 1;

		foreach ($path as $v ) {
			if (($p1 = strpos($v, '[')) && ($p2 = strrpos($v, ']'))) {
				$predicates = substr($v, $p1 + 1, $p2 - $p1 - 1);
				$v = substr($v, 0, $p1);
			}

			if ($pathCount == $pathItem++) {
				eval('$ret' . $arrKey . '["' . $v . '"] = $array[$col];');
				unset($array[$col]);

				return true;
			}

			$arrKey .= '["' . $v . '"]';

			if ($predicates) {
				return false;
			}

			$predicates = NULL;
		}

		return true;
	}

	static public function array_path($array, $path)
	{
		$path_array = explode('/', $path);
		$_code = '$return = $array';

		if ($path_array) {
			foreach ($path_array as $s_path ) {
				$_code .= '[\'' . $s_path . '\']';
			}
		}

		$_code = $_code . ';';
		eval($_code);
		return $return;
	}

	static public function buildTag($params, $tag, $finish = true)
	{
		$ret = array();

		foreach ((array) $params as $k => $v ) {
			if (!is_null($v) && !is_array($v)) {
				if ($k == 'value') {
					$v = htmlspecialchars($v);
				}

				$ret[] = $k . '="' . $v . '"';
			}
		}

		return '<' . $tag . ' ' . implode(' ', $ret) . ($finish ? ' /' : '') . '>';
	}

	static public function mkdir_p($dir, $dirmode = 493)
	{
		$path = explode('/', str_replace('\\', '/', $dir));
		$depth = count($path);

		for ($i = $depth; 0 < $i; $i--) {
			if (file_exists(implode('/', array_slice($path, 0, $i)))) {
				break;
			}
		}

		for (; $i < $depth; $i++) {
			if ($d = implode('/', array_slice($path, 0, $i + 1))) {
				if (!is_dir($d)) {
					mkdir($d, $dirmode);
				}
			}
		}

		return is_dir($dir);
	}

	static public function cp($src, $dst)
	{
		if (is_dir($src)) {
			$obj = dir($src);

			while (($file = $obj->read()) !== false) {
				if ($file[0] == '.') {
					continue;
				}

				$s_daf = $src . '/' . $file;
				$d_daf = $dst . '/' . $file;

				if (is_dir($s_daf)) {
					if (!file_exists($d_daf)) {
						self::mkdir_p($d_daf);
					}

					self::cp($s_daf, $d_daf);
				}
				else {
					$d_dir = dirname($d_daf);

					if (!file_exists($d_dir)) {
						self::mkdir_p($d_dir);
					}

					copy($s_daf, $d_daf);
				}
			}
		}
		else if (!copy($src, $dst)) {
			throw new RuntimeException($src . ' cannot copy to ' . $dst, 10101);
		}
	}

	public function copy_directory($directory, $destination, $force = true)
	{
		if (!is_dir($directory)) {
			return false;
		}

		$options = FilesystemIterator::SKIP_DOTS;

		if (!is_dir($destination)) {
			if (!mkdir($destination, 511, true)) {
				return false;
			}
		}

		$items = new FilesystemIterator($directory, $options);

		foreach ($items as $item ) {
			$target = $destination . '/' . $item->getBasename();

			if ($item->isDir()) {
				$path = $item->getPathName();

				if (!copy_directory($path, $target, $options)) {
					return false;
				}
			}
			else {
				if (!file_exists($target) || (file_exists($target) && $force)) {
					if (!copy($item->getPathname(), $target)) {
						return false;
					}
				}
			}
		}

		return true;
	}

	static public function remove_p($sDir)
	{
		if ($rHandle = opendir($sDir)) {
			while (false !== $sItem = readdir($rHandle)) {
				if (($sItem != '.') && ($sItem != '..')) {
					if (is_dir($sDir . '/' . $sItem)) {
						self::remove_p($sDir . '/' . $sItem);
					}
					else if (!unlink($sDir . '/' . $sItem)) {
						trigger_error(app::get('base')->_('因权限原因 ') . $sDir . '/' . $sItem . app::get('base')->_('无法删除'), 1024);
					}
				}
			}

			closedir($rHandle);
			rmdir($sDir);
			return true;
		}
		else {
			return false;
		}
	}

	static public function replace_p($path, $replace_map)
	{
		if (is_dir($path)) {
			$obj = dir($path);

			while (($file = $obj->read()) !== false) {
				if ($file[0] == '.') {
					continue;
				}

				if (is_dir($path . '/' . $file)) {
					self::replace_p($path . '/' . $file, $replace_map);
				}
				else {
					self::replace_in_file($path . '/' . $file, $replace_map);
				}
			}
		}
		else {
			self::replace_in_file($path, $replace_map);
		}
	}

	static public function replace_in_file($file, $replace_map)
	{
		file_put_contents($file, str_replace(array_keys($replace_map), array_values($replace_map), file_get_contents($file)));
	}

	static public function tree($dir)
	{
		$ret = array();

		if (!is_dir($dir)) {
			return $ret;
		}

		$obj = dir($dir);

		while (($file = $obj->read()) !== false) {
			if (substr($file, 0, 1) == '.') {
				continue;
			}

			$daf = $dir . '/' . $file;
			$ret[] = $daf;

			if (is_dir($daf)) {
				$ret = array_merge($ret, self::tree($daf));
			}
		}

		return $ret;
	}

	static public function array_change_key(&$items, $key, $is_resultset_array = false)
	{
		if (is_array($items)) {
			$result = array();
			if (!empty($key) && is_string($key)) {
				foreach ($items as $_k => $_item ) {
					if ($is_resultset_array) {
						$result[$_item[$key]][] = &$items[$_k];
					}
					else {
						$result[$_item[$key]] = &$items[$_k];
					}
				}

				return $result;
			}
		}

		return false;
	}

	static public function mydate($f, $d = NULL)
	{
		global $_dateCache;

		if (!$d) {
			$d = time();
		}

		if (!isset($_dateCache[$d][$f])) {
			$_dateCache[$d][$f] = date($f, $d);
		}

		return $_dateCache[$d][$f];
	}

	public function _getval($expval)
	{
		$expval = trim($expval);

		if ($expval !== '') {
			eval('$expval = ' . $expval . ';');

			if (0 < $expval) {
				return 1;
			}
			else if ($expval == 0) {
				return 1 / 2;
			}
			else {
				return 0;
			}
		}
		else {
			return 0;
		}
	}

	public function _getceil($expval)
	{
		if ($expval = trim($expval)) {
			eval('$expval = ' . $expval . ';');

			if (0 < $expval) {
				return ceil($expval);
			}
			else {
				return 0;
			}
		}
		else {
			return 0;
		}
	}

	static public function array_ksort_recursive($data, $sort_flags = SORT_STRING)
	{
		if (is_array($data)) {
			ksort($data, $sort_flags);

			foreach ($data as $k => $v ) {
				$data[$k] = self::array_ksort_recursive($v, $sort_flags);
			}
		}

		return $data;
	}

	static public function _RemoveXSS($val)
	{
		$val = preg_replace('/([\\x00-\\x08,\\x0b-\\x0c,\\x0e-\\x19])/', '', $val);
		$search = 'abcdefghijklmnopqrstuvwxyz';
		$search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$search .= '1234567890!@#$%^&*()';
		$search .= '~`";:?+/={}[]-_|\'\\';

		for ($i = 0; $i < strlen($search); $i++) {
			$val = preg_replace('/(&#[xX]0{0,8}' . dechex(ord($search[$i])) . ';?)/i', $search[$i], $val);
			$val = preg_replace('/(&#0{0,8}' . ord($search[$i]) . ';?)/', $search[$i], $val);
		}

		$ra1 = array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
		$ra2 = array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
		$ra = array_merge($ra1, $ra2);
		$found = true;

		while ($found == true) {
			$val_before = $val;

			for ($i = 0; $i < sizeof($ra); $i++) {
				$pattern = '/';

				for ($j = 0; $j < strlen($ra[$i]); $j++) {
					if (0 < $j) {
						$pattern .= '(';
						$pattern .= '(&#[xX]0{0,8}([9ab]);)';
						$pattern .= '|';
						$pattern .= '|(&#0{0,8}([9|10|13]);)';
						$pattern .= ')*';
					}

					$pattern .= $ra[$i][$j];
				}

				$pattern .= '/i';
				$replacement = substr($ra[$i], 0, 2) . '<x>' . substr($ra[$i], 2);
				$val = preg_replace($pattern, $replacement, $val);

				if ($val_before == $val) {
					$found = false;
				}
			}
		}

		return $val;
	}

	static public function _filter_input($data)
	{
		if (is_array($data)) {
			foreach ($data as $key => $v ) {
				$data[$key] = self::_filter_input($data[$key]);
			}
		}
		else if (strlen($data)) {
			$data = self::_RemoveXSS($data);
		}
		else {
			$data = $data;
		}

		return $data;
	}

	static public function _filter_crlf($url)
	{
		$url = trim($url);
		$url = strip_tags($url, '');
		$url = str_replace("\n", '', str_replace(' ', '', $url));
		$url = str_replace("\t", '', $url);
		$url = str_replace("\r\n", '', $url);
		$url = str_replace("\r", '', $url);
		$url = str_replace('"', '', $url);
		$url = trim($url);
		return $url;
	}

	static public function _isInternalUrl($url)
	{
		if (strpos($url, 'http') !== false) {
			if (strpos($url, kernel::base_url(1)) === 0) {
				return true;
			}
		}

		return false;
	}
}


?>
