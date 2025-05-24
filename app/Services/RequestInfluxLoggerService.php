<?php

namespace App\Services;

use InfluxDB2\Client;
use InfluxDB2\Model\WritePrecision;
use InfluxDB2\Point;
use App\Services\Contracts\RequestLoggerInterface;
use App\Services\Contracts\ExceptionLoggerInterface;

class RequestInfluxLoggerService implements RequestLoggerInterface
{
    protected $client;
    protected $writeApi;
    protected $bucket;
    protected $org;

    public function __construct(protected ExceptionLoggerInterface $logger)
    {
        try {
            $this->client = new Client([
                "url" => env('INFLUXDB_URL'),
                "token" => env('INFLUXDB_TOKEN'),
                "bucket" => env('INFLUXDB_BUCKET'),
                "org" => env('INFLUXDB_ORG')
            ]);

            $this->writeApi = $this->client->createWriteApi();
            $this->bucket = env('INFLUXDB_BUCKET');
            $this->org = env('INFLUXDB_ORG');
        } catch (\Exception $e) {
            $this->logger->logException($e, null);
        }
    }
    
    public function logRequest($measurement, $method, $url, $requestData, $responseData, $duration):void
    {
        try {
            $point = Point::measurement($measurement)
            ->addTag('method', $method)
            ->addTag('url', (string) $url)
            ->addTag('request', substr(json_encode($requestData), 0, 1000))
            ->addTag('response', substr(json_encode($responseData), 0, 1000))
            ->addField('duration', (float) $duration)
            ->time(now(), WritePrecision::S);

            $this->writeApi->write($point, WritePrecision::S, $this->bucket, $this->org);
        } catch (\Exception $e) {
            $this->logger->logException($e, null);
        }
        
    }
}
