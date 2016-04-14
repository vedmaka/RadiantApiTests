<?php

/**
 * Class RadiantAPIDictionaryTest
 * @group medium
 */
class RadiantAPIDictionaryTest extends ApiTestCase
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

    public function addDBData()
    {
        $this->insertPage( "Property:utdictionary", "[[Allows value::One]] [[Allows value::Two]]" );
    }

    /**
     * @expectedException UsageException
     */
    public function testApiSetup()
    {
        $data = $this->doApiRequest(
            array(
                'action' => 'dictionary/get/utdictionary'
            )
        );
        $this->assertArrayNotHasKey( 'error', $data[0] );
        $this->assertArrayHasKey( 'radiant', $data[0] );
        $this->assertArrayHasKey( 'values', $data[0]['radiant'] );
        $this->assertArrayEquals( array('One', 'Two') , $data[0]['radiant']['values'] );
    }

}