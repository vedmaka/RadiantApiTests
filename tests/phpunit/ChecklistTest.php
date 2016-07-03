<?php

/**
 * Class RadiantAPIChecklistTest
 * @group Database
 * @group medium
 * @group API
 */
class RadiantAPIChecklistTest extends ApiTestCase
{
    protected static $_checklistId;

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

        if ( Title::newFromText( 'Checklist', NS_TEMPLATE )->exists() ) {
            return;
        }

        $title = Title::newFromText( "Checklist", NS_TEMPLATE );

        $user = User::newFromName( 'UTSysop' );
        $comment = __METHOD__ . ': Sample page for unit test.';

        $page = WikiPage::factory( $title );
        $page->doEditContent( ContentHandler::makeContent(
            " [[Category:Checklist]]"
            , $title ), $comment, 0, false, $user );

        $result = $this->insertPage( "UTChecklist", "{{Checklist||Checklist name=UTChecklist||Checklist items=* Test item 1}}" );

        self::$_checklistId = $result['id'];

    }

    /**
     * Tests checklist listing
     * @covers RadiantApiEndpoint::action_checklist_get
     */
    public function testChecklistGet()
    {
        $data = $this->doApiRequest(
            array(
                'action' => 'radiant',
                'method' => 'checklist/get'
            )
        );

        $this->assertArrayNotHasKey( 'error', $data[0] );
        $this->assertArrayHasKey( 'radiant', $data[0] );
        $this->assertArrayHasKey( 'items', $data[0]['radiant'] );
        $this->assertEquals( 1, count($data[0]['radiant']['items']) );
        $this->assertEquals( 'UTChecklist', $data[0]['radiant']['items'][0]['title'] );
    }

    /**
     * Tests querying of one checklist item
     * @covers RadiantApiEndpoint::action_checklist_get
     */
    public function testChecklistGetOne()
    {
        $data = $this->doApiRequest(
            array(
                'action' => 'radiant',
                'method' => 'checklist/get/'.self::$_checklistId
            )
        );

        $this->assertArrayNotHasKey( 'error', $data[0] );
        $this->assertArrayHasKey( 'radiant', $data[0] );
        $this->assertArrayHasKey( 'items_count', $data[0]['radiant'] );
        $this->assertArrayHasKey( 'items', $data[0]['radiant'] );
        $this->assertEquals( 1, (int)$data[0]['radiant']['items_count'] );
        $this->assertEquals( 'UTChecklist', $data[0]['radiant']['items'][0]['title'] );

    }

    /**
     * Tests checklist creation
     * @covers RadiantApiEndpoint::action_checklist_put
     */
    public function testChecklistPut()
    {

        $data = $this->doApiRequest(
            array(
                'action' => 'radiant',
                'method' => 'checklist/put',
                'data' => '{"Checklist name": "UTChecklist2", "Checklist items": "* Item 1"}',
                'title' => "UTChecklist2"
            )
        );

        $this->assertArrayNotHasKey( 'error', $data[0] );
        $this->assertArrayHasKey( 'radiant', $data[0] );
        $this->assertArrayHasKey( 'status', $data[0]['radiant'] );
        $this->assertEquals( 'success', $data[0]['radiant']['status'] );
        $this->assertArrayHasKey( 'touched_unix', $data[0]['radiant'] );

    }


    /**
     * @covers RadiantApiEndpoint::action_clause_put
     */
    public function testChecklistEdit()
    {

        $data = $this->doApiRequest(
            array(
                'action' => 'radiant',
                'method' => 'checklist/put/'.self::$_checklistId,
                'data' => '{"Checklist name": "UTChecklist_edited", "Checklist items": "* Item 1_edited"}'
            )
        );

        $this->assertArrayNotHasKey( 'error', $data[0] );
        $this->assertArrayHasKey( 'radiant', $data[0] );
        $this->assertArrayHasKey( 'status', $data[0]['radiant'] );
        $this->assertEquals( 'success', $data[0]['radiant']['status'] );
        $this->assertArrayHasKey( 'touched_unix', $data[0]['radiant'] );

    }

    /**
     * Verify proper error handling
     * @coversNothing
     */
    public function testChecklistError()
    {
        $data = $this->doApiRequest(
            array(
                'action' => 'radiant',
                'method' => 'checklist/get/-1'
            )
        );

        $this->assertArrayHasKey( 'error', $data[0] );
        $this->assertEquals( 'unknown_title', $data[0]['error']['code'] );

    }

    /**
     * @covers RadiantApiEndpoint::action_checklist_delete
     */
    public function testChecklistDelete()
    {
        $data = $this->doApiRequest(
            array(
                'action' => 'radiant',
                'method' => 'checklist/delete/'.self::$_checklistId
            )
        );
        $this->assertEquals( null, Title::newFromID( self::$_checklistId ) );
    }

}