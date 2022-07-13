<?php
$utms = ['utm_content','utm_medium','utm_campaign','utm_source','utm_term'];
foreach($utms as $utm){
    if(isset($_GET[$utm]))
        setcookie($utm, $_GET[$utm], time() + 3600 * 24);
}