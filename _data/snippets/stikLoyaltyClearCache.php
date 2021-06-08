id: 100
name: stikLoyaltyClearCache
category: stik
properties: null

-----

$modx->cacheManager->delete('levels', [xPDO::OPT_CACHE_KEY => 'loyalty']);