<?php

declare(strict_types=1);

use local_companyservice\external\get_infouser;

global $CFG;
require_once($CFG->dirroot .'/webservice/tests/helpers.php');
require_once ($CFG->dirroot.'/local/companyservice/classes/external/get_infouser.php');

class get_infouser_test extends \externallib_advanced_testcase {
    public function test_empty_username() {
        $class = new get_infouser();
        
        $this->expectException(\invalid_parameter_exception::class);
        
        $returnValue=$class->execute('');
    }
    public function test_noexistent_username() {
        $class = new get_infouser();
        
        $this->expectException(\invalid_parameter_exception::class);
        
        $returnValue=$class->execute('johndoe');
    }
    public function test_existent_username() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        
        $this->getDataGenerator()->create_custom_profile_field([
            'shortname' => 'agency',
            'name' => 'Agencia',
            'datatype' => 'text',
            'default' => 'no-agency'
        ]);

        $newUser = $this->getDataGenerator()->create_user(array('firstname'=>'user1', 'email'=>'user1@example.com', 'username'=>'user1', 'profile_field_agency'=>'agencia prueba'));
        
        $class = new get_infouser();
        
        $returnValue=$class->execute($newUser->username);
        
        $this->assertEquals($returnValue->firstname, $newUser->firstname);
        $this->assertEquals($returnValue->email, $newUser->email);
        $this->assertEquals($returnValue->username, $newUser->username);
        $this->assertEquals($returnValue->agency, 'agencia prueba');
        
    }
}