<?php
namespace unapi\sms\smsaero;

use GuzzleHttp\Promise\PromiseInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use unapi\sms\common\dto\MessageInterface;
use unapi\sms\common\GateServiceInterface;

class Service implements GateServiceInterface, LoggerAwareInterface
{
    /** @var Client */
    private $client;
    /** @var LoggerInterface */
    private $logger;

    /** @var string */
    private $sign;
    /** @var string */
    private $channel = 'DIRECT';

    /**
     * @param array $config Service configuration settings.
     */
    public function __construct(array $config = [])
    {
        if (!isset($config['client'])) {
            throw new \InvalidArgumentException('Client required');
        } elseif ($config['client'] instanceof Client) {
            $this->setClient($config['client']);
        } else {
            throw new \InvalidArgumentException('Client must be instance of \unapi\sms\smsaero\Client');
        }

        if (!isset($config['logger'])) {
            $this->setLogger(new NullLogger());
        } elseif ($config['logger'] instanceof LoggerInterface) {
            $this->setLogger($config['logger']);
        } else {
            throw new \InvalidArgumentException('Logger must be instance of LoggerInterface');
        }

        if (!isset($config['sign'])) {
            throw new \InvalidArgumentException('Sign required');
        } else {
            $this->setSign($config['sign']);
        }

        if (isset($config['channel'])) {
            $this->setChannel($config['channel']);
        }
    }

    /**
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * @param Client $client
     */
    public function setClient(Client $client): void
    {
        $this->client = $client;
    }

    /**
     * @inheritdoc
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    /**
     * @return string
     */
    public function getSign(): string
    {
        return $this->sign;
    }

    /**
     * @param string $sign
     */
    public function setSign(string $sign): void
    {
        $this->sign = $sign;
    }

    /**
     * @return string
     */
    public function getChannel(): string
    {
        return $this->channel;
    }

    /**
     * @param string $channel
     */
    public function setChannel(string $channel): void
    {
        $this->channel = $channel;
    }

    /**
     * @param MessageInterface $message
     * @return PromiseInterface
     */
    public function sendMessage(MessageInterface $message): PromiseInterface
    {
        $this->getLogger()->debug('Отправляем SMS на номер {number}: {text}' , [
            'number' => $message->getPhone()->getNumber('7'),
            'text' => $message->getText()
        ]);

        return $this->getClient()->getAsync('/v2/sms/send', [
            'query' => [
                'number' => $message->getPhone()->getNumber('7'),
                'text' => $message->getText(),
                'sign' => $this->getSign(),
                'channel'=> $this->getChannel()
            ]
        ]);
    }
}