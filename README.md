# Microsite

Skeleton for custom PHP-based tools with a Bootstrap 4 web interface.

Microsite was developed specifically to have a common featureset for the kind of custom tools a PHP developer needs in his daily work. It can be installed via composer, and offers most features a web interface needs. It does not support user authentication: that's beyond the scope of the package.

## Features

* Bootstrap 4 interface
* Integrated common UI elements like DataGrids
* Helper classes for most tasks via [mistralys/application-utils](https://github.com/Mistralys/application-utils)
* Localization via [mistralys/application-localization](https://github.com/Mistralys/application-localization)
* Form handling via the extended [mistralys/HTML_QuickForm2](https://github.com/Mistralys/HTML_QuickForm2)

## Howtos

### Displaying media files

Add the `DisplayMedia` page in your site, like this:

```php
<?php

namespace YourSiteNamespace;

class Page_DisplayMedia extends \Microsite\Page_DisplayMedia
{
}
```

This will allow using the site's `getMediaURL` method to generate an URL to view any media file on disk.

```php
$imageFile = '/path/to/image.png';
$imageUrl = $site->getMediaURL($imageFile);

echo '<img src="'.$imageUrl.'">';
```
