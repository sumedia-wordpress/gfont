<?php

namespace Sumedia\GFont\Admin\View;

use Sumedia\GFont\Base\View;
use function Sumedia\GFont\ds;

class NewFont extends View
{
    public function __construct()
    {
        $this->set_template(SUMEDIA_GFONT_PLUGIN_PATH . ds('/templates/admin/newfont.phtml'));
    }

}