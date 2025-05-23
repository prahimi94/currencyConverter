<?php

namespace App\Services;

use InfluxDB2\Client;
use InfluxDB2\Model\WritePrecision;
use InfluxDB2\Point;

class InfluxService
{
    protected $client;
    protected $writeApi;
    protected $bucket;
    protected $org;

    public function __construct()
    {
        $this->client = new Client([
            "url" => env('INFLUXDB_URL'),
            "token" => env('INFLUXDB_TOKEN'),
            "bucket" => env('INFLUXDB_BUCKET'),
            "org" => env('INFLUXDB_ORG')
        ]);

        $this->writeApi = $this->client->createWriteApi();
        $this->bucket = env('INFLUXDB_BUCKET');
        $this->org = env('INFLUXDB_ORG');
    }

    public function writeRequestLog($method, $url, $requestData, $responseData, $duration)
    {
        $point = Point::measurement('http_requests')
            ->addTag('method', $method)
            ->addTag('url', (string) $url)
            ->addTag('request', substr(json_encode($requestData), 0, 1000))
            ->addTag('response', substr(json_encode($responseData), 0, 1000))
            ->addField('duration', (float) $duration)
            ->time(now(), WritePrecision::S);

        $this->writeApi->write($point, WritePrecision::S, $this->bucket, $this->org);
    }

    
    public function writeApiCallLog($url, $method, $requestData, $responseData, $duration)
    {
        $point = Point::measurement('api_calls')
            ->addTag('method', $method)
            ->addTag('url', (string) $url)
            ->addTag('request', substr(json_encode($requestData), 0, 1000))
            ->addTag('response', substr(json_encode($responseData), 0, 1000))
            ->addField('duration', (float) $duration)
            ->time(now(), WritePrecision::S);

        $this->writeApi->write($point, WritePrecision::S, $this->bucket, $this->org);
    }
}
