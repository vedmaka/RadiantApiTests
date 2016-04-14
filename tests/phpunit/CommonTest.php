<?php

class RadiantAPICommonTest extends ApiTestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->doLogin();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    public function testApiSetup()
    {
        $data = $this->doApiRequest(
            array(
                'action' => 'radiant'
            )
        );
    }

}