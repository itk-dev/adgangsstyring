<?php

namespace ItkDev\AzureAdDeltaSync;

use ItkDev\AzureAdDeltaSync\Exception\ClientException;
use ItkDev\AzureAdDeltaSync\Exception\DataException;
use ItkDev\AzureAdDeltaSync\Handler\HandlerInterface;
use Nyholm\Psr7\Request;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class Controller.
 *
 * Contains the logic needed for running the Azure AD Delta Sync flow.
 */
class Controller
{
    private ClientInterface $client;

    public function __construct(ClientInterface $client, private array $options)
    {
        $this->client = $client;

        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);

        $this->options = $resolver->resolve($options);
    }

    /**
     * Runs the Azure AD Delta Sync flow.
     *
     * @throws DataException
     * @throws ClientException
     */
    public function run(HandlerInterface $handler): void
    {
        $request = new Request(
            'POST',
            $this->options['uri'],
            [],
            json_encode([
                'securityKey' => $this->options['security_key'],
                'clientSecret' => $this->options['client_secret'],
            ])
        );

        try {
            $postResponse = $this->client->sendRequest($request);
        } catch (ClientExceptionInterface $e) {
            throw new ClientException($e->getMessage(), $e->getCode(), $e);
        }

        try {
            $data = json_decode($postResponse->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new DataException($e->getMessage(), $e->getCode(), $e);
        }

        if (!is_array($data) && !empty($data)) {
            throw new DataException('No users found in system.');
        }

        $handler->collectUsersForDeletionList();
        $handler->removeUsersFromDeletionList($data);
        $handler->commitDeletionList();
    }

    /**
     * Sets required options.
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired(['uri', 'security_key', 'client_secret']);
    }
}
