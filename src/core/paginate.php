<?php
namespace Core;

/**
 *	Paginate
 * 	Copyright (c) 2018 Grzegorz Miskiewicz
 *	All Rights Reserved
 *
 *	@package Avatec CMS
 *
 *	Usage in model/class:
 *	Paginate::$query = "SELECT * FROM my_table ORDER BY id DESC"; // define mysql query
 *	Paginate::$perpage = 20; // set per page
 *	return Paginate::make(); // generates paginate_footer
 *
 *	Usage in view:
 *	{Paginate::get()} to view footer
 *
 */

class Paginate
{
    public static $query;
    public static $perpage = 15;

    public static $page;
    public static $max_pages;

    public static $website_html = [];
    public static $admin_html = [];

    public function __construct()
    {
        if (!empty($_SESSION['paginate_footer'])) {
            unset($_SESSION['paginate_footer']);
        }
    }

    public static function get_next_page()
    {
        if (!empty(self::$page)) {
            return ++self::$page;
        }
    }

    public static function makeUsingData( $data, $pages_all )
    {
        if( empty( $data )) {
            return;
        }

        global $config, $request, $app_request_url;
        $current = (!empty( $request->get['page'] ) ? $request->get['page'] : 1);

        if (!empty($config['service_perpage'])) {
            self::$perpage = $config['service_perpage'];
        }

        if( self::$perpage > 0 ) {
            $pages_num = ceil($pages_all / self::$perpage);
            if( $pages_num < 1 ) {
                $pages_num = 1;
            }
        }

        if( isset( $current ) && $current > 0 ) {
            self::$page = $current;
            $index_start = (self::$page * self::$perpage) - self::$perpage;
        } else {
            self::$page = 1;
            $index_start = 0;
        }

        $qs = preg_replace("!page=([0-9]+)!" , "" , $_SERVER['QUERY_STRING']);
        $qs = preg_replace("!page=([0-9]+)[&]!" , "" , $qs);
        $url = str_replace("?" . $_SERVER['QUERY_STRING'], "" , $app_request_url);
        $url = $url . (!empty($qs) ? "?" . $qs . "&" : "?");

        self::footer($pages_num, $url, $pages_all);

        return array_slice( $data, $index_start, self::$perpage );
    }

    public static function make()
    {
        global $config, $request, $request, $app_request_url;

        if (!empty(self::$query)) {
            $count = Db::query(self::$query);
            $count = (is_array($count) ? count($count) : 0);
        } else {
            trigger_error("Paginate::make requires self::\$query to be defined");
        }

        if (!empty($config['service_perpage'])) {
            self::$perpage = $config['service_perpage'];
        }

        if (empty(self::$page)) {
            if (isset($request->get['page'])) {
                self::$page = $request->get['page'];
            } else {
                self::$page = 1;
            }
        }

        $pages = ceil($count/self::$perpage);
        if ($pages == 0) {
            $pages = 1;
        }

        if (empty(self::$page)) {
            $page_start = 0;
            $page_selected = 1;
        } else {
            $page_start = (self::$page * self::$perpage) - self::$perpage;
        }

        $qs = preg_replace("!page=([0-9]+)!" , "" , $_SERVER['QUERY_STRING']);
        $qs = preg_replace("!page=([0-9]+)[&]!" , "" , $qs);
        $url = str_replace("?" . $_SERVER['QUERY_STRING'], "" , $app_request_url);
        $url = $url . (!empty($qs) ? "?" . $qs . "&" : "?");

        self::footer($pages, $url, $pages);

        return Db::query(self::$query . " LIMIT " . $page_start . "," . self::$perpage);
    }

    public static function footer($pages = 20, $url = null, $max_page = null)
    {
        global $route;

        if (is_null($url) or is_null($max_page)) {
            return false;
        }

        self::$max_pages = $max_page;

        $prev_page = $url . 'page=' . ((self::$page==1) ? '1' : self::$page-1);
        $next_page = $url . 'page=' . ((self::$page>=$max_page) ? $max_page : self::$page+1);

        if (self::$page == 1) {
            $dis1 = ' disabled';
        }
        if (self::$page == $max_page) {
            $dis2 = ' disabled';
        }

        $html = '';

        self::$website_html[] = '<div class="row justify-content-between mt-4"><div class="col-auto"><span class="order-pages">Strona ' . self::$page . ' z ' . $max_page . '</span></div>';
        self::$website_html[] = '<div class="col-auto"><nav aria-label="Page navigation example"><ul class="pagination">';

        if (self::$page > 1) {
            self::$website_html[] = '<li class="page-item"><a class="page-link" href="' . $prev_page . '" aria-label="Previous"><span class="fal fa-chevron-double-left"></span></a></li>';
        }

        if ($max_page > 1) {
            $html .= '<ul class="pagination">';

            if ($route->isBackend == false) {
                @$html .= '<li class="page-item page-prev'.$dis1.'">'.(empty($dis1) ? '<a class="page-link" href="'.$prev_page.'"><span class="fal fa-angle-double-left"></span></a>' : '<a class="page-link"><span class="fal fa-angle-double-left"></span></a>').'</li>';
            } else {
                @$html .= '<li class="page-item page-prev'.$dis1.'">'.(empty($dis1) ? '<a class="page-link" href="'.$prev_page.'"><span class="fa fa-angle-double-left"></span></a>' : '<a class="page-link"><span class="fa fa-angle-double-left"></span></a>').'</li>';
            }
            for ($i=1;$i<=$pages;$i++) {
                if ($i == self::$page) {
                    self::$website_html[] = '<li class="page-item active"><a class="page-link" href="#">' . $i . '</a></li>';
                    $html .= '<li class="page-item active"><a class="page-link">'.$i.'</a></li>';
                } else {
                    self::$website_html[] = '<li class="page-item"><a class="page-link" href="'.$url . 'page=' . $i .'">' . $i . '</a></li>';
                    $html .= '<li class="page-item"><a class="page-link" href="'.$url . 'page=' . $i .'">'.$i.'</a></li>';
                }
            }
            if (isset($dis1)) {
                unset($dis1);
            }
            if ($route->isBackend == false) {
                $html .= '<li class="page-item page-next'.(!empty($dis2) ? $dis2: '').'">'.(empty($dis2) ? '<a class="page-link" href="'.$next_page.'"><span class="fal fa-angle-double-right"></span></a>' : '<a class="page-link"><span class="fal fa-angle-double-right"></span></a>').'</li>';
            } else {
                $html .= '<li class="page-item page-next'.(!empty($dis2) ? $dis2: '').'">'.(empty($dis2) ? '<a class="page-link" href="'.$next_page.'"><span class="fa fa-angle-double-right"></span></a>' : '<a class="page-link"><span class="fa fa-angle-double-right"></span></a>').'</li>';
            }

            $html .= '</ul>';
        }

        if (!empty(self::$page < $max_page)) {
            self::$website_html[] = '<li class="page-item"><a class="page-link" href="' . $next_page . '" aria-label="Next"><span class="fal fa-chevron-double-right"></span></a></li>';
        }

        self::$website_html[] = '</ul></nav></div></div>';

        self::set($html);
        if ($pages>1) {
        }
    }

    public static function set($html)
    {
        $_SESSION['paginate_footer'] = $html;
    }

    public static function get($website = false)
    {
        if ($website == false) {
            if (isset($_SESSION['paginate_footer'])) {
                return $_SESSION['paginate_footer'];
            }
        }
        return implode(self::$website_html);
    }

    public static function clear()
    {
        if (!empty($_SESSION['paginate_footer'])) {
            unset($_SESSION['paginate_footer']);
        }
    }
}
