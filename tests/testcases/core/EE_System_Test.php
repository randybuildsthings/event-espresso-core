<?php

if (!defined('EVENT_ESPRESSO_VERSION'))
	exit('No direct script access allowed');

/**
 *
 * EE_System_Test
 *
 * @package			Event Espresso
 * @subpackage		
 * @author				Mike Nelson
 *
 */
/**
 * @group core
 */
class EE_System_Test extends EE_UnitTestCase{
	public function test_detect_request_type(){
		//we will be playing with this option which might need to persist after the test
		$original_espresso_db_update= get_option('espresso_db_update');
		
		EE_System::reset();
		$this->_pretend_espresso_db_update_is(array(
			espresso_version() => array(current_time('mysql'))
		));
		$request_type = EE_System::instance()->detect_req_type();
		$this->assertEquals(EE_System::req_type_normal,$request_type);
		//check that it detects an upgrade
		EE_System::reset();
		$this->_pretend_espresso_db_update_is(array(
			$this->_add_to_version(espresso_version(), '0.0.-1.0.0') => array(current_time('mysql'))
		));
		$request_type = EE_System::instance()->detect_req_type();
		$this->assertEquals(EE_System::req_type_upgrade,$request_type);
		//check that it detects activation
		EE_System::reset();
		$this->_pretend_espresso_db_update_is(NULL);
		$request_type = EE_System::instance()->detect_req_type();
		$this->assertEquals(EE_System::req_type_new_activation,$request_type);
		//check that it detects downgrade, even though we don't really care atm
//		EE_System::reset();
//		$this->_pretend_espresso_db_upgrade_is(array(
//			$this->_add_to_version(espresso_version(), '0.1.0.0.0') => array(current_time('mysql'))
//		));
//		$request_type = EE_System::instance()->detect_req_type();
//		$this->assertEquals(EE_System::req_type_downgrade,$request_type);
		//lastly, check that we detect reactivations
		EE_System::reset();
		update_option('ee_espresso_activation',true);
		$this->_pretend_espresso_db_update_is(array(
				espresso_version() => array(current_time('mysql'))
				));
		$request_type = EE_System::instance()->detect_req_type();
		$this->assertEquals(EE_System::req_type_reactivation,$request_type);
		//reset espresso_db_update... might not be necessary if I understood
		//what happens to the db between test functions better
		$this->_pretend_espresso_db_update_is($original_espresso_db_update);
		EE_System::reset();
		EE_System::instance()->detect_req_type();
	}
	/**
	 * just tests the EE_System_Test::_add_to_version private method to make sure it's working
	 * (because other tests depend on it)
	 */
	function test_add_to_version(){
		$version = '4.3.2.alpha.1';
		$version_to_add = '1.0.-1.1.1';
		$new_version = $this->_add_to_version($version, $version_to_add);
		$this->assertEquals('5.3.1.alpha.2',$new_version);
	}
	
	/**
	 * Sets the wordpress option 'espresso_db_update'
	 * @param array $espresso_db_upgrade top-level-keys shoudl be version numbers,
	 * and their values should be an array of mysql datetimes when that version was activated
	 */
	private function _pretend_espresso_db_update_is($espresso_db_upgrade){
		if($espresso_db_upgrade === NULL){
			delete_option('espresso_db_update',$espresso_db_upgrade);
		}else{
			update_option( 'espresso_db_update',$espresso_db_upgrade );
		}
	}
	
	/**
	 * 
	 * @param string $version_string eg "4.3.2.alpha.003
	 * @param string $version_amount_to_add eg "0.0.0.0.1"
	 * @return string eg if given the mentioned inputs, would be "4.3.2.alpha.4";
	 */
	private function _add_to_version($version_string,$version_amount_to_add = '0.0.1'){
		$version_parts = explode(".",$version_string);
		$version_amount_to_add_parts = explode(".",$version_amount_to_add);
		foreach($version_parts as $key => $version_part){
			if(is_numeric($version_part)){
				$version_parts[$key] = $version_parts[$key] + $version_amount_to_add_parts[$key];
			}
		}
		return implode(".",$version_parts);
	}
}

// End of file EE_System_Test.php