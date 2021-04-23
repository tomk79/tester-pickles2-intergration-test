<?php
/**
 * test for Pickles 2
 */
class mainTest extends PHPUnit_Framework_TestCase{
	private $fs;
	private $utils;

	public function setup(){
		mb_internal_encoding('UTF-8');
		$this->fs = new tomk79\filesystem();
		require_once(__DIR__.'/libs/utils.php');
		$this->utils = new \utils();
	}


	/**
	 * インスタンス化して実行してみるテスト
	 */
	public function testStandard(){
		$cd = realpath('.');
		$SCRIPT_FILENAME = $_SERVER['SCRIPT_FILENAME'];
		chdir(__DIR__.'/../project-001/src_px2/');
		$_SERVER['SCRIPT_FILENAME'] = __DIR__.'/../project-001/src_px2/.px_execute.php';

		$px = new picklesFramework2\px(__DIR__.'/../project-001/px-files/');
		$toppage_info = $px->site()->get_page_info('');
		// var_dump($toppage_info);
		$this->assertEquals( $toppage_info['title'], 'ホーム' );
		$this->assertEquals( $toppage_info['path'], '/index.html' );
		$this->assertEquals( $_SERVER['HTTP_USER_AGENT'], '' );

		$this->assertEquals( $px->get_scheme(), 'https' );
		$this->assertEquals( $px->get_domain(), null );


		// サブリクエストでキャッシュを消去
		$output = $px->internal_sub_request(
			'/index.html?PX=clearcache',
			array(),
			$vars
		);
		$this->assertTrue( $this->utils->common_error( $output ) );
		$error = $px->get_errors();
		// var_dump($output);
		// var_dump($vars);
		// var_dump($error);
		$this->assertTrue( is_string($output) );
		$this->assertSame( 0, $vars ); // <- strict equals
		$this->assertSame( array(), $error );


		chdir($cd);
		$_SERVER['SCRIPT_FILENAME'] = $SCRIPT_FILENAME;

		$px->__destruct();// <- required on Windows
		unset($px);
	}

	/**
	 * パブリッシュコマンドを実行してみるテスト
	 */
	public function testPublish(){
		$output = $this->utils->px_execute( 
			'/project-001/src_px2/.px_execute.php' ,
			'/?PX=publish.run'
		);
		clearstatcache();
		// var_dump($output);
		$this->assertTrue( $this->utils->common_error( $output ) );
	}

	/**
	 * キャッシュを消去するテスト
	 */
	public function testClearcache(){
		$output = $this->utils->px_execute(
			'/project-001/src_px2/.px_execute.php' ,
			'/?PX=clearcache'
		);
		clearstatcache();
		// var_dump($output);
		$this->assertTrue( $this->utils->common_error( $output ) );
	}

}
