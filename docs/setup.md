# Initial setup

## Installing

Install via composer.
``` bash
$ composer require glaivepro/ajaxable
```

## Setting up

You have to add the trait to a model that you want to be ajaxable.

```php
namespace App;

use GlaivePro\Ajaxable\Traits\Ajaxable;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use Ajaxable;
	//
}
```
