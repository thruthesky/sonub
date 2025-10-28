<?php


class ApiEndpoints
{
    public function __construct(
        public string $func,
        public array $params = []
    ) {}
}

// etc/config/app.config.php
class AppConfigApi
{


    public string $thumbnail_url;

    /**
     * @var array<string, ApiEndpoints> $endpoints
     */
    public array $endpoints = [];
    public function __construct()
    {

        $this->thumbnail_url = '/thumbnail.php';

        $this->endpoints = [
            'file_upload' => new ApiEndpoints(
                func: 'file_upload',
                params: []
            ),
            'file_delete' => new ApiEndpoints(
                func: 'file_delete',
                params: ['url' => 'string']
            ),
        ];
    }

    public function toArray(): array
    {
        $endpoints = [
            'thumbnail_url' => $this->thumbnail_url,
        ];

        foreach ($this->endpoints as $key => $endpoint) {
            $endpoints[$key] = $key;
        }

        return $endpoints;
    }
}
