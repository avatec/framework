<?php
namespace Core;

use Core\Language;

class Route
{
    public $num = 0;
    public $path = '/';
    public $path_array = [];
    public $paths = [];
    public $results = array();

    public $error_code = null;
    public $isBackend 	= false;
    // Jeżeli jest to główna strona
    public $isMain		= false;
    public $isApi       = false;
    public $isCron 		= false;
    public $isError 	= false;
    public $isUpload 	= false;
    public $isOAuth2 	= false;

    public $isLanguage 	= true;
    public $language 	= 'pl';
    public $asCatalog	= false;
    public $catalog 	= '/';

    protected $replace_pattern = [
        ':id' => '([\d]+)',
        ':string' => '([^/]+)',
        ':file' => '([\w\s-]+\.[A-Za-z]+)',
        ':any' => '(.+)'
    ];

    private $config;
    public function __construct( $config )
    {
        $this->config = $config;

        $this->get_path();
    }

    public function get_path()
    {
        $this->path = $_SERVER['REQUEST_URI'];
        $this->path = str_replace("?" . $_SERVER['QUERY_STRING'], "", $this->path);
        $this->path_array = array_filter(explode("/", $this->path));
        $this->path_array = array_values($this->path_array);

        if (!empty($this->path_array[0]) && in_array($this->path_array[0], array_keys(Language::$available)) == true) {
            $this->language = $this->path_array[0];
            Language::change($this->language);
            unset($this->path_array[0]);
            $this->path_array = array_values($this->path_array);
            $this->path = implode("/", $this->path_array);
        }

        if (empty($this->path_array[0])) {
            $check_path = '/';
        } else {
            if ($this->path_array[0] == $this->catalog) {
                $check_path = (!empty($this->path_array[1]) ? $this->path_array[1] : '/');
            } else {
                $check_path = $this->path_array[0];
            }
        }

        $this->num = count($this->path_array);
        global $backendFolder;
        if ($check_path == $backendFolder) {
            $this->isBackend = true;
        }

        switch ($check_path) {
            case "/":
                $this->isMain = true;
            break;

            case "cron":
                $this->isCron = true;
            break;

            case "api":
                $this->isApi = true;
            break;

            case "upload":
                $this->isUpload = true;
            break;

            case "oauth":
                $this->isOAuth2 = true;
            break;

            case "error":
                $this->isError = true;
                $this->error_code = end($this->path_array);
            break;
        }
    }

    protected function parse($regexp)
    {
        return str_replace(array_keys($this->replace_pattern), array_values($this->replace_pattern), $regexp);
    }

    public function get($regexp, $option)
    {
        if (in_array($regexp, $this->paths) !== false) {
            //Kernel::log('routing.log', $this->path . ' has been defined before ' . __FILE__);
            return;
        } else {
            $this->paths[] = $regexp;
        }

        preg_match('#^/?' . $this->parse($regexp) . '/?$#', $this->path, $m);
        if (empty($m[0])) {
            return;
        }

        if (is_callable($option)) {
            $this->results = call_user_func($option);
        } else {
            $this->results = $option;
        }

        if (!empty($this->results['id'])) {
            if (!is_array($this->results['id'])) {
                if (strpos($this->results['id'], '$') !== false) {
                    $this->results['id'] = $m[str_replace('$', '', $this->results['id'])];
                }
            } else {
                $id = [];
                foreach ($this->results['id'] as $k=>$i) {
                    $this->results['id'][$k] = $m[str_replace('$', '', $i)];
                }
            }
        }

        if (!empty($this->results['command']) && strpos($this->results['command'], '$') !== false) {
            $this->results['command'] = $m[str_replace('$', '', $this->results['command'])];
        }

        if (empty($this->results['file']) && !empty($this->results['module'])) {
            $this->results['file'] = $this->results['module'];
        }

        global $app_path;

        if (!empty($this->results['module'])) {
            if (file_exists($app_path . 'modules/' . $this->results['module'] . '/frontend/' . $this->results['file'] . '.frontend.php') == true) {
                $this->results['url'] = $app_path . 'modules/' . $this->results['module'] . '/frontend/' . $this->results['file'] . '.frontend.php';
            } else {
                $this->results['url'] = $app_path . 'modules/' . $this->results['module'] . '/' . $this->results['file'] . '.website.php';
            }
        }
    }
}
