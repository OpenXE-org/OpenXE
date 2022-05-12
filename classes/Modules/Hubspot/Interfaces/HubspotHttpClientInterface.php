<?php


namespace Xentral\Modules\Hubspot\Interfaces;


interface HubspotHttpClientInterface
{

    const GET_REQUEST = 'GET';
    const POST_REQUEST = 'POST';
    const DELETE_REQUEST = 'DELETE';
    const PATCH_REQUEST = 'PATCH';
    const PUT_REQUEST = 'PUT';

    //public function get($url, $data = [], $header = []);
}
