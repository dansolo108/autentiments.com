Сниппет вывода объектов избранного `msFavorites.objects`

## Параметры
Параметр               | По умолчанию  | Описание
-----------------------|---------------|---------------------------------------------
**list**               | default                   | Название списока избранногою
**type**               | resource                  | Тип списка избранного
**uid**                |                           | Идентификатор пользователя
**sortby**             | {"createdon": "ASC"}      | Сортировка выборки
**return**             | data                      | Формат возвращаемых данных
**processObjectExtra** |                           | Обработать дополнительные поля объекта избранного
**toPlaceholder**      |                           | Плейсхолдер куда нужно охранить результат сниппета место вывода не экран

## Вызов
```
[[!msFavorites.objects?
&list=`default`
&type=`resource`
&returnIds=`1`
]]
```

### Плейсхолдеры
Вы можете увидеть все доступные плейсхолдеры заказа просто указав пустой чанк:
```
<pre>
[[!msFavorites.objects?
&list=`default`
&type=`resource`
&processObjectExtra=`1`
&return=`chunk`
]]
</pre>
```

```
Array
(
    [uid] => 1ekv5jgf977iael7v3i73fku6h
    [anon] => 1
    [temp] => 1
    [createdon] => 2019-07-04 16:09:29
    [list] => default
    [type] => resource
    [key] => 3
    [extra] => Array
        (
        )

    [idx] => 1
)
Array
(
    [uid] => 1ekv5jgf977iael7v3i73fku6h
    [anon] => 1
    [temp] => 1
    [createdon] => 2019-07-04 16:09:30
    [list] => default
    [type] => resource
    [key] => 11
    [extra] => Array
        (
        )

    [idx] => 2
)
```

