<?php

class Sumedia_GFont_Font_Fetcher
{
    protected $font_dir;

    protected $font_name;

    public function __construct()
    {
        $this->font_dir = SUMEDIA_PLUGIN_PATH . SUMEDIA_GFONT_PLUGIN_NAME . Suma\ds('/data');
        if (!file_exists($this->font_dir)) {
            mkdir($this->font_dir, 0777, true);
        }
    }

    public function fetch($google_url, $font_name)
    {
        $css_styles = $this->fetch_styles($google_url);

        $files = array_merge(
            $this->fetch_webfont_urls($google_url, 'Mozilla/4.0 (compatible; MSIE 5.01; "."Windows NT 5.0'),
            $this->fetch_webfont_urls($google_url, 'Opera/9.63 (Macintosh; Intel Mac OS X; U; en) Presto/2.1.1'),
            $this->fetch_webfont_urls($google_url, 'Mozilla/5.0 (iPhone; CPU iPhone OS 10_3_1 like Mac OS X) AppleWebKit/603.1.30 (KHTML, like Gecko) Version/10.0 Mobile/14E304 Safari/602.1'),
            $this->fetch_webfont_urls($google_url, 'Mozilla/5.0 (compatible; MSIE 9.0; Windows Phone OS 7.5; Trident/5.0; IEMobile/9.0)')
        );

        $dir = $this->font_dir . Suma\ds('/webfonts/' . $font_name);
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }

        foreach ($files as $ext => $file) {
            copy($file, $dir . Suma\DS . $font_name . '.' . $ext);
        }

        $css_content = $this->get_css_content($font_name, $css_styles);

        file_put_contents($dir . '/' . $font_name . '.css', $css_content);
    }

    public function fetch_styles($google_url)
    {
        $content = file_get_contents($google_url);
        if (!preg_match_all('#^ +(.*?):(.*?);$#ims', $content, $matches)) {
            return;
        }

        return array_combine($matches[1], $matches[2]);
    }

    public function fetch_webfont_urls($google_url, $useragent)
    {
        $files = [];

        $curl = curl_init($google_url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_USERAGENT, $useragent);
        curl_setopt($curl, CURLOPT_HTTPGET, 1);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_REFERER, '');
        curl_setopt($curl, CURLOPT_COOKIEJAR, 'cookie.txt');
        $content = curl_exec($curl);

        if (!preg_match_all('#url\((.*?)\)#ims', $content, $matches)) {
            return;
        }

        foreach ($matches[1] as $file) {
            $files[substr($file, strrpos($file, '.')+1)] = $file;
        }

        return $files;
    }

    public function get_css_content($font_name, $css_styles)
    {
        $dir = $this->font_dir . Suma\ds('/webfonts/' . $font_name);
        $style = '@font-face {';
        foreach ($css_styles as $key => $value) {
            if ('src' == $key) {
                continue;
            }
            if ('font-family' == $key) {
                $style .= $key . ':' . $font_name . ';';
            } else {
                $style .= $key . ':' . trim($value) . ';';
            }
        }
        $font_path = SUMEDIA_PLUGIN_URL . SUMEDIA_GFONT_PLUGIN_NAME . '/data/webfonts/' . $font_name . '/' . $font_name;
        $style .= 'src: url(\'' . $font_path . '.eot?#iefix\') format(\'embedded-opentype\'),';
        $style .= 'url(\'' . $font_path . '.woff\') format(\'woff\'),';
        $style .= 'url(\'' . $font_path . '.woff2\') format(\'woff2\'),';
        $style .= 'url(\'' . $font_path . '.ttf\')  format(\'truetype\');';
        $style .= '}';
        $style .= '.' . $font_name . ' { font-family: ' . $font_name . '; }';

        return $style;
    }
}
