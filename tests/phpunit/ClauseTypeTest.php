<?php

/**
 * Class RadiantAPIClauseTypeTest
 * @group Database
 * @group medium
 * @group API
 */
class RadiantAPIClauseTypeTest extends ApiTestCase
{
    protected static $_clausetypeId;

    protected function setUp()
    {
        parent::setUp();
        $this->setMwGlobals( array(
            'smwgEnableUpdateJobs' => false,
            'smwgAutoRefreshOnPurge' => false,
            'smwgFactboxCacheRefreshOnPurge' => false,
            'smwgCacheType' => CACHE_NONE
        ) );
        $this->doLogin();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * Create Checklist template and adds 1 checklist item into database
     * @throws Exception
     * @throws MWException
     */
    public function addDBData()
    {

        if ( Title::newFromText( 'Clause type', NS_TEMPLATE )->exists() ) {
            return;
        }

        $title = Title::newFromText( "Clause type", NS_TEMPLATE );

        $user = User::newFromName( 'UTSysop' );
        $comment = __METHOD__ . ': Sample page for unit test.';

        $page = WikiPage::factory( $title );
        $page->doEditContent( ContentHandler::makeContent(
            " [[Category:Clause type]]"
            , $title ), $comment, 0, false, $user );

        $result = $this->insertPage( "UTClauseType", "{{Clause_type}}" );

        self::$_clausetypeId = $result['id'];

    }

    /**
     * @covers RadiantApiEndpoint::action_clausetype_get
     */
    public function testClauseTypeGet()
    {
        $data = $this->doApiRequest(
            array(
                'action' => 'radiant',
                'method' => 'clausetype/get'
            )
        );

        $this->assertArrayNotHasKey( 'error', $data[0] );
        $this->assertArrayHasKey( 'radiant', $data[0] );
        $this->assertArrayHasKey( 'items', $data[0]['radiant'] );
        $this->assertEquals( 1, count($data[0]['radiant']['items']) );
        $this->assertEquals( 'UTClauseType', $data[0]['radiant']['items'][0]['title'] );
    }

    /**
     * @covers RadiantApiEndpoint::action_clausetype_get
     */
    public function testClauseTypeGetOne()
    {
        $data = $this->doApiRequest(
            array(
                'action' => 'radiant',
                'method' => 'clausetype/get/'.self::$_clausetypeId
            )
        );

        $this->assertArrayNotHasKey( 'error', $data[0] );
        $this->assertArrayHasKey( 'radiant', $data[0] );
        $this->assertArrayHasKey( 'items_count', $data[0]['radiant'] );
        $this->assertArrayHasKey( 'items', $data[0]['radiant'] );
        $this->assertEquals( 1, (int)$data[0]['radiant']['items_count'] );
        $this->assertEquals( 'UTClauseType', $data[0]['radiant']['items'][0]['title'] );

    }

    /**
     * @covers RadiantApiEndpoint::action_clausetype_put
     */
    public function testClauseTypePut()
    {

        $data = $this->doApiRequest(
            array(
                'action' => 'radiant',
                'method' => 'clausetype/put',
                'data' => '{"Random field": "Random value"}',
                'title' => "UTClauseType2"
            )
        );

        $this->assertArrayNotHasKey( 'error', $data[0] );
        $this->assertArrayHasKey( 'radiant', $data[0] );
        $this->assertArrayHasKey( 'status', $data[0]['radiant'] );
        $this->assertEquals( 'success', $data[0]['radiant']['status'] );
        $this->assertArrayHasKey( 'touched_unix', $data[0]['radiant'] );

    }
    
    /**
     * @covers RadiantApiEndpoint::action_clausetype_put
     */
    public function testClauseTypeEdit()
    {

        $data = $this->doApiRequest(
            array(
                'action' => 'radiant',
                'method' => 'clausetype/put/'.self::$_clausetypeId,
                'data' => '{"Random field": "Random value 123"}',
            )
        );

        $this->assertArrayNotHasKey( 'error', $data[0] );
        $this->assertArrayHasKey( 'radiant', $data[0] );
        $this->assertArrayHasKey( 'status', $data[0]['radiant'] );
        $this->assertEquals( 'success', $data[0]['radiant']['status'] );
        $this->assertArrayHasKey( 'touched_unix', $data[0]['radiant'] );

    }

    /**
     * @covers RadiantApiEndpoint::action_clausetype_delete
     */
    public function testClauseDelete()
    {
        $data = $this->doApiRequest(
            array(
                'action' => 'radiant',
                'method' => 'clausetype/delete/'.self::$_clausetypeId
            )
        );
        $this->assertEquals( null, Title::newFromID( self::$_clausetypeId ) );
    }

}