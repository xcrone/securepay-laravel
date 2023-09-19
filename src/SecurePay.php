<?php

namespace Xcrone\SecurePay;

use Xcrone\Exceptions\InvalidCredentialException;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;

class SecurePay
{
    /**
     * The production API endpoint.
     *
     * @var string
     */
    private $production_endpoint = 'https://securepay.my/api';

    /**
     * The staging API endpoint.
     *
     * @var string
     */
    private $sandbox_endpoint = 'https://sandbox.securepay.my/api';

    /**
     * The API version.
     *
     * @var string
     */
    private $version = 'v1';

    /**
     * The API UID.
     *
     * @var string
     */
    private $api_uid;

    /**
     * The API auth token.
     *
     * @var string
     */
    private $auth_token;

    /**
     * The checksum token.
     *
     * @var string
     */
    private $checksum_token;

    /**
     * Create a new class instance.
     *
     * @return void
     */
    public function __construct($params = [
        'uid' => null,
        'auth' => null,
        'checksum' => null,
    ]) {
        $this->api_uid = config('securepay.uid');
        $this->auth_token = config('securepay.auth');
        $this->checksum_token = config('securepay.checksum');

        $this->validateApiCredential();
    }

    /**
     * Instantiate Guzzle Client.
     *
     * @return \GuzzleHttp\Client
     */
    private function http($private = true)
    {
        $base_uri = $this->getBaseUri(config('securepay.environment'), $private);

        return new Client([
            'base_uri' => $base_uri,
            'auth' => [
                $this->api_uid,
                $this->auth_token,
            ],
            'http_errors' => false,
        ]);
    }

    /**
     * Get the base API URI based on the environment.
     *
     * @param  string  $environment
     * @return string
     */
    private function getBaseUri($environment = 'sandbox', $private = true)
    {
        $base_uri = $environment == 'sandbox'
            ? $this->sandbox_endpoint
            : $this->production_endpoint;

        return $private
            ? "{$base_uri}/{$this->version}/"
            : "{$base_uri}/public/{$this->version}/";
    }

    /**
     * Validate the provided API credential.
     *
     * @return void
     *
     * @throws \Xcrone\InvalidCredentialException
     */
    private function validateApiCredential()
    {
        $response = $this->http()->post('merchants/validate', [
            'form_params' => [
                'checksum_token' => config('securepay.checksum'),
            ],
        ]);

        if ($response->getStatusCode() != 201) {
            throw new InvalidCredentialException('Invalid SecurePay API Credential');
        }
    }

    /**
     * Create a new payment.
     *
     * @param  array  $options
     * @return \GuzzleHttp\Psr7\Stream
     */
    public function createPayment($options)
    {
        $options = array_merge($options, [
            'uid' => $this->api_uid,
            'token' => $this->auth_token,
        ]);

        $array = Arr::only($options, [
            'buyer_email',
            'buyer_name',
            'buyer_phone',
            'callback_url',
            'order_number',
            'product_description',
            'redirect_url',
            'transaction_amount',
            'uid',
        ]);

        $options = array_merge($options, [
            'checksum' => generate_checksum($array, $this->checksum_token),
        ]);

        return $this->http()->post('payments', [
            'form_params' => $options,
        ])->getBody();
    }

    /**
     * Get a bank list.
     *
     * @return JSON
     */
    public function getRetailBankList($online = null)
    {
        $request = $this->http(false)->get('banks/b2c');

        if($online == true || $online == false) {
            $request = $this->http(false)->get('banks/b2c', [
                'status' => $online ? 'online' : 'offline',
            ]);
        }

        return json_decode($request->getBody()->getContents());
    }

    /**
     * Get a bank list.
     *
     * @return JSON
     */
    public function getCorporateBankList($online = null)
    {
        $request = $this->http(false)->get('banks/b2b');

        if($online == true || $online == false) {
            $request = $this->http(false)->get('banks/b2b', [
                'status' => $online ? 'online' : 'offline',
            ]);
        }

        return json_decode($request->getBody()->getContents());
    }

    /**
     * Verify response checksum.
     *
     * @param  array  $result
     * @param  string  $checksum
     * @return bool
     */
    public function verifyChecksum($result, $checksum)
    {
        if (! blank($result['params'])) {
            $params = json_encode($result['params']);
            $params = str_replace('":"', '"=>"', $params);
            $params = str_replace('","', '", "', $params);
            $result['params'] = $params;
        }

        $new_checksum = generate_checksum($result, $this->checksum_token);

        return $checksum === $new_checksum;
    }
}
