# Azure AD Delta Sync

Composer package for the Azure AD Delta Sync flow.

## Usage

If you are looking to use this in a Symfony or Drupal project you should use
either:

- Symfony: [itk-dev/azure-ad-delta-sync-symfony](https://github.com/itk-dev/azure-ad-delta-sync-symfony)
- Drupal: [itk-dev/azure-ad-delta-sync-drupal](https://github.com/itk-dev/azure-ad-delta-sync-drupal)

### Direct installation

To install this package directly run

```shell
composer require itk-dev/azure-ad-delta-sync
```

### Flow

To start the flow one needs to call the
`Controller` `run(HandlerInterface $handler)` command.

Therefore, you must create your own handler that implements
`HandlerInterface`.

#### Example Usage

```php
<?php

use ItkDev\AzureAdDeltaSync\Handler\HandlerInterface;

class SomeHandler implements HandlerInterface
{
    public function collectUsersForDeletionList(): void
    {
        // Some start logic
    }

    public function removeUsersFromDeletionList(array $users): void
    {
        // Some user logic
    }

    public function commitDeletionList(): void
    {
        // Some commit logic
    }
}
```

To start the flow provide a HTTP Client that implements
[PSR-18](https://www.php-fig.org/psr/psr-18/) `CLientInterface`,
and the required options seen in the example beneath.

Note that this example uses Guzzle HTTP Client.
For a list of PSR-18 implementing libraries click [here](https://packagist.org/providers/psr/http-client-implementation).

```php

use GuzzleHttp\Client;
use ItkDev\AzureAdDeltaSync\Controller;


$options = [
  'uri' => 'https://aarhus.../RetrieveProvisioningData/...', // System provisioning uri
  'security_key' => 'some_security_key', // Provisioning data security key
  'client_secret' => 'some_client_secret', // System provisioning client secret
];

$handler = new SomeHandler();

$client = new Client();
$controller = new Controller($client, $this->options);

$controller->run($handler);
```

### General comments

Note that this package does not do the synchronization
of users, instead it provides a list of all users that
currently has access to the system in question.

Should the configured system contain no users an exception will be
thrown. This is to avoid using systems to be under the impression
that every single user should be deleted.

## Development Setup

A `docker-compose.yml` file with a PHP 8.2 image is included in this project.
To install the dependencies you can run

```shell
docker compose pull
docker compose up -d
docker compose exec phpfpm composer install
```

### Unit Testing

We use PHPUnit for unit testing. To run the tests:

```shell
docker compose exec phpfpm composer install
docker compose exec phpfpm ./vendor/bin/phpunit tests
```

The test suite uses [Mocks](https://phpunit.de/manual/6.5/en/test-doubles.html)
for generation of test doubles.

### Coding Standard

#### PHP files (PHP_CodeSniffer)

Check PHP coding standards

```shell
docker compose run --rm phpfpm composer install
docker compose run --rm phpfpm composer coding-standards-check
```

Apply coding standard changes

```shell
docker compose run --rm phpfpm composer coding-standards-apply
```

#### Markdown files

```shell
docker run --rm --volume "$PWD:/md" itkdev/markdownlint '**/*.md'
docker run --rm --volume "$PWD:/md" itkdev/markdownlint '**/*.md' --fix
```

### GitHub Actions

All code checks mentioned above are automatically run by [GitHub
Actions](https://github.com/features/actions) when a pull request is created.

To run the actions locally, install [act](https://github.com/nektos/act) and run

```sh
act -P ubuntu-latest=shivammathur/node:focal pull_request
```

Use `act -P ubuntu-latest=shivammathur/node:focal pull_request --list` to see
individual workflow jobs that can be run, e.g.

```sh
act -P ubuntu-latest=shivammathur/node:focal pull_request --job phpcsfixer
```

## Versioning

We use [SemVer](http://semver.org/) for versioning.
For the versions available, see the
[tags on this repository](https://github.com/itk-dev/azure-ad-delta-sync/tags).

## License

This project is licensed under the MIT License - see the
[LICENSE.md](LICENSE.md) file for details
