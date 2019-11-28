<?php
/*
Plugin Name: Delete Statc Files for StaticPres
Description: StaticPressで生成されたファイルを削除するプラグイン
Author: kz23s
Version: 0.0.1
License: GPL2

    Copyright 2019 kz23s (email : kzfms07@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
     published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA */


add_action('init', 'DeleteStaticFiles::init');

class DeleteStaticFiles
{

    const VERSION           = '0.0.1';
    const PLUGIN_ID         = 'delete-static-files';
    const CREDENTIAL_ACTION = self::PLUGIN_ID . '-nonce-action';
    const CREDENTIAL_NAME   = self::PLUGIN_ID . '-nonce-key';
    const STATIC_DIR_OPTION_NAME  = 'StaticPress::static dir';

    static function init()
    {
        return new self();
    }

    function __construct()
    {
        if (is_admin() && is_user_logged_in()) {
            add_action('admin_menu', [$this, 'set_plugin_sub_menu']);
        }
    }

    function set_plugin_sub_menu() {

        add_submenu_page(
            'static-press',
            'Delete Static Files',
            'Delete Static Files',
            'manage_options',
            'delete-static-files',
            [$this, 'show_delete_buttom']);
    }

    function show_delete_buttom() {
?>
      <div class="wrap">
        <h1>Delete Static Files</h1>
        <p>StaticPressが生成した静的ファイルを削除します。</p>

        <form action="" method='post' id="my-submenu-form">
            <?php wp_nonce_field(self::CREDENTIAL_ACTION, self::CREDENTIAL_NAME) ?>
            <input type="hidden" name="action_key" value="delete_static_files" />
            <p><input type='submit' value='削除' class='button button-primary button-large'></p>
        </form>
      </div>
<?php

        if (isset($_POST['action_key']) && $_POST['action_key'] == 'delete_static_files') {
            if (isset($_POST[self::CREDENTIAL_NAME]) && $_POST[self::CREDENTIAL_NAME] ) {
                $static_dir = get_option(self::STATIC_DIR_OPTION_NAME);

                if ($static_dir != '' && $static_dir !== '/') {
                    if (count(glob($static_dir.'/*')) === 0) { 
                        exit("削除するファイルがありません。");
                    }
                    foreach(glob($static_dir.'/*') as $dir) {
						echo "Delete  ";
						echo exec("rm -rf $dir");
						echo $dir . "<br>\n";
                    }
                    echo '削除が完了しました。';
                } else {
                    echo "Error Occured. [ERROR CODE: INVALID_STATIC_DIR]";
                }
            } else {
                echo "Error Occured. [ERROR CODE: INVALID_POST_VALUE]";
            }
        }
    }
} 
