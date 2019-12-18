<?php

namespace Sumedia\GFont\Admin\Form;

class NewFont extends \Sumedia\GFont\Base\Form
{
    /**
     * @param array $request_data
     * @return bool
     */
    public function is_valid_data(array $request_data)
    {
        $valid = true;
        $messenger = \Sumedia\GFont\Base\Messenger::get_instance();

        if (!isset($request_data['_wpnonce']) || !wp_verify_nonce($request_data['_wpnonce'])){
            $messenger->add_message($messenger::TYPE_ERROR, __('The form could not be verified, please try again', SUMEDIA_GFONT_PLUGIN_NAME));
            $valid = false;
        }

        if (!isset($request_data['google_url'])) {
            $messenger->add_message($messenger::TYPE_ERROR, sprintf(__('Missing parameter: %s.', SUMEDIA_GFONT_PLUGIN_NAME), 'google_url'));
            $valid = false;
        }

        if (!$this->is_valid_url($request_data['google_url'])) {
            $messenger->add_message($messenger::TYPE_ERROR, __('The given Google URL seems not to be valid.', SUMEDIA_GFONT_PLUGIN_NAME));
            $valid = false;
        }

        if (!isset($request_data['font_name'])) {
            $messenger->add_message($messenger::TYPE_ERROR, sprintf(__('Missing parameter: %s.', SUMEDIA_GFONT_PLUGIN_NAME), 'font_name'));
            $valid = false;
        }

        if (!preg_match('#^[a-z0-9.\-]+$#i', $request_data['font_name'])){
            $messenger->add_message($messenger::TYPE_ERROR, __('Invalid Font Name', SUMEDIA_GFONT_PLUGIN_NAME));
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

    /**
     * @param array $request_data
     * @return bool
     */
    public function is_valid(array $request_data)
    {
        if ($this->is_valid_data($request_data)) {
            $this->set_data([
                'google_url' => $request_data['google_url'],
                'font_name' => $request_data['font_name']
            ]);
            return true;
        }
        return false;
    }
}
