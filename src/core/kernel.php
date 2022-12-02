<?php
namespace Core;

use Core\Error;
use Core\Request;
use Modules\Admins\Backend\Admins;

class Kernel
{
    public static $tpl;
    public static $css;
    public static $js;
    public static $token;

    protected static $messages;

    public static $meta_title;
    public static $meta_description;
    public static $meta_keywords;
    public static $meta_index = false;
    public static $meta_follow = false;


    public static function changeLog()
    {
        global $app_path;
        if (file_exists($app_path . "changelog.txt")) {
            $content = file_get_contents($app_path . "changelog.txt");
            return nl2br($content);
        }
    }

    protected static $components;
    protected static function hasComponentID($id, $name)
    {
        if (empty(self::$components[$id]['id'])) {
            return false;
        }

        $key = array_search($id, array_column(self::$components, 'id'));
        if (!empty($key)) {
            Error::show(
                'RegisterComponent Duplicate found #' . $id,
                'You are trying to registerComponent to existing ID in ' . $name . '<br/>but it has been registered in: ' . self::$components[$key]['name']
            );
            exit;
        }
    }

    public static function registerComponent($id, $name, $file)
    {
        if (empty(self::$components)) {
            self::$components[] = [
                'id' => $id,
                'name' => $name,
                'file' => (is_null($file) ? null : $file)
            ];
            return true;
        }

        foreach (self::$components as $item) {
            if ($item['id'] == $id) {
                continue;
            }
        }

        self::$components[] = [
            'id' => $id,
            'name' => $name,
            'file' => (is_null($file) ? null : $file)
        ];
    }

    public static function getComponentID($name)
    {
        $component_id = null;

        if (!empty(self::$components)) {
            foreach (self::$components as $k=>$i) {
                if ($i['file'] == $name) {
                    $component_id = $i['id'];
                }
            }
        }

        return $component_id;
    }

    public static function readComponents($id = null)
    {
        if (!empty(Form::$post['component'])) {
            foreach (self::$components as $k=>$i) {
                if (Form::$post['component'] == $i['file'] || str_replace(";", "", Form::$post['component'] == $i['file'])) {
                    self::$components[$k]['selected'] = true;
                }
            }

            sort(self::$components);
        }

        if (is_null($id)) {
            if (!empty(self::$components)) {
                return self::$components;
            }
        } else {
            foreach (self::$components as $item) {
                if ($item['id'] == $id) {
                    return $item['file'];
                }
            }
        }
    }

    public static function html_decode($string)
    {
        return html_entity_decode(html_entity_decode($string));
    }

    public static function addPath($o = null)
    {
        if (!empty($o)) {
            self::$tpl['path'][] = array(
                'name' => (isset($o['name']) ? $o['name'] : 'brak danych'),
                'url' => (isset($o['url']) ? $o['url'] : false),
                'main' => (isset($o['main']) ? $o['main'] : false)
            );
        }
    }

    public static function viewPath()
    {
        if (!empty(self::$tpl['path'])) {
            $html[] = '<ol class="breadcrumb mb-0 px-0">';
            foreach (self::$tpl['path'] as $key=>$item) {
                if ($item['main'] == true) {
                    $html[] = '<li class="breadcrumb-item active"><a>'.$item['name'].'</a></li>';
                } else {
                    $html[] = '<li class="breadcrumb-item"><a href="'.$item['url'].'">'.$item['name'].'</a></li>';
                }


                unset($classAdd);
            }
            $html[] = '</ol>';
            return implode($html);
        }
    }

    public static function addMeta($title, $description = null, $keywords = null, $index = false, $follow = false)
    {
        self::$meta_title = $title;
        self::$meta_description = (!empty($description) ? $description : $title);
        self::$meta_keywords = (!empty($keywords) ? $keywords : null);
        self::$meta_index = (!empty($index) ? $index : true);
        self::$meta_follow = (!empty($follow) ? $follow : true);
    }

    public static function getMeta()
    {
        if (self::$meta_index == true || self::$meta_index == "TRUE") {
            self::$meta_index = 'index';
        } else {
            self::$meta_index = 'noindex';
        }

        if (self::$meta_follow == true || self::$meta_follow == "TRUE") {
            self::$meta_follow = 'follow';
        } else {
            self::$meta_follow = 'nofollow';
        }

        return array(
            "title" => self::$meta_title,
            "description" => self::$meta_description,
            "keywords" => self::$meta_keywords,
            "robots" => ((self::$meta_index == true) ? "index" : "noindex") . "," . ((self::$meta_follow == true) ? "follow" : "nofollow"),
            "index" => self::$meta_index,
            "follow" => self::$meta_follow
        );
    }

