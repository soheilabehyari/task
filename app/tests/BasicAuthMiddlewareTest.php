<?php

class BasicAuthMiddlewareTest extends TestCase
{

    public function testBasicAuthWithValidCredentials()
    {
        $this->call('GET', 'api/v1/auth', [], [], [],
            ["WWW-Authenticate" => "Basic " . base64_encode("guest:guest"), 'PHP_AUTH_USER' => 'guest', 'PHP_AUTH_PW' => 'guest']);
        $this->assertResponseStatus(200);
    }

    public function testBasicAuthWithInValidCredentials()
    {
        $this->call('GET', 'api/v1/auth', [], [], [],
            ["WWW-Authenticate" => "Basic " . base64_encode("dummy:dummy"), 'PHP_AUTH_USER' => 'dummy', 'PHP_AUTH_PW' => 'dummy']);
        $this->assertResponseStatus(401);
    }
}
