<?php

/**
 * Class RadiantAPICommonTest
 * @group medium
 */
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

    /**
     * @expectedException UsageException
     */
    public function testApiSetup()
    {
        $data = $this->doApiRequest(
            array(
                'action' => 'radiant'
            )
        );
        $this->assertArrayNotHasKey( 'error', $data[0] );
    }

}