    public static function setMessage($type = "NOTICE", $text = '', $ErrorsArray = null)
    {
        if (empty($ErrorsArray)) {
            self::$messages[$type][] = $text;
            self::storeMessage();
            return;
        }

        if (is_array($ErrorsArray)) {
            self::$messages[$type][] = $text . '<ul><li>' . implode('</li><li>', $ErrorsArray) . '</li></ul>';
        } else {
            self::$messages[$type][] = $text . '<ul><li>' . $ErrorsArray . '</li></ul>';
        }

        self::storeMessage();
    }

    public static function getMessage($type = "NOTICE")
    {
        self::restoreMessage();
        if (isset(self::$messages[$type])) {
            if (is_array(self::$messages[$type])) {
                return self::$messages[$type];
            } else {
                return self::$messages[$type];
            }
        }
    }

    private static function storeMessage()
    {
        $_SESSION['admin_message'] = self::$messages;
    }

    private static function restoreMessage()
    {
        if (!empty($_SESSION['admin_message'])) {
            self::$messages = $_SESSION['admin_message'];
        }
    }

    public static function clearMessages()
    {
        if (!empty($_SESSION['admin_message'])) {
            unset($_SESSION['admin_message']);
        }
    }

    public static function module($name)
    {
        self::$tpl['module'] = $name;
    }

    public static function template($file)
    {
        self::$tpl['file'] = $file;
    }

    public static function schema($file)
    {
        self::$tpl['schema'] = $file;
    }

    public static function get_view()
    {
        global $app_path;

        if (!empty(self::$tpl['module']) && !empty(self::$tpl['file'])) {
            return $app_path . 'modules/' . self::$tpl['module'] . '/frontend/views/' .  self::$tpl['file'];
        }
    }

    public static function getTpl()
    {
        if (is_array(self::$tpl)) {
            return self::$tpl;
        }
    }

    public static function rewrite($text)
    {
        $string = strtolower($text);
        $polskie = array(',', ' - ',' ','ę', 'Ę', 'ó', 'Ó', 'Ą', 'ą', 'Ś', 's', 'ł', 'Ł', 'ż', 'Ż', 'Ź', 'ź', 'ć', 'Ć', 'ń', 'Ń','-',"'","/","?", '"', ":", 'ś', '!','.', '&', '&amp;', '#', ';', '[',']','domena.pl', '(', ')', '`', '%', '”', '„', '…');
        $miedzyn = array('-','-','-','e', 'e', 'o', 'o', 'a', 'a', 's', 's', 'l', 'l', 'z', 'z', 'z', 'z', 'c', 'c', 'n', 'n','-',"","","","","",'s','','', '', '', '', '', '', '', '', '', '', '', '', '');
        $string = str_replace($polskie, $miedzyn, $string);

        $string = preg_replace('/[\-]+/', '-', $string);
        $string = trim($string, '-');
        $string = stripslashes($string);
        $string = urlencode($string);

        $encoded = array(
            "%E4%98","%E4%99","%E3%B3","%E3%93","%E4%85","%E4%84",
            "%E5%9B","%E5%9A","%E5%82","%E5%81","%E5%BE","%E5%BB",
            "%E5%BA","%E5%B9","%E4%87","%E4%86","%E5%84","%E5%83"
        );
        $new = array(
            "e","e","o","o","a","a",
            "s","s","l","l","z","z",
            "z","z","c","c","n","n"
        );

        $string = str_replace($encoded, $new, $string);

        if (strlen($string > 50)) {
            return substr($string, 0, 50);
        } else {
            return $string;
        }
    }

    public static function callModule($module, $command, $options = null)
    {
        global $app_path;
        $check = explode(".", $module);
        if (count($check) > 1) {
            $module = $check['0'];
            $file = $check['1'];
        }

        $url = $app_path . "modules/" . $module . "/" . (isset($file) ? $file : $module) . ".website.php";
        if (file_exists($url)) {
            include $url;
            return true;
        } else {
            throw new Exception('File ' . $url . ' not found');
        }
    }

