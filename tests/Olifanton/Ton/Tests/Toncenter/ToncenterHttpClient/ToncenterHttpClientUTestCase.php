<?php declare(strict_types=1);

namespace Olifanton\Ton\Tests\Toncenter\ToncenterHttpClient;

use GuzzleHttp\Psr7\Response;
use Http\Client\Common\HttpMethodsClientInterface;
use Mockery\MockInterface;
use Olifanton\Ton\ClientOptions;
use Olifanton\Ton\Toncenter\ToncenterHttpV2Client;
use Olifanton\Interop\Address;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

abstract class ToncenterHttpClientUTestCase extends TestCase
{
    protected HttpMethodsClientInterface & MockInterface $httpClientMock;

    protected function setUp(): void
    {
        $this->httpClientMock = \Mockery::mock(HttpMethodsClientInterface::class); // @phpstan-ignore-line
    }

    protected function tearDown(): void
    {
        \Mockery::close();
    }

    protected function getInstance(): ToncenterHttpV2Client
    {
        return new ToncenterHttpV2Client(
            $this->httpClientMock,
            new ClientOptions(
                baseUri: "https://toncenter.local/api/v2",
                apiKey: "foo-bar",
            ),
        );
    }

    /**
     * @throws \JsonException
     */
    protected function createResponseStub(string|array $body, int $status = 200): ResponseInterface
    {
        if (is_array($body)) {
            $body = json_encode($body, JSON_THROW_ON_ERROR);
        }

        return new Response($status, [], $body);
    }

    /**
     * @throws \JsonException
     */
    protected function createResponseDataStub(string $datafile, int $status = 200): ResponseInterface
    {
        $filePath = STUB_DATA_DIR . "/toncenter-responses/" . $datafile . ".json";

        if (!file_exists($filePath)) {
            throw new \InvalidArgumentException("Stub file " .  $filePath . " not found");
        }

        return $this->createResponseStub(file_get_contents($filePath), $status);
    }

    protected function createAddressStub(): Address
    {
        return new Address("EQD__________________________________________0vo");
    }

    /**
     * @throws \JsonException
     */
    protected function prepareSendMock(string $dataFile): void
    {
        $response = $this->createResponseDataStub($dataFile);
        $this
            ->httpClientMock
            ->shouldReceive("send")
            ->andReturn($response);
    }
}
