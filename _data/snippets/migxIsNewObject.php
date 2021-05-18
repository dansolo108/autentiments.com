id: 26
name: migxIsNewObject
category: MIGX
properties: 'a:0:{}'

-----

if (isset($_REQUEST['object_id']) && $_REQUEST['object_id']=='new'){
    return 1;
}