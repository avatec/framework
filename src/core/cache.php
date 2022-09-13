<?php namespace Core;

/**
 * Cache oparty na plikach
 *
 * Cache::setExpire(5)->set('nazwa' , $tablica);
 */
class Cache
{
    private static $expire = 600;

/**
 * Ustawia czas ważności danego cache
 * @param int $minutes
 */
    public static function setExpire( int $minutes )
    {
        self::$expire = $minutes * 60;
        return new static;
    }

/**
 * Zwraca informacje z cache na podstawie nazwy
 * @param  string $key
 * @return mixed
 */
    public static function get( string $key )
    {
        global $app_path, $cache;
        if ($cache == false) {
            return false;
        }

        $files = glob($app_path . 'cache/cache.' . preg_replace('#[^A-Z0-9\._-]#i', '', $key) . '.*');

        if (!empty($files)) {
            $cache = file_get_contents($files[0]);

            $data = unserialize($cache);

            foreach ($files as $file) {
                $time = substr(strrchr($file, '.'), 1);

                if ($time < time() && file_exists($file)) {
                    unlink($file);
                }
            }

            return $data;
        }
    }

/**
 * Zapisuje dane w cache
 * @param string $key
 * @param mixed $value
 */
    public static function set( string $key, $value ): void
    {
        global $app_path;
        self::delete($key);

        $file = $app_path . 'cache/cache.' . preg_replace('#[^A-Z0-9\._-]#i', '', $key) . '.' . (time() + self::$expire);

        $handle = fopen($file, 'w');

        fwrite($handle, serialize($value));

        fclose($handle);
    }

/**
 * Usunięcie cache
 * @param  string $key
 */
    public static function delete( string $key ): void
    {
        global $app_path;
        $files = glob($app_path . 'cache/cache.' . preg_replace('#[^A-Z0-9\._-]#i', '', $key) . '.*');

        if ($files) {
            foreach ($files as $file) {
                if (file_exists($file)) {
                    unlink($file);
                }
            }
        }
    }
}
