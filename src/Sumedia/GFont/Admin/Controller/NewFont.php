<?php

namespace Sumedia\GFont\Admin\Controller;

class NewFont extends \Sumedia\GFont\Base\Controller
{
    public function prepare()
    {
        $overview = \Sumedia\GFont\Base\Registry::get('Sumedia\GFont\Admin\View\Overview');
        $overview->set_content_view(\Sumedia\GFont\Base\Registry::get('Sumedia\GFont\Admin\View\NewFont'));

        $heading = \Sumedia\GFont\Base\Registry::get('Sumedia\GFont\Admin\View\Heading');
        $heading->set_title(__('Google Fonts', SUMEDIA_GFONT_PLUGIN_NAME));
        $heading->set_side_title(__('Add new font', SUMEDIA_GFONT_PLUGIN_NAME));
        $heading->set_version(SUMEDIA_GFONT_VERSION);
        $heading->set_options([
            [
                'name' => __('Google font list'),
                'url' => admin_url('admin.php?page=' . SUMEDIA_GFONT_PLUGIN_NAME . '&action=fontlist')
            ]
        ]);
    }

    public function execute()
    {
        $form = \Sumedia\GFont\Base\Registry::get('Sumedia\GFont\Admin\Form\NewFont');
        if (!empty($_POST) && $form->is_valid($_POST)) {
            $url = $form->get_data('google_url');
            $name = $form->get_data('font_name');
            $fetcher = \Sumedia\GFont\Base\Registry::get('Sumedia\GFont\FontFetcher');
            $fetcher->fetch($url, $name);
            $messenger = \Sumedia\GFont\Base\Messenger::get_instance();
            $messenger->add_message($messenger::TYPE_SUCCESS, __('The font has been fetched from google.', SUMEDIA_GFONT_PLUGIN_NAME));

            $fonts = \Sumedia\GFont\Base\Registry::get('Sumedia\GFont\Repository\Fonts');
            $fonts->create([
                'google_url' => $form->get_data('google_url'),
                'fontname' => $form->get_data('font_name'),
                'use_flag' => 1
            ]);

            wp_redirect(admin_url('admin.php?page=' . SUMEDIA_GFONT_PLUGIN_NAME . '&action=fontlist'));
        }
    }
}