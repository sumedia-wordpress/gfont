<?php

namespace Sumedia\GFont\Repository;

use Sumedia\GFont\Base\Repository;

class Fonts extends Repository
{
    /**
     * @return string
     */
    public function get_table_name()
    {
        return 'sumedia_gfont_fonts';
    }

    /**
     * @param array $data
     * @return bool
     */
    public function is_valid_data($data)
    {
        $valid = true;

        if (!isset($data['google_url'])) {
            $valid = false;
        }

        if (!$this->is_valid_url($data['google_url'])) {
            $valid = false;
        }

        if (!isset($data['fontname'])) {
            $valid = false;
        }

        if (!preg_match('#^[a-z0-9.\-]+$#i', $data['fontname'])){
            $valid = false;
        }

        return $valid;
    }

    /**
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
}
