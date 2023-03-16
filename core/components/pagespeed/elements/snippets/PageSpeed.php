<?php
//
/*
    [[!PageSpeed?
        &bundle=`link script`
        &convert=`static`
        &critical=`true`
        &crossorigin=`anonymous`
        &display=`swap`
        &enable=`true`
        &integrity=`sha256`
        &lifetime=`604800`
        &loading=`lazy`
        &minify=`html link script`
        &path=`{assets_path}PageSpeed/`
        &quality=`80`
        &resize=`true`
        &script=`defer`
        &subresources=`{
            "link" : [
                { "name" : "", "version" : "", "filename" : "", "crossorigin" : "", "integrity" : "", "media" : "" },
                { "url" : "", "crossorigin" : "", "integrity" : "", "media" : "" }
            ],
            "script" : [
                { "name" : "", "version" : "", "filename" : "", "async" : "", "crossorigin" : "", "defer" : "", "integrity" : "", "nomodule" : "" },
                { "url" : "", "async" : "", "crossorigin" : "", "defer" : "", "integrity" : "", "nomodule" : "" }
            ]
        }`
        &url=`{url_scheme}{http_host}{assets_url}PageSpeed/`
    ]]
*/
if( isset( $modx->PageSpeed ) && $modx->PageSpeed instanceof \PageSpeed )
    $modx->PageSpeed->configure( $scriptProperties );