<?php
switch ($modx->event->name) {
    case 'OnWebPageInit':
        if ($modx->context->key == 'mgr') {
            return;
        }

        /** авторизовывает админа в админке (если он не авторизован на фронте, и является администратором) */
        if (!$modx->user->hasSessionContext('mgr') && $modx->user->hasSessionContext($modx->context->key) && ($modx->user->get('sudo') || $modx->user->isMember('Administrator'))) {
            $modx->user->addSessionContext('mgr');
        }

        /** авторизовывает на фронте любого юзера, который авторизован в админке */
        if ($modx->user->hasSessionContext('mgr') && !$modx->user->hasSessionContext($modx->context->key)) {
            $modx->user->addSessionContext($modx->context->key);
        }

        break;
}