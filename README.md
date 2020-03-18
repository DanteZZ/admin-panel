# Конструктор Админ Панели

Модуль для [CrossEngine](https://github.com/DanteZZ/cross_engine), позволяющий быстро разработать админ-панель.

## Способ установки

  1. Перенесите модуль в директорию /addict/modules;
  3. Готово!

## Функциональные особенности
Плагин делится на несколько составляющих, это:
 - Страницы [/pages/*.tmpl]
 - Предписанные скрипты получения данных [/data-rules/*.php]
 - Скрипты обработки данных [/handlers/*.php]
 - Vue компоненты для работы [/vue-components/*/*.vue]

Благодаря данной структуре, можно полностью кастомизировать Админ-Панель. Вы можете создать собственные страницы, и уместить необходимые компоненты. Настроить обработку данных, любым из 3 способов: json массив, data-rule, php.

Также вы можете скачать файл синтаксиса для файлов .TMPL разработанный специально для CrossEngine:
<p align="center">
  <img src="https://github.com/DanteZZ/admin-panel/blob/master/readme/syntax.png?raw=true" alt="Sublime Syntax TMPL"/>
</p>

## Используемые плагины
  - JQuery
  - Vue.JS
  - Responsive File Manager
  - Ace.JS
  - Vue-Loader
 
