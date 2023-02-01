<?php

$_SERVER['SERVER_NAME'] = $_SERVER['HTTP_HOST'];


$WHITELIST_PHP = array(

        
        '/index.php' => true,

        'upgrade.php' => true,

        '/wp-login.php' => true,

        'data2' => true,

        'setup-config.php' => true,

        '/wp-includes/js/tinymce/wp-tinymce.php' => true,

        '/wp-admin/install.php' => true,
        '/wp-admin/admin' => true,
         '/wp-admin/media' => true,
        '/phpmyadmin' => true,
        '/wp-admin/about.php' => true,        
        '/wp-admin/post.php' => true,
        '/wp-admin/post.php' => true,
        '/wp-admin/term' => true,
        '/wp-admin/edit.php' => true,
        '/wp-admin/index.php' => true,
        'update.php' => true,
        '/wp-admin/post' => true,
        '/wp-admin/plugins.php' => true,
        '/wp-admin/load-scripts.php' => true,
        '/wp-admin/load-styles.php' => true,
        '/wp-admin/admin.php' => true,
        '/wp-admin/admin-ajax.php' => true,
        '/wp-comments-post.php' => true,
        'wp-admin/theme-' => TRUE,
        '/widgets.php' => true,
        'post-new.php' => true,
        '/wp-cron.php' => true,
        '/wp-admin/plugin' => true,
        '/wp-admin/edit' => true,
        '/wp-admin/export.php' => true,
        '/wp-admin/import.php' => true,
        '/wp-admin/options' => true,
        '/wp-admin/update-core' => true,
        '/wp-admin/nav-menus.php' => true,
        '/wp-admin/user' => true,
        '/wp-admin/customize.php' => true,
        '/wp-admin/revision' => true,
        '/wp-admin/edit-comments.php' => true,
        '/wp-admin/upload.php' => true,
        '/wp-admin/themes.php' => true,
        '/wp-admin/tools.php' => true,
        'media-new.php' => true,
        'profile.php' => true,
        #'xmlrpc.php' => true,
        'async-upload.php' => true,
);

if(isset($_SERVER['REQUEST_URI']))
{
        if(strpos($_SERVER['REQUEST_URI'],".php") !== false)
        {
                $D2BLOCK = true;
                foreach($WHITELIST_PHP as $D2URL => $tmp)
                {
                        
                    if(strpos($_SERVER['REQUEST_URI'], $D2URL) !== false)
                    {
                            $D2BLOCK = false;
                            break;
                    }

                }

                if(strpos($_SERVER['REQUEST_URI'], "wp-content/uploads") !== false)
                {
                        $D2BLOCK = true;
                }

                if($D2BLOCK)
                {
                        header('HTTP/1.0 403 Forbidden');
                        #@file_put_contents('/tmp/403-log.txt', $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . PHP_EOL, FILE_APPEND);
                        //exit('Data2 - Security System - #' . $_SERVER['REQUEST_URI']);
                        exit("<div id='ouro-cloud-container'>Ouro.Cloud - Security System...</div><script>var request = new XMLHttpRequest(); request.onreadystatechange = function() { if(request.readyState == 4 && request.status == 200) { document.getElementById('ouro-cloud-container').innerHTML = request.responseText; } } ; request.open('GET', 'https://ouro.cloud/templates/security/?from=' + encodeURIComponent(window.location.href)); request.send();</script>");
                }
        }
}
