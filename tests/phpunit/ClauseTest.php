<?php

/**
 * Class RadiantAPIClauseTest
 * @group Database
 * @group medium
 * @group API
 */
class RadiantAPIClauseTest extends ApiTestCase {
    
    protected static $_clauseId = null;
    protected static $_clauseId_approved = null;

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
        //parent::teardownTestDB();
    }

    /**
     * Create Clause template and adds 1 clause item into database
     * @throws Exception
     * @throws MWException
     */
    public function addDBData()
    {

        if ( Title::newFromText( 'Clause', NS_TEMPLATE )->exists() ) {
            return;
        }

        $title = Title::newFromText( "Clause", NS_TEMPLATE );

        $user = User::newFromName( 'UTSysop' );
        $comment = __METHOD__ . ': Sample page for unit test.';

        $page = WikiPage::factory( $title );
        $page->doEditContent( ContentHandler::makeContent(
            "[[Category:Clause]]"
            , $title ), $comment, 0, false, $user );

        $result = $this->insertPage( "UTClause", "{{Clause||Clause type=Sample clause type||Bias=Lorem ipsum|Clause source=|Choice of law=|Clause status=|Approved clause=|Length=}}" );
        $result_approved = $this->insertPage( "UTClauseApproved", "{{Clause||Clause type=Sample clause type||Bias=Lorem ipsum|Clause source=|Choice of law=|Clause status=|Approved clause=Yes|Length=}}" );

        self::$_clauseId = $result['id'];
        self::$_clauseId_approved = $result_approved['id'];

    }

    /**
     * @covers RadiantApiEndpoint::action_clause_get
     */
    public function testClauseGet()
    {
        
        $data = $this->doApiRequest(
            array(
                'action' => 'radiant',
                'method' => 'clause/get'
            )
        );

        $this->assertArrayNotHasKey( 'error', $data[0] );
        $this->assertArrayHasKey( 'radiant', $data[0] );
        $this->assertArrayHasKey( 'items', $data[0]['radiant'] );
        //$this->assertEquals( 1, count($data[0]['radiant']['items']) );
        //$this->assertEquals( 'UTClause', $data[0]['radiant']['items'][0]['title'] );

    }

    /**
     * @covers RadiantApiEndpoint::action_clause_get
     */
    public function testClauseGetOne()
    {
        $data = $this->doApiRequest(
            array(
                'action' => 'radiant',
                'method' => 'clause/get/'.self::$_clauseId
            )
        );

        $this->assertArrayNotHasKey( 'error', $data[0] );
        $this->assertArrayHasKey( 'radiant', $data[0] );
        $this->assertArrayHasKey( 'items', $data[0]['radiant'] );
        $this->assertEquals( 1, count($data[0]['radiant']['items']) );
        //$this->assertEquals( 'UTClause', $data[0]['radiant']['items'][0]['title'] );
    }

    /**
     * @covers RadiantApiEndpoint::action_clause_put
     */
    public function testClausePut()
    {
        $data = $this->doApiRequest(
            array(
                'action' => 'radiant',
                'method' => 'clause/put',
                'data' => '{"Clause type": "Sample clause type", "Bias": "Lorem ipsum lorem ipsum"}',
                'title' => "UTClause2",
                //'content' => 'Reproduce wihtout peace, and we won’t avoid a queen.',
                //'terms' => '[ { "Term": "Sample term 1", "Definition": "This is sample definition 1" } ]'
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
    public function testClauseEdit()
    {
        $data = $this->doApiRequest(
            array(
                'action' => 'radiant',
                'method' => 'clause/put/'.self::$_clauseId,
                'data' => '{"Clause type": "Sample clause type 123", "Bias": "Lorem ipsum lorem ipsum 123"}',
                //'title' => "UTClause2",
                //'content' => 'Reproduce wihtout peace, and we won’t avoid a queen.',
                //'terms' => '[ { "Term": "Sample term 1", "Definition": "This is sample definition 1" } ]'
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
    public function testClauseEditContent()
    {
        $data = $this->doApiRequest(
            array(
                'action' => 'radiant',
                'method' => 'clause/put/'.self::$_clauseId,
                'content' => 'Lunar, fantastic girls surprisingly promise a vital, terrifying c-beam.',
                'data' => '{"Clause type": "Sample clause type 12345", "Bias": "Lorem ipsum lorem ipsum 12345"}',
            )
        );

        $this->assertArrayNotHasKey( 'error', $data[0] );
        $this->assertArrayHasKey( 'radiant', $data[0] );
        $this->assertArrayHasKey( 'status', $data[0]['radiant'] );
        $this->assertEquals( 'success', $data[0]['radiant']['status'] );
        $this->assertArrayHasKey( 'content', $data[0]['radiant'] );

    }

    /**
     * @covers RadiantApiEndpoint::action_clause_put
     */
    public function testClauseEditTerms()
    {
        $data = $this->doApiRequest(
            array(
                'action' => 'radiant',
                'method' => 'clause/put/'.self::$_clauseId,
                'terms' => '[ { "Term": "Sample term 123", "Definition": "This is sample definition 123" } ]',
                'data' => '{"Clause type": "Sample clause type 12345", "Bias": "Lorem ipsum lorem ipsum 12345"}',
            )
        );

        $this->assertArrayNotHasKey( 'error', $data[0] );
        $this->assertArrayHasKey( 'radiant', $data[0] );
        $this->assertArrayHasKey( 'status', $data[0]['radiant'] );
        $this->assertEquals( 'success', $data[0]['radiant']['status'] );

    }

    /**
     * @covers RadiantApiEndpoint::action_clause_delete
     */
    public function testClauseDelete()
    {
        $data = $this->doApiRequest(
            array(
                'action' => 'radiant',
                'method' => 'clause/delete/'.self::$_clauseId
            )
        );
        $this->assertEquals( null, Title::newFromID( self::$_clauseId ) );
    }

    public function testClauseActionStatusNew()
    {
        $data = $this->doApiRequest(
            array(
                'action' => 'radiant',
                'method' => 'clause/put',
                'data' => '{"Clause type": "Sample clause type", "Bias": "Lorem ipsum lorem ipsum"}',
                'title' => "UTClause3",
                'content' => 'Reproduce wihtout peace, and we won’t avoid a queen.',
                'terms' => '[ { "Term": "Sample term 1", "Definition": "This is sample definition 1" } ]'
            )
        );

        // Ensure we have 'new' action value
        $this->assertArrayHasKey( 'action', $data[0]['radiant'] );
        $this->assertEquals( 'new', $data[0]['radiant']['action'] );

        // Ensure we have 'new' action value for content
        $this->assertArrayHasKey( 'content', $data[0]['radiant'] );
        $this->assertArrayHasKey( 'action', $data[0]['radiant']['content'] );
        $this->assertEquals( 'new', $data[0]['radiant']['content']['action'] );

        self::$_clauseId = $data[0]['radiant']['page_id'];

    }

    public function testClauseActionStatusUpdateOnPropertiesEdit()
    {
        $data = $this->doApiRequest(
            array(
                'action' => 'radiant',
                'method' => 'clause/put/' . self::$_clauseId,
                'data' => '{"Clause type": "Sample clause type 123", "Bias": "Lorem ipsum lorem ipsum 123"}',
                'content' => 'Reproduce wihtout peace, and we won’t avoid a queen.',
                'terms' => '[ { "Term": "Sample term 1", "Definition": "This is sample definition 1" } ]'
            )
        );

        // Ensure we have 'new' action value
        $this->assertArrayHasKey( 'action', $data[0]['radiant'] );
        $this->assertEquals( 'update', $data[0]['radiant']['action'] );

        // Ensure we have 'new' action value for content
        $this->assertArrayHasKey( 'content', $data[0]['radiant'] );
        $this->assertArrayHasKey( 'action', $data[0]['radiant']['content'] );
        $this->assertEquals( 'nothing', $data[0]['radiant']['content']['action'] );

    }

    public function testClauseActionStatusUpdateOnContentEdit()
    {
        $data = $this->doApiRequest(
            array(
                'action' => 'radiant',
                'method' => 'clause/put/' . self::$_clauseId,
                'data' => '{"Clause type": "Sample clause type 123", "Bias": "Lorem ipsum lorem ipsum 123"}',
                'content' => 'Reproduce wihtout peace, and we won’t avoid a queen. 123',
                'terms' => '[ { "Term": "Sample term 1", "Definition": "This is sample definition 1" } ]'
            )
        );

        // Ensure we have 'new' action value
        $this->assertArrayHasKey( 'action', $data[0]['radiant'] );
        $this->assertEquals( 'nothing', $data[0]['radiant']['action'] );

        // Ensure we have 'new' action value for content
        $this->assertArrayHasKey( 'content', $data[0]['radiant'] );
        $this->assertArrayHasKey( 'action', $data[0]['radiant']['content'] );
        $this->assertEquals( 'update', $data[0]['radiant']['content']['action'] );

    }

    public function testClauseApprovalInformationList()
    {
        $data = $this->doApiRequest(
            array(
                'action' => 'radiant',
                'method' => 'clause/get'
            )
        );

        $this->assertArrayNotHasKey( 'error', $data[0] );
        $this->assertArrayHasKey( 'radiant', $data[0] );
        $this->assertArrayHasKey( 'items', $data[0]['radiant'] );

	    $this->assertEquals( 3, count( $data[0]['radiant']['items'] ) );

        $this->assertEquals( 0, $data[0]['radiant']['items'][1]['approvable'] );
        $this->assertEquals( 0, $data[0]['radiant']['items'][1]['content']['approvable'] );

    }

	public function testClauseApprovalInformationOne()
	{
		$data = $this->doApiRequest(
			array(
				'action' => 'radiant',
				'method' => 'clause/get/' . self::$_clauseId
			)
		);

		$this->assertArrayNotHasKey( 'error', $data[0] );
		$this->assertArrayHasKey( 'radiant', $data[0] );
		$this->assertArrayHasKey( 'items', $data[0]['radiant'] );

		$this->assertEquals( 0, $data[0]['radiant']['items'][0]['approvable'] );
		$this->assertEquals( 0, $data[0]['radiant']['items'][0]['content']['approvable'] );

	}

	public function testClauseApprovableHijack()
	{

		$data = $this->doApiRequest(
			array(
				'action' => 'radiant',
				'method' => 'clause/put/' . self::$_clauseId_approved,
				'data' => '{"Clause type": "Sample clause type 123", "Bias": "Lorem ipsum lorem ipsum 123", "Approved clause": "No"}',
				'content' => 'Reproduce wihtout peace, and we won’t avoid a queen. 123456',
				'terms' => '[ { "Term": "Sample term 123", "Definition": "This is sample definition 123" } ]'
			)
		);

		// Ensure we have 'new' action value
		$this->assertArrayHasKey( 'action', $data[0]['radiant'] );

		$data = $this->doApiRequest(
			array(
				'action' => 'radiant',
				'method' => 'clause/get/' . self::$_clauseId_approved
			)
		);

		// Clause still approvable regardless of our attempt to alter 'Approved Clause' flag
		$this->assertEquals( 1, $data[0]['radiant']['items'][0]['approvable'], 'Clause' );
		$this->assertEquals( 1, $data[0]['radiant']['items'][0]['content']['approvable'], 'Content' );

	}

}