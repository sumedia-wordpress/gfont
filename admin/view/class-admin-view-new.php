<?php

class Sumedia_GFont_Admin_View_New extends Sumedia_Base_View
{
    public function __construct()
    {
        $this->set_template(SUMEDIA_PLUGIN_PATH . SUMEDIA_GFONT_PLUGIN_NAME . Suma\ds('/admin/templates/new.phtml'));
    }

}