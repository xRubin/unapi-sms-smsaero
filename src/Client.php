<?php
namespace unapi\sms\smsaero;

class Client extends \GuzzleHttp\Client
{
    /**
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        if (!array_key_exists('base_uri', $config)) {
            if (!array_key_exists('email', $config))
                throw new \InvalidArgumentException('email required for Client base_uri');
            if (!array_key_exists('apiKey', $config))
                throw new \InvalidArgumentException('apiKey required for Client base_uri');

            $config['base_uri'] = sprintf('https://%s:%s@gate.smsaero.ru/', urlencode($config['email']), $config['apiKey']);
        }

        parent::__construct($config);
    }
}