<?php

class Sumedia_GFont_Admin_Controller_New extends Sumedia_Base_Controller
{
    /**
     * @var $this
     */
    protected static $instance;

    public function prepare()
    {
        $overview = Sumedia_Base_Registry_View::get('Sumedia_Base_Admin_View_Overview');
        $overview->set_content_view(Sumedia_Base_Registry_View::get('Sumedia_GFont_Admin_View_New'));

        $heading = Sumedia_Base_Registry_View::get('Sumedia_Base_Admin_View_Heading');
        $heading->set_title(__('Google Fonts', SUMEDIA_GFONT_PLUGIN_NAME));
        $heading->set_side_title(__('Create new', SUMEDIA_GFONT_PLUGIN_NAME));
        $heading->set_version(SUMEDIA_GFONT_VERSION);
        $heading->set_options([
            [
                'name' => __('Back to the plugin overview'),
                'url' => admin_url('admin.php?page=sumedia')
            ],
            [
                'name' => __('Back to the google fontlist'),
                'url' => admin_url('admin.php?page=sumedia&plugin=' . SUMEDIA_GFONT_PLUGIN_NAME . '&action=fontlist')
            ]
        ]);
    }

    public function execute()
    {
        $form = Sumedia_Base_Registry_Form::get('Sumedia_GFont_Admin_Form_New');
        if (!empty($_POST) && $form->is_valid($_POST)) {
            $url = $form->get_data('google_url');
            $name = $form->get_data('font_name');
            $fetcher = new Sumedia_GFont_Font_Fetcher();
            $fetcher->fetch($url, $name);
            $messenger = Sumedia_Base_Messenger::get_instance();
            $messenger->add_message($messenger::TYPE_SUCCESS, __('The font has been fetched from google.', SUMEDIA_GFONT_PLUGIN_NAME));

            $fonts = Sumedia_GFont_Repository_Fonts::get_instance();
            $fonts->create([
                'google_url' => $form->get_data('google_url'),
                'fontname' => $form->get_data('font_name'),
                'use_flag' => 1
            ]);

            wp_redirect('admin.php?page=sumedia&plugin=' . SUMEDIA_GFONT_PLUGIN_NAME . '&action=fontlist');
        }
    }
}