<?php

/**
 * Class RadiantAPIChecklistTest
 * @group medium
 */
class RadiantAPIChecklistTest extends ApiTestCase
{
    protected $_checklistId = null;

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
     * Create Checklist template and adds 1 checklist item into database
     * @throws Exception
     * @throws MWException
     */
    public function addDBData()
    {
        $title = Title::newFromText( "Checklist", NS_TEMPLATE );

        $user = User::newFromName( 'UTSysop' );
        $comment = __METHOD__ . ': Sample page for unit test.';

        $page = WikiPage::factory( $title );
        $page->doEditContent( ContentHandler::makeContent(
            "[[Checklist name::{{{Checklist name|}}}]] " .
            "{{#if: {{{Checklist items}}}| [[Checklist items::{{{Checklist items}}}| ]] {{{Checklist items}}} }}" .
            " [[Category:Checklist]]"
            , $title ), $comment, 0, false, $user );

        $result = $this->insertPage( "UTChecklist", "{{Checklist||Checklist name=UTChecklist||Checklist items=* Test item 1}}" );
        $this->_checklistId = $result['id'];

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
                'method' => 'checklist/get/'.$this->_checklistId
            )
        );

        $this->assertArrayNotHasKey( 'error', $data[0] );
        $this->assertArrayHasKey( 'radiant', $data[0] );
        $this->assertArrayHasKey( 'items_count', $data[0]['radiant'] );
        $this->assertArrayHasKey( 'items', $data[0]['radiant'] );
        $this->assertEquals( 1, $data[0]['radiant']['items_count'] );
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

    /*public function testChecklistEdit()
    {

        $data = $this->doApiRequest(
            array(
                'action' => 'radiant',
                'method' => 'checklist/put/'.$this->_checklistId,
                'data' => '{"Checklist name": "UTChecklist_edited", "Checklist items": "* Item 1_edited"}'
            )
        );

        $this->assertArrayNotHasKey( 'error', $data[0] );
        $this->assertArrayHasKey( 'radiant', $data[0] );
        $this->assertArrayHasKey( 'status', $data[0]['radiant'] );
        $this->assertEquals( 'success', $data[0]['radiant']['status'] );
        $this->assertArrayHasKey( 'touched_unix', $data[0]['radiant'] );

    }*/

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

}