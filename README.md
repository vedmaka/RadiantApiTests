Modification required to:

* TemplateData
* VisualCollapsible
* SlackBot

onPageContentSave and onArticleDelete should be prepended with:

```php
if( defined('MW_PHPUNIT_TEST') ) { return true; }
```
