<?php

namespace Tests\Support;

use Stripe\ApiRequestor;
use Stripe\HttpClient\ClientInterface;
use Stripe\Stripe;

/**
 * In-memory replacement for Stripe's HTTP client: no network calls in tests.
 *
 *     $stripe = FakeStripeClient::install();
 *     $stripe->respond('GET', '/v1/checkout/sessions/cs_1', ['id' => 'cs_1', 'object' => 'checkout.session', ...]);
 *     ...
 *     expect($stripe->requested('POST', '/v1/checkout/sessions/cs_1/expire'))->toBeTrue();
 */
class FakeStripeClient implements ClientInterface
{
    /** @var array<string, array{0: array<string, mixed>, 1: int}> */
    private array $responses = [];

    /** @var array<int, string> "METHOD /path" of every request made */
    public array $requests = [];

    public static function install(): self
    {
        Stripe::setApiKey('sk_test_fake');
        ApiRequestor::setHttpClient($client = new self);

        return $client;
    }

    public static function uninstall(): void
    {
        ApiRequestor::setHttpClient(null);
    }

    /**
     * @param  array<string, mixed>  $body
     */
    public function respond(string $method, string $path, array $body, int $status = 200): self
    {
        $this->responses[strtoupper($method).' '.$path] = [$body, $status];

        return $this;
    }

    public function respondMissing(string $method, string $path): self
    {
        return $this->respond($method, $path, ['error' => [
            'type' => 'invalid_request_error',
            'code' => 'resource_missing',
            'message' => 'No such checkout.session',
        ]], 404);
    }

    public function requested(string $method, string $path): bool
    {
        return in_array(strtoupper($method).' '.$path, $this->requests, true);
    }

    public function request($method, $absUrl, $headers, $params, $hasFile, $apiMode = 'v1', $maxNetworkRetries = null)
    {
        $key = strtoupper($method).' '.parse_url($absUrl, PHP_URL_PATH);
        $this->requests[] = $key;

        [$body, $status] = $this->responses[$key]
            ?? throw new \LogicException("Unexpected Stripe request: {$key}");

        return [json_encode($body), $status, []];
    }
}
