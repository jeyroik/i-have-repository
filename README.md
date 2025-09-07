# php-repository
Framework agnostic repository base

# usage

## example

```php
use jeyroik\interfaces\IHaveAttributes;
use jeyroik\interfaces\attributes\IHaveIdString;

class Some implements IHaveAttributes, IHaveIdString
{
    use THasIdString;

    //...
}

$some = new class implements IHaveRepository {
    use THasRepository;

    public function createSome(array $data): Some
    {
        return $this->getRepo(Some::class)->insertOne($data);
    }
};

$someItem = $some->createSome([
    'p1' => 'v1'
]);

echo $someItem->p1;// v1

//if RepoPluginUUid is on:
echo $someItem->getId();// something like 181d7dbb-fb11-40c1-af55-ee4cefc6fa33
```

## set envs

- DB__CLASS = class for a db driver
- DB__NAME = name for a db
- REPOSITORY__PLUGINS_FILE = path to repository plugins file

## repository plugins file example

```php
<?php
use jeyroik\components\repositories\plugins\RepoPluginUuid;

return [
    RepoPluginUuid::class => [
        //options for plugin - see in the specific plugin description
    ]
];
```

