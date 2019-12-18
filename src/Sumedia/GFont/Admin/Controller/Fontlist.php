<?php

namespace Sumedia\GFont\Admin\Controller;

class Fontlist
{
    public function prepare()
    {
        $overview = \Sumedia\GFont\Base\Registry::get('Sumedia\GFont\Admin\View\Overview');
        $overview->set_content_view(\Sumedia\GFont\Base\Registry::get('Sumedia\GFont\Admin\View\Fontlist'));

        $heading = \Sumedia\GFont\Base\Registry::get('Sumedia\GFont\Admin\View\Heading');
        $heading->set_title(__('Google Fonts', SUMEDIA_GFONT_PLUGIN_NAME));
        $heading->set_side_title(__('Fontlist', SUMEDIA_GFONT_PLUGIN_NAME));
        $heading->set_version(SUMEDIA_GFONT_VERSION);
    }

    public function execute()
    {
    }
}