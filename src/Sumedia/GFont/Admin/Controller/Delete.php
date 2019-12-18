<?php

namespace Sumedia\GFont\Admin\Controller;

use Sumedia\GFont\Base\Controller;
use Sumedia\GFont\Base\Messenger;
use Sumedia\GFont\Base\Registry;
use const Sumedia\GFont\DS;

class Delete extends Controller
{
    public function execute()
    {
        if (!wp_verify_nonce($_POST['_wpnonce'], 'bulk-plugins_page_sumedia-gfont')) {
            return;
        }

        $fonts = Registry::get('Sumedia\GFont\Repository\Fonts');
        foreach ($_POST['ids'] as $id) {

            if(!is_numeric($id)) {
                continue;
            }

            $data = $fonts->findOne('id', $id);
            if (!$data) {
                continue;
            }

            $font_name = $data['fontname'];

            $font_dir = wp_get_upload_dir()['basedir'] . DS . SUMEDIA_GFONT_PLUGIN_NAME;
            $dir = $font_dir . DS . 'webfonts' . DS . $font_name;
            $filelist = glob($dir. DS . '*');
            foreach ($filelist as $file) {
                unlink($file);
            }
            rmdir($dir);

            $fonts->delete($id);
        }

        $messenger = Messenger::get_instance();
        $messenger->add_message($messenger::TYPE_SUCCESS, __('The font has been successfully removed.', SUMEDIA_GFONT_PLUGIN_NAME));

        wp_redirect(admin_url('admin.php?page=' . SUMEDIA_GFONT_PLUGIN_NAME . '&action=Fontlist'));

    }
}