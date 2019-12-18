<?php

namespace Sumedia\GFont\Admin\View;

use Sumedia\GFont\Base\Registry;
use Sumedia\GFont\Base\View;
use function Sumedia\GFont\ds;

class Overview extends View
{
    /**
     * @var Heading
     */
    protected $heading_view;

    /**
     * @var Overview
     */
    protected $content_view;

    public function __construct()
    {
        $this->set_template(ds(SUMEDIA_GFONT_PLUGIN_PATH . '/templates/admin/overview.phtml'));
        $this->set_heading_view(Registry::get('Sumedia\GFont\Admin\View\Heading'));
        $this->set_content_view(Registry::get('Sumedia\GFont\Admin\View\Fontlist'));
    }

    /**
     * @return Heading
     */
    public function get_heading_view()
    {
        return $this->heading_view;
    }

    /**
     * @param Heading $heading_view
     */
    public function set_heading_view($heading_view)
    {
        $this->heading_view = $heading_view;
    }

    /**
     * @return Overview
     */
    public function get_content_view()
    {
        return $this->content_view;
    }

    /**
     * @param Overview $content_view
     */
    public function set_content_view($content_view)
    {
        $this->content_view = $content_view;
    }
}