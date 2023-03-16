--------------------
mSync
--------------------
Автор: Киреев Виталий <kireevvit@gmail.com>
--------------------
Компонент синхронизации с 1С и сервисами "Класс365", "МойСклад" посредством протокола CommerceMl.

url: https://modstore.pro/packages/import-and-export/msync
doc: https://docs.modx.pro/components/msync/


Add support on FastCGI mode:
add code in .htaccess

Add support on FastCGI mode
RewriteCond %{HTTP:Authorization} !^$
RewriteRule ^(.*)$ $1?http_auth=%{HTTP:Authorization} [QSA]