    public static function log($filename, $data)
    {
        global $app_path;

        $now = date('Y-m-d');

        if (is_dir($app_path . 'logs/') == false) {
            @mkdir($app_path . 'logs/');
        }

        if( !empty( $now )) {
            if (is_dir($app_path . "logs/" . $now . "/") == false) {
                @mkdir($app_path . "logs/" . $now . "/");
            }

            file_put_contents($app_path . "logs/" . $now . '/' . $filename, "[".date('Y-m-d H:i:s')."] " . $data . "\r\n", FILE_APPEND);
        }
    }

    public static function access($module, $account = null)
    {
        global $app_admin_url;

        if (is_null($account)) {
            $account = ['-1','1'];
        }

        if ((strstr(Admins::$auth['access'], $module) != true) && (in_array(Admins::$auth['type'], $account) !== true)) {
            Kernel::setMessage("ERROR", "Dostęp zabroniony dla Twojego konta");
            Request::redirect($app_admin_url . "start.html");
        } else {
            return true;
        }
    }

    public static function loadClass($file)
    {
        global $app_path;
        if (file_exists($app_path . $file)) {
            require_once $app_path . $file;
        }
    }

    public static function getCss()
    {
        if (isset(self::$css)) {
            return self::$css;
        } else {
            return false;
        }
    }

    public static function setCss($file, $path = false, $admin = false)
    {
        global $app_url;

        if ($path == false) {
            self::$css[$file] = $app_url . 'templates/' . ($admin == true ? 'admin/' : 'website/') . 'css/' . $file;
        } else {
            self::$css[$file] = $app_url . 'modules/' . $path . '/css/' . $file;
        }
    }

    public static function setExternalCss($name, $link)
    {
        self::$css[$name] = $link;
    }

    public static function getJs()
    {
        if (isset(self::$js)) {
            return self::$js;
        } else {
            return false;
        }
    }

    public static function setJs($file, $path = false, $admin = false)
    {
        global $app_url;

        if ($path == false) {
            self::$js[$file] = $app_url . 'templates/' . ($admin == true ? 'admin/' : 'website/') . 'js/' . $file;
        } else {
            self::$js[$file] = $app_url . 'modules/' . $path . '/js/' . $file;
        }
    }

    public static function setExternalJs($name, $link)
    {
        self::$js[$name] = $link;
    }

    public static function generateToken($strenght)
    {
        global $config, $request;

        if (!$request->post) {
            self::$token = md5(uniqid($config['salt'] . "|" . time(), true));
            $_SESSION['token'] = self::$token;
        }

        if (empty($_SESSION['token'])) {
            self::$token = md5(uniqid($config['salt'] . "|" . time(), true));
            $_SESSION['token'] = self::$token;
        } else {
            self::$token = $_SESSION['token'];
        }

        return self::$token;
    }

    public static function real_escape($value)
    {
        $search = array("\\",  "\x00", "\n",  "\r",  "'",  '"', "\x1a");
        $replace = array("\\\\","\\0","\\n", "\\r", "\'", '\"', "\\Z");

        return str_replace($search, $replace, $value);
    }

    public static function json_encode($string)
    {
        $json = json_encode($string, JSON_UNESCAPED_UNICODE | JSON_ERROR_UTF8);
        $json = str_replace("\r", "", $json);
        $json = str_replace("\n", "", $json);
        $json = trim(preg_replace('/\s\s+/', ' ', $json));
        return $json;
    }

    public static function SummerNote($id, $o = null)
    {
        if (empty($o['height'])) {
            $o['height'] = 250;
        }
        $html[] = '<script type="text/javascript">$(document).ready(function() {';
        if (!is_array($id)) {
            $html[] = '$("' . $id . '").summernote({height: ' . $o['height'] . ',lang: \'pl-PL\',popover: {image: [' .
                '[\'imagesize\', [\'imageSize100\',\'imageSize50\',\'imageSize25\']],' .
                '[\'float\', [\'floatLeft\',\'floatRight\',\'floatNone\']],[\'remove\', [\'removeMedia\']],[\'custom\', [\'imageTitle\']],' .
                ']},callbacks: { onImageUpload: function( file ) {' .
                    'summernote_uploader( file, "' . $id . '" );},
					onMediaDelete: function(target) { summernote_delete_file(target[0].src); }' .
                '}});';
        }
        $html[] = '});</script>';

        return implode($html);
    }
}
