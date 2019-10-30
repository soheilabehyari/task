<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Ivory\Serializer\Serializer;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    protected function respond(Request $request, $data, int $statusCode = 200)
    {
        $serializer = $this->createSerializer();
        $acceptFormat = 'json';
        if (0 === strpos($request->headers->get('Accept'), 'application/xml') or
            0 === strpos($request->headers->get('Content-Type'), 'application/xml')) {
            $acceptFormat = 'xml';
        }
        $content = $serializer->serialize($data, $acceptFormat);
        return response($content, $statusCode, ['Content-Type' => 'application/' . $acceptFormat]);
    }

    private function createSerializer()
    {
        return $serializer = new Serializer();
    }
}
