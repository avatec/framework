<?php namespace Core;

class Cache
{
	private static $expire = 600;

	public static function get($key)
    {
		global $app_path, $cache;
		if( $cache == false ) {
			return false;
		}

		$files = glob($app_path . 'cache/cache.' . preg_replace('/[^A-Z0-9\._-]/i', '', $key) . '.*');

		if ($files) {
			$cache = file_get_contents($files[0]);

			$data = unserialize($cache);

			foreach ($files as $file) {
				$time = substr(strrchr($file, '.'), 1);

      			if ($time < time()) {
					if (file_exists($file)) {
						unlink($file);
					}
      			}
    		}

			return $data;
		}
	}

  	public static function set($key, $value)
    {
  		global $app_path;

        self::delete($key);

		$file = $app_path . 'cache/cache.' . preg_replace('/[^A-Z0-9\._-]/i', '', $key) . '.' . (time() + self::$expire);

		$handle = fopen($file, 'w');

    	fwrite($handle, serialize($value));
    	fclose($handle);
  	}

  	public static function delete($key)
    {
  		global $app_path;
		$files = glob($app_path . 'cache/cache.' . preg_replace('/[^A-Z0-9\._-]/i', '', $key) . '.*');

		if (!empty($files)) {
    		foreach ($files as $file) {
      			if (file_exists($file)) {
					unlink($file);
				}
    		}
		}
  	}
}
