<?php

namespace Sumup\Api\Service\Merchant;

use GuzzleHttp\Exception\ClientException;
use Psr\Http\Message\ResponseInterface;
use Sumup\Api\Configuration\ConfigurationInterface;
use Sumup\Api\Http\Exception\RequestException;
use Sumup\Api\Http\Exception\RequiredArgumentException;
use Sumup\Api\Http\Request;
use Sumup\Api\Model\Factory\ProductFactory;
use Sumup\Api\Repository\Collection;
use Sumup\Api\Security\OAuth2\OAuthClientInterface;
use Sumup\Api\Service\SumupService;

class ProductService extends SumupService
{
    const REQUIRED_DATA = ['title'];

    /**
     * @var ConfigurationInterface
     */
    protected $configuration;

    /**
     * @var OAuthClientInterface
     */
    protected $client;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var string
     */
    protected $requiredArgumentsValidator;

    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var ProductFactory
     */
    protected $productFactory;

    public function __construct(
        ConfigurationInterface $configuration,
        OAuthClientInterface $client,
        Request $request,
        string $requiredArgumentsValidator,
        Collection $collection,
        ProductFactory $productFactory)
    {
        $this->configuration = $configuration;
        $this->client = $client;
        $this->request = $request;
        $this->requiredArgumentsValidator = $requiredArgumentsValidator;
        $this->collection = $collection;
        $this->productFactory = $productFactory;
    }

    /**
     * @param $shelfId
     * @return Collection
     * @throws \Exception
     */
    public function all($shelfId)
    {
        $request = $this->request->setMethod('GET')
                                 ->setUri(
                                     $this->configuration->getFullEndpoint()
                                     . '/me/merchant-profile/shelves/'
                                     . (int)$shelfId
                                     . '/products'
                                 );

        /** @var ResponseInterface $response */
        $response = $this->client->request($request);

        return $this->productFactory->collect(json_decode((string)$response->getBody(), true));
    }

    /**
     * @param $shelfId
     * @param $productId
     * @return mixed
     * @throws RequestException
     * @throws \Exception
     */
    public function get($shelfId, $productId)
    {
        $request = $this->request->setMethod('GET')
                                 ->setUri(
                                     $this->configuration->getFullEndpoint()
                                     . '/me/merchant-profile/shelves/'
                                     . (int)$shelfId
                                     . '/products/'
                                     . (int)$productId
                                 );

            /** @var ResponseInterface $response */
            $response = $this->client->request($request);

        $product = $this->productFactory->create();

        return $product->hydrate(json_decode((string)$response->getBody(), true));
    }

    /**
     * @param $shelfId
     * @param $data
     * @return mixed
     * @throws RequiredArgumentException
     * @throws \Exception
     */
    public function create($shelfId, $data)
    {
        if (false === $this->requiredArgumentsValidator::validate($data, self::REQUIRED_DATA)) {
            throw new RequiredArgumentException('Missing required data provided to ' . __CLASS__);
        }

        $request = $this->request->setMethod('POST')
                                 ->setUri(
                                     $this->configuration->getFullEndpoint()
                                     . '/me/merchant-profile/shelves/'
                                     . (int)$shelfId
                                     . '/products'
                                 )
                                 ->setBody($data);

        /** @var ResponseInterface $response */
        $response = $this->client->request($request);

        $product = $this->productFactory->create();

        return $product->hydrate(json_decode((string)$response->getBody(), true));
    }

    /**
     * @param $shelfId
     * @param $productId
     * @param array $data
     * @return bool
     * @throws \Exception
     */
    public function update($shelfId, $productId, $data = [])
    {
        $request = $this->request->setMethod('PUT')
                                 ->setUri(
                                     $this->configuration->getFullEndpoint()
                                     . '/me/merchant-profile/shelves/'
                                     . (int)$shelfId
                                     . '/products/'
                                     . (int)$productId
                                 )
                                 ->setBody($data);

        /** @var ResponseInterface $response */
        $response = $this->client->request($request);

        return ($response->getStatusCode() === 204);
    }

    /**
     * @param $shelfId
     * @param $productId
     * @return bool
     * @throws \Exception
     */
    public function delete($shelfId, $productId)
    {
        $request = $this->request->setMethod('DELETE')
                                 ->setUri(
                                     $this->configuration->getFullEndpoint()
                                     . '/me/merchant-profile/shelves/'
                                     . (int)$shelfId
                                     . '/products/'
                                     . (int)$productId
                                 );

        /** @var ResponseInterface $response */
        $response = $this->client->request($request);

        return ($response->getStatusCode() === 204);
    }
}
