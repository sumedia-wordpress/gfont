<?php

class Sumedia_GFont_Admin_Controller_Delete extends Sumedia_Base_Controller
{
    public function execute()
    {
        if (!wp_verify_nonce($_POST['_wpnonce'], 'bulk-plugins_page_sumedia')) {
            return;
        }

        $fonts = Sumedia_Base_Registry::get('Sumedia_GFont_Repository_Fonts');
        foreach ($_POST['ids'] as $id) {

            if(!is_numeric($id)) {
                continue;
            }

            $data = $fonts->findOne('id', $id);
            if (!$data) {
                continue;
            }

            $font_name = $data['fontname'];

            $font_dir = wp_get_upload_dir()['basedir'] . Suma\DS . SUMEDIA_GFONT_PLUGIN_NAME;
            $dir = $font_dir . Suma\DS . 'webfonts' . Suma\DS . $font_name;
            $filelist = glob($dir.'/*');
            foreach ($filelist as $file) {
                unlink($file);
            }
            rmdir($dir);

            $fonts->delete($id);
        }

        $messenger = Sumedia_Base_Messenger::get_instance();
        $messenger->add_message($messenger::TYPE_SUCCESS, __('The font has been successfully removed.', SUMEDIA_GFONT_PLUGIN_NAME));

        wp_redirect('admin.php?page=sumedia&plugin=' . SUMEDIA_GFONT_PLUGIN_NAME . '&action=fontlist');

    }
}