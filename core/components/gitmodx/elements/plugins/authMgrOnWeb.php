<?php
if ($modx->event->name == 'OnWebPageInit') {
    if (!$modx->user->hasSessionContext('web') && $modx->user->hasSessionContext('mgr') && $modx->user->isMember('Administrator')) {
        $modx->user->addSessionContext('web');
    } elseif ($modx->user->hasSessionContext('web') && !$modx->user->hasSessionContext('mgr') && $modx->user->isMember('Administrator')) {
        $modx->user->removeSessionContext('web');
    }
}