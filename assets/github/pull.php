<?php
define('MODX_API_MODE', true);
require_once dirname(__DIR__, 2) . '/index.php';
/** @var $modx gitModx */
$input = json_decode(file_get_contents('php://input'),1);
if(strpos($input['ref'],$modx->getOption('github_branch_on_push',[],'main')) !== false){
    `git stash push --include-untracked`;
    `git stash drop`;
    `git pull`;
    $modx->cacheManager->refresh();
}