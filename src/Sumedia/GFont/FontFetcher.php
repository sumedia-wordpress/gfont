<?php

namespace Sumedia\GFont;

class FontFetcher
{
    protected $font_dir;

    public function __construct()
    {
        $this->font_dir = wp_get_upload_dir()['basedir'] . DS . SUMEDIA_GFONT_PLUGIN_NAME;
        if (!file_exists($this->font_dir)) {
            mkdir($this->font_dir, 0777, true);
        }
    }

    /**
     * Only Google Domain is allowed in order to not get bad files
     *
     * @param string $url
     * @return bool
     */
    public function is_valid_url($url)
    {
        $parsed = parse_url($url);
        if (!isset($parsed['host']) || $parsed['host'] != 'fonts.googleapis.com') {
            return false;
        }
        if (!isset($parsed['query']) || empty($parsed['query'])) {
            return false;
        }
        return true;
    }

    public function fetch($google_url, $font_name)
    {
        // only allow right domain from google
        if (!$this->is_valid_url($google_url)) {
            return;
        }

        $css_styles = $this->fetch_styles($google_url);

        $files = array_merge(
            $this->fetch_webfont_urls($google_url, 'Mozilla/4.0 (compatible; MSIE 5.01; "."Windows NT 5.0'),
            $this->fetch_webfont_urls($google_url, 'Opera/9.63 (Macintosh; Intel Mac OS X; U; en) Presto/2.1.1'),
            $this->fetch_webfont_urls($google_url, 'Mozilla/5.0 (iPhone; CPU iPhone OS 10_3_1 like Mac OS X) AppleWebKit/603.1.30 (KHTML, like Gecko) Version/10.0 Mobile/14E304 Safari/602.1'),
            $this->fetch_webfont_urls($google_url, 'Mozilla/5.0 (compatible; MSIE 9.0; Windows Phone OS 7.5; Trident/5.0; IEMobile/9.0)')
        );

        $dir = $this->font_dir . ds('/webfonts/' . $font_name);
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }

        foreach ($files as $ext => $file) {
            copy($file, $dir . DS . $font_name . '.' . $ext);
        }

        // generated local content, no extern or input involved
        $css_content = $this->get_css_content($font_name, $css_styles);

        file_put_contents($dir . '/' . $font_name . '.css', $css_content);
    }

    public function fetch_styles($google_url)
    {
        // only allow right domain from google
        if (!$this->is_valid_url($google_url)){
            return;
        }

        $content = wp_remote_get($google_url);
        if (!isset($content['body']) || empty($content['body'])) {
            return;
        }
        if (!preg_match_all('#^ +(.*?):(.*?);$#ims', $content['body'], $matches)) {
            return;
        }

        return array_combine($matches[1], $matches[2]);
    }

    public function fetch_webfont_urls($google_url, $useragent)
    {
        // only allow right domain from google
        if (!$this->is_valid_url($google_url)){
            return;
        }

        $files = [];

        $content = wp_remote_get($google_url,[
            'user-agent' => $useragent
        ]);

        if (!isset($content['body']) || empty($content['body'])) {
            return;
        }

        if (!preg_match_all('#url\((.*?)\)#ims', $content['body'], $matches)) {
            return;
        }

        foreach ($matches[1] as $file) {
            $files[substr($file, strrpos($file, '.')+1)] = $file;
        }

        return $files;
    }

    public function get_css_content($font_name, $css_styles)
    {
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

        $style .= 'src: url(\'' . $font_name . '.eot?#iefix\') format(\'embedded-opentype\'),';
        $style .= 'url(\'' . $font_name . '.woff\') format(\'woff\'),';
        $style .= 'url(\'' . $font_name . '.woff2\') format(\'woff2\'),';
        $style .= 'url(\'' . $font_name . '.ttf\')  format(\'truetype\');';
        $style .= '}';
        $style .= '.' . $font_name . ' { font-family: ' . $font_name . '; }';

        return $style;
    }
}
