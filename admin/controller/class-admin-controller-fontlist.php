<?php

class Sumedia_GFont_Admin_Controller_Fontlist extends Sumedia_Base_Controller
{
    /**
     * @var $this
     */
    protected static $instance;

    public function prepare()
    {
        $overview = Sumedia_Base_Registry_View::get('Sumedia_Base_Admin_View_Overview');
        $overview->set_content_view(Sumedia_Base_Registry_View::get('Sumedia_GFont_Admin_View_Fontlist'));

        $heading = Sumedia_Base_Registry_View::get('Sumedia_Base_Admin_View_Heading');
        $heading->set_title(__('Google Fonts', SUMEDIA_GFONT_PLUGIN_NAME));
        $heading->set_side_title(__('Configuration', SUMEDIA_GFONT_PLUGIN_NAME));
        $heading->set_version(SUMEDIA_GFONT_VERSION);
        $heading->set_options([
            [
                'name' => __('Back to the plugin overview'),
                'url' => admin_url('admin.php?page=sumedia')
            ]
        ]);
    }

    public function execute()
    {

    }
}