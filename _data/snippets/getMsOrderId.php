id: 111
name: getMsOrderId
category: miniShop2
properties: 'a:0:{}'

-----

if (isset($_GET['OrderId']) AND !isset($_GET['msorder'])) {
    $_GET['msorder'] = (int)$_GET['OrderId'];
}