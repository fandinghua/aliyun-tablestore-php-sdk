<?php

namespace Aliyun\OTS\Tests;

use Aliyun\OTS\Consts\ComparatorTypeConst;
use Aliyun\OTS\Consts\PrimaryKeyTypeConst;
use Aliyun\OTS\Consts\RowExistenceExpectationConst;

require_once __DIR__ . '/TestBase.php';
require_once __DIR__ . '/../../vendor/autoload.php';


class PutRowTest extends SDKTestBase {

    private static $usedTables = array (
        'myTable',
        'myTable1',
        'TableAll',
    );

    public static function setUpBeforeClass()
    {
        SDKTestBase::cleanUp ( self::$usedTables );
        SDKTestBase::createInitialTable ( array (
            'table_meta' => array (
                'table_name' => self::$usedTables[0],
                'primary_key_schema' => array (
                    array('PK1', PrimaryKeyTypeConst::CONST_INTEGER),
                    array('PK2', PrimaryKeyTypeConst::CONST_STRING)
                )
            ),
            'reserved_throughput' => array (
                'capacity_unit' => array (
                    'read' => 0,
                    'write' => 0
                )
            ),
            'table_options' => array(
                'time_to_live' => -1,
                'max_versions' => 2,
                'deviation_cell_version_in_sec' => 86400
            )
        ) );
        SDKTestBase::createInitialTable ( array (
            'table_meta' => array (
                'table_name' => self::$usedTables[1],
                'primary_key_schema' => array (
                    array('PK1', PrimaryKeyTypeConst::CONST_INTEGER),
                    array('PK2', PrimaryKeyTypeConst::CONST_STRING)
                )
            ),
            'reserved_throughput' => array (
                'capacity_unit' => array (
                    'read' => 0,
                    'write' => 0
                )
            ),
            'table_options' => array(
                'time_to_live' => -1,
                'max_versions' => 2,
                'deviation_cell_version_in_sec' => 86400
            )
        ) );

        SDKTestBase::createInitialTable ( array (
            'table_meta' => array (
                'table_name' => self::$usedTables[2],
                'primary_key_schema' => array (
                    array('PK1', PrimaryKeyTypeConst::CONST_INTEGER),
                    array('PK2', PrimaryKeyTypeConst::CONST_STRING),
                    array('PK3', PrimaryKeyTypeConst::CONST_BINARY)
                )
            ),
            'reserved_throughput' => array (
                'capacity_unit' => array (
                    'read' => 0,
                    'write' => 0
                )
            ),
            'table_options' => array(
                'time_to_live' => -1,
                'max_versions' => 2,
                'deviation_cell_version_in_sec' => 86400
            )
        ) );

        SDKTestBase::waitForTableReady ();
    }

    public static function tearDownAfterClass()
    {
        SDKTestBase::cleanUp ( self::$usedTables );
    }

    /*
     *
     * TableNameOfZeroLength
     * ???????????????0???????????????????????????????????????Invalid table name: '{TableName}'. ????????????TableName??????????????????
     */
    public function testTableNameOfZeroLength() {
        $tablename1 = array (
            'table_name' => '',
            'condition' => RowExistenceExpectationConst::CONST_IGNORE,
            'primary_key' => array (
                array('PK1', 1),
                array('PK2', 'a1')
            ),
            'attribute_columns' => array (
                array('attr1', 'name'),
                array('attr2', 256)
            ) 
        );
        try {
            $this->otsClient->putRow ( $tablename1 );
        } catch ( \Aliyun\OTS\OTSServerException $exc ) {
            $c = "Invalid table name: ''.";
            $this->assertEquals ( $c, $exc->getOTSErrorMessage () );
        }
        // print_r($this->otsClient->putRow($tablename));
        // die;
        $tablename2 = array (
            'table_name' => 'testU+0053',
            'condition' => RowExistenceExpectationConst::CONST_IGNORE,
            'primary_key' => array (
                array('PK1', 1),
                array('PK2', 'a1')
            ),
            'attribute_columns' => array (
                array('attr1', 'name'),
                array('attr2', 256)
            ) 
        );
        try {
            $this->otsClient->putRow ( $tablename2 );
            $this->fail ( 'An expected exception has not been raised.' );
        } catch ( \Aliyun\OTS\OTSServerException $exc ) {
            $c = "Invalid table name: 'testU+0053'.";
            $this->assertEquals ( $c, $exc->getOTSErrorMessage () );
        }
    }
    
    /*
     * ColumnNameOfZeroLength
     * ???????????????0???????????????????????????????????????Invalid column name: '{ColumnName}'. ????????????ColumnName???????????????
     * ColumnNameWithUnicode
     * ????????????Unicode??????????????????????????????Invalid column name: '{ColumnName}'. ????????????ColumnName??????????????????
     * 1KBColumnName
     * ????????????Unicode??????????????????????????????Invalid column name: '{ColumnName}'. ????????????ColumnName??????????????????
     */
    public function testColumnNameLength() {
        // ColumnNameOfZeroLength
        $tablename1 = array (
            'table_name' => self::$usedTables[0],
            'condition' => RowExistenceExpectationConst::CONST_IGNORE,
            'primary_key' => array (
                array('PK1', 1),
                array('PK2', 'a1')
            ),
            'attribute_columns' => array (
                array('', 'name'),
                array('attr2', 256)
            ) 
        );
        try {
            $this->otsClient->putRow ( $tablename1 );
            $this->fail ( 'An expected exception has not been raised.' );
        } catch ( \Aliyun\OTS\OTSServerException $exc ) {
            $c = "Invalid column name: ''.";
            $this->assertEquals ( $c, $exc->getOTSErrorMessage () );
        }
        // ColumnNameWithUnicode
        $tablename2 = array (
            'table_name' => self::$usedTables[0],
            'condition' => RowExistenceExpectationConst::CONST_IGNORE,
            'primary_key' => array (
                array('PK1', 1),
                array('PK2', 'a1')
            ),
            'attribute_columns' => array (
                array('#name', 'name'),
                array('attr2', 256)
            ) 
        );
        try {
            $this->otsClient->putRow ( $tablename2 );
            $this->fail ( 'An expected exception has not been raised.' );
        } catch ( \Aliyun\OTS\OTSServerException $exc ) {
            $c = "Invalid column name: '#name'.";
            $this->assertEquals ( $c, $exc->getOTSErrorMessage () );
        }
        // 1KBColumnName
        $name = '';
        for($i = 1; $i < 1025; $i ++) {
            $name .= 'a';
        }
        $tablename3 = array (
            'table_name' => self::$usedTables[0],
            'condition' => RowExistenceExpectationConst::CONST_IGNORE,
            'primary_key' => array (
                array('PK1', 1),
                array('PK2', 'a1')
            ),
            'attribute_columns' => array (
                array("{$name}", 'name'),
                array('attr2', 256)
            ) 
        );
        try {
            $this->otsClient->putRow ( $tablename3 );
            $this->fail ( 'An expected exception has not been raised.' );
        } catch ( \Aliyun\OTS\OTSServerException $exc ) {
            $c = "Invalid column name: '{$name}'.";
            $this->assertEquals ( $c, $exc->getOTSErrorMessage () );
        }
    }
    
    /*
     * 10WriteCUConsumed
     * ??????????????????10??????CU????????????CU Consumed???????????????
     */
    public function testWrite10CUConsumed() {
        $name = '';
        for($i = 1; $i < (4097 * 9); $i ++) {
            $name .= 'a';
        }
        $tablename = array (
            'table_name' => self::$usedTables[1],
            'condition' => RowExistenceExpectationConst::CONST_IGNORE,
            'primary_key' => array (
                array('PK1', 1),
                array('PK2', 'a1')
            ),
            'attribute_columns' => array (
                array('att2', $name)
            )
        );
        
        if (is_array ( $this->otsClient->putRow ( $tablename ) )) {
            $name = $this->otsClient->putRow ( $tablename );
            $this->assertEquals ( $name['consumed']['capacity_unit']['write'], 10 );
            $this->assertEquals ( $name['consumed']['capacity_unit']['read'], 0 );
        }
        // print_r($name['consumed']['capacity_unit']['write']);die;
        // $getrow = $this->otsClient->putRow($tablename);
    }
    
    /*
     * ???????????????????????????
     * NormanStringValue
     * ??????StringValue???10?????????????????????
     * UnicodeStringValue
     * ??????StringValue??????Unicode??????????????????
     */
    public function testNormanStringValue() {
        $name = '';
        for($i = 1; $i < (1025 * 10); $i ++) {
            $name .= 'a';
        }
        // echo strlen($a);die;
        $tablename = array (
            'table_name' => self::$usedTables[0],
            'condition' => RowExistenceExpectationConst::CONST_IGNORE,
            'primary_key' => array (
                array('PK1', 11),
                array('PK2', 'a11')
            ),
            'attribute_columns' => array (
                array('att2', $name)
            ) 
        );
        $this->otsClient->putRow ( $tablename );
        $body = array (
            'table_name' => self::$usedTables[0],
            'primary_key' => array (
                array('PK1', 11),
                array('PK2', 'a11')
            ),
            'max_versions' => 1,
            'columns_to_get' => array () 
        );
        
        if (is_array ( $this->otsClient->getRow ( $body ) )) {
            $name = $this->otsClient->getRow ( $body );
            $this->assertColumnEquals($tablename['attribute_columns'], $name['attribute_columns']);
        }
        // print_r($name);die;
        // $getrow = $this->otsClient->putRow($tablename);
        //
    }
    
    /*
     * ???????????????????????????
     * UnicodeStringValue
     * ??????StringValue??????Unicode??????????????????
     * EmptyStringValue
     * ??????????????????????????????
     */
    public function testUnicodeStringValue() {
        // echo strlen($a);die;
        $tablename = array (
            'table_name' => self::$usedTables[0],
            'condition' => RowExistenceExpectationConst::CONST_IGNORE,
            'primary_key' => array (
                array('PK1', 12),
                array('PK2', 'a12')
            ),
            'attribute_columns' => array (
                array('att1', 'sdfv\u597d'),
                array('att2', 'U+0053'),
                array('att3', '????????????')
            )
        );
        $this->otsClient->putRow ( $tablename );
        $body = array (
            'table_name' => self::$usedTables[0],
            'primary_key' => array (
                array('PK1', 12),
                array('PK2', 'a12')
            ),
            'max_versions' => 1
        );
        
        if (is_array ( $this->otsClient->getRow ( $body ) )) {
            $name = $this->otsClient->getRow ( $body );
            // UnicodeStringValue
            $this->assertColumnEquals($tablename['attribute_columns'], $name['attribute_columns']);
        }
        $tablename = array (
            'table_name' => self::$usedTables[0],
            'condition' => RowExistenceExpectationConst::CONST_IGNORE,
            'primary_key' => array (
                array('PK1', 13),
                array('PK2', 'a13')
            ),
            'attribute_columns' => array (
                array('att1', ''),
                array('att2', '')
            ) 
        );
        $this->otsClient->putRow ( $tablename );
        $body = array (
            'table_name' => self::$usedTables[0],
            'primary_key' => array (
                array('PK1', 13),
                array('PK2', 'a13')
            ),
            'max_versions' => 1
        );
        
        if (is_array ( $this->otsClient->getRow ( $body ) )) {
            $name = $this->otsClient->getRow ( $body );
            // UnicodeStringValue
            $this->assertColumnEquals($tablename['attribute_columns'], $name['attribute_columns']);
        }
        // print_r($name);die;
        // $getrow = $this->otsClient->putRow($tablename);
        //
    }
    
    /*
     * StringValueTooLong
     * ????????????????????????1MB???????????????????????????????????? ??????2097152???
     */
    public function testStringValueTooLong() {

        $name = str_repeat('a', 1024 * 1024 * 3);

        // echo strlen($a);die;
        $tablename = array (
            'table_name' => self::$usedTables[0],
            'condition' => RowExistenceExpectationConst::CONST_IGNORE,
            'primary_key' => array (
                array('PK1', 20),
                array('PK2', 'a20')
            ),
            'attribute_columns' => array (
                array('att1', $name)
            ) 
        );
        try {
            $this->otsClient->putRow ( $tablename );
            $this->fail ( 'An expected exception has not been raised.' );
        } catch ( \Aliyun\OTS\OTSServerException $exc ) {
            $c = "The length of attribute column: 'att1' exceeds the MaxLength:";
            $this->assertContains ( $c, $exc->getOTSErrorMessage () );
        }
    }
    
    /*
     * CASE_ID: NormalIntegerValue
     * ??????IntegerValue??????10????????????
     * CASE_ID: IntegerValueInBoundary
     * ??????IntegerValue?????????8???????????????????????????????????????????????????
     * ???8?????????getRow???????????????4293856185
     * ???8???????????????4293856185?????????0
     */
    public function testIntegerValue() {
        $tablename = array (
            'table_name' => self::$usedTables[0],
            'condition' => RowExistenceExpectationConst::CONST_IGNORE,
            'primary_key' => array (
                array('PK1', 30),
                array('PK2', 'a30')
            ),
            'attribute_columns' => array (
                array('attr10', -10)
            ) 
        );
        $this->otsClient->putRow ( $tablename );
        $body = array (
            'table_name' => self::$usedTables[0],
            'primary_key' => array (
                array('PK1', 30),
                array('PK2', 'a30')
            ),
            'max_versions' => 1,
            'columns_to_get' => array () 
        );
        $getrow = $this->otsClient->getRow ( $body );
        $this->assertColumnEquals($tablename['attribute_columns'], $getrow['attribute_columns']);
        
        $tablename = array (
            'table_name' => self::$usedTables[0],
            'condition' => RowExistenceExpectationConst::CONST_IGNORE,
            'primary_key' => array (
                array('PK1', 31),
                array('PK2', 'a31')
            ),
            'attribute_columns' => array (
                array('attr1', 1),
                array('attr2', 0),
                array('attr3', 4293856185)
            ) 
        );
        $this->otsClient->putRow ( $tablename );
        $body = array (
            'table_name' => self::$usedTables[0],
            'primary_key' => array (
                array('PK1', 31),
                array('PK2', 'a31')
            ),
            'max_versions' => 1,
            'columns_to_get' => array () 
        );
        $getrow1 = $this->otsClient->getRow ( $body );
        // echo $getrow['attribute_columns']['attr1'];die;
        $this->assertColumnEquals($tablename['attribute_columns'], $getrow1['attribute_columns']);
    }
    
    /*
     * NormalDoubleValue
     * ??????DoubleValue??????3.1415926????????????
     * DoubleValueInBoundary
     * ??????DoubleValue?????????8??????????????????????????????????????????????????????
     */
    public function testDoubleValue() {
        $tablename = array (
            'table_name' => self::$usedTables[0],
            'condition' => RowExistenceExpectationConst::CONST_IGNORE,
            'primary_key' => array (
                array('PK1', 40),
                array('PK2', 'a40')
            ),
            'attribute_columns' => array (
                array('attr10', 3.1415926)
            )
        );
        $this->otsClient->putRow ( $tablename );
        $body = array (
            'table_name' => self::$usedTables[0],
            'primary_key' => array (
                array('PK1', 40),
                array('PK2', 'a40')
            ),
            'max_versions' => 1,
            'columns_to_get' => array () 
        );
        $getrow = $this->otsClient->getRow ( $body );
        $this->assertColumnEquals($tablename['attribute_columns'], $getrow['attribute_columns']);

        $tablename = array (
            'table_name' => self::$usedTables[0],
            'condition' => RowExistenceExpectationConst::CONST_IGNORE,
            'primary_key' => array (
                array('PK1', 41),
                array('PK2', 'a41')
            ),
            'attribute_columns' => array (
                array('attr11', -0.0000001),
                array('attr12', 9.9999999)
            ) 
        );
        $this->otsClient->putRow ( $tablename );
        $body = array (
            'table_name' => self::$usedTables[0],
            'primary_key' => array (
                array('PK1', 41),
                array('PK2', 'a41')
            ),
            'max_versions' => 1,
            'columns_to_get' => array () 
        );
        $getrow1 = $this->otsClient->getRow ( $body );
        // echo $getrow1['attribute_columns']['attr11'];die;
        $this->assertColumnEquals($tablename['attribute_columns'], $getrow1['attribute_columns']);
    }

    /*
     * AllValue
     * ??????????????????????????????
     */
    public function testAllValue() {
        $tablename = array (
            'table_name' => self::$usedTables[2],
            'condition' => RowExistenceExpectationConst::CONST_IGNORE,
            'primary_key' => array (
                array('PK1', 40),
                array('PK2', 'a40'),
                array('PK3', '\x01\x02\x03', PrimaryKeyTypeConst::CONST_BINARY)
            ),
            'attribute_columns' => array (
                array('int', 1024),
                array('double', 3.1415926),
                array('neg_double', -3.1415926),
                array('empty', ''),
                array('string', '??????????????????'),
                array('binary', '\x00\x03\x05\x07', PrimaryKeyTypeConst::CONST_BINARY),
                array('bool', true)
            )
        );
        $this->otsClient->putRow ( $tablename );
        $body = array (
            'table_name' => self::$usedTables[2],
            'primary_key' => array (
                array('PK1', 40),
                array('PK2','a40'),
                array('PK3','\x01\x02\x03','BINARY')
            ),
            'max_versions' => 1,
            'columns_to_get' => array ()
        );
        $getrow = $this->otsClient->getRow ( $body );

        // notice here, sorted by OTS return.
        $expectColumns = array(
            array('binary', '\x00\x03\x05\x07', 'BINARY'),
            array('bool', true),
            array('double', 3.1415926),
            array('empty', '', 'STRING'),
            array('int', 1024),
            array('neg_double', -3.1415926),
            array('string', '??????????????????')
        );

        $this->assertColumnEquals($expectColumns, $getrow['attribute_columns']);
    }
    /*
     * BooleanValueTrue
     * ???????????????????????????True????????????
     * BooleanValueFalse
     * ???????????????????????????False?????????
     */
    public function testBooleanValue() {
        $tablename = array (
            'table_name' => self::$usedTables[0],
            'condition' => RowExistenceExpectationConst::CONST_IGNORE,
            'primary_key' => array (
                array('PK1', 50),
                array('PK2', 'a50')
            ),
            'attribute_columns' => array (
                array('attr1', true),
                array('attr2', false)
            )
        );
        $this->otsClient->putRow ( $tablename );
        $body = array (
            'table_name' => self::$usedTables[0],
            'primary_key' => array (
                array('PK1', 50),
                array('PK2', 'a50')
            ),
            'max_versions' => 1,
            'columns_to_get' => array ()
        );
        $getrow = $this->otsClient->getRow ( $body );
        $this->assertColumnEquals($tablename['attribute_columns'], $getrow['attribute_columns']);
    }

    /*
     * putWithTimestamp
     * ?????????????????????column???timestamp????????????
     */
    public function testPutWithTimestamp() {
        $timestamp = getMicroTime();
        $tablename = array (
            'table_name' => self::$usedTables[0],
            'condition' => RowExistenceExpectationConst::CONST_IGNORE,
            'primary_key' => array (
                array('PK1', 31415),
                array('PK2', 'a31415')
            ),
            'attribute_columns' => array (
                array('attr1', 1234),
                array('attr2', 3.1415),
                array('attr3', 'with timestamp', null, $timestamp)
            )
        );
        $this->otsClient->putRow ( $tablename );
        $body = array (
            'table_name' => self::$usedTables[0],
            'primary_key' => array (
                array('PK1', 31415),
                array('PK2', 'a31415')
            ),
            'max_versions' => 1,
            'columns_to_get' => array ()
        );
        $getrow = $this->otsClient->getRow ( $body );
        $this->assertColumnEquals($tablename['attribute_columns'], $getrow['attribute_columns']);
    }
    
    /*
     * ExpectNotExistConditionWhenRowNotExist
     * ??????????????????????????????????????????????????????Condition???EXPECT_NOT_EXIST???
     * ExpectNotExistConditionWhenRowExist
     * ??????????????????????????????????????????Condition???EXPECT_NOT_EXIST,??????????????????Condition check failed.
     */
    public function testExpectNotExistConditionWhenRowNotExist() {
        $request = array (
            'table_name' => self::$usedTables[0],
            'condition' => RowExistenceExpectationConst::CONST_IGNORE,
            'primary_key' => array (
                array('PK1', 50),
                array('PK2', 'a50')
            ) 
        );
        $this->otsClient->deleteRow ( $request );
        
        $tablename = array (
            'table_name' => self::$usedTables[0],
            'condition' => RowExistenceExpectationConst::CONST_EXPECT_NOT_EXIST,
            'primary_key' => array (
                array('PK1', 50),
                array('PK2', 'a50')
            ),
            'attribute_columns' => array (
                array('attr1', true),
                array('attr2', false)
            ) 
        );
        $this->otsClient->putRow ( $tablename );
        $body = array (
            'table_name' => self::$usedTables[0],
            'primary_key' => array (
                array('PK1', 50),
                array('PK2', 'a50')
            ),
            'max_versions' => 1,
            'columns_to_get' => array () 
        );
        $getrow = $this->otsClient->getRow ( $body );
        $this->assertColumnEquals($tablename['attribute_columns'], $getrow['attribute_columns']);
        
        $tablename1 = array (
            'table_name' => self::$usedTables[0],
            'condition' => RowExistenceExpectationConst::CONST_EXPECT_NOT_EXIST,
            'primary_key' => array (
                array('PK1', 50),
                array('PK2', 'a50')
            ),
            'attribute_columns' => array (
                array('attr1', true),
                array('attr2', false)
            ) 
        );
        
        try {
            $this->otsClient->putRow ( $tablename1 );
            $this->fail ( 'An expected exception has not been raised.' );
        } catch ( \Aliyun\OTS\OTSServerException $exc ) {
            $a = $exc->getMessage ();
            $c = 'Condition check failed.';
            $this->assertContains ( $c, $a );
        }
    }
    
    /**
     * ???????????????ColumnCondition??????????????????????????????????????????????????????
     */
    public function testPutRowWithColumnCondition() {
        $delete_query = array (
            'table_name' => self::$usedTables[0],
            'condition' => RowExistenceExpectationConst::CONST_IGNORE,
            'primary_key' => array (
                array('PK1', 50),
                array('PK2', 'a50')
            ) 
        );
        $this->otsClient->deleteRow ( $delete_query );
        
        $put_query1 = array (
            'table_name' => self::$usedTables[0],
            'condition' => RowExistenceExpectationConst::CONST_EXPECT_NOT_EXIST,
            'primary_key' => array (
                array('PK1', 50),
                array('PK2', 'a50')
            ),
            'attribute_columns' => array (
                array('attr1', true),
                array('attr2', true)
            ) 
        );
        $this->otsClient->putRow ( $put_query1 );
        
        $put_query2 = array (
            'table_name' => self::$usedTables[0],
            'condition' => array (
                'row_existence' => RowExistenceExpectationConst::CONST_EXPECT_EXIST,
                'column_condition' => array (
                    'column_name' => 'attr1',
                    'value' => true,
                    'comparator' => ComparatorTypeConst::CONST_EQUAL 
                ) 
            ),
            'primary_key' => array (
                array('PK1', 50),
                array('PK2', 'a50')
            ),
            'attribute_columns' => array (
                array('attr3', false),
                array('attr4', false)
            ) 
        );
        $this->otsClient->putRow ( $put_query2 );
        
        $get_query = array (
            'table_name' => self::$usedTables[0],
            'primary_key' => array (
                array('PK1', 50),
                array('PK2', 'a50')
            ),
            'max_versions' => 1,
            'columns_to_get' => array (
                'attr1',
                'attr2',
                'attr3',
                'attr4' 
            ) 
        );
        $get_row_res = $this->otsClient->getRow ( $get_query );
        $this->assertColumnEquals($put_query2['attribute_columns'], $get_row_res['attribute_columns']);
        
        $put_query3 = array (
            'table_name' => self::$usedTables[0],
            'condition' => array (
                'row_existence' => RowExistenceExpectationConst::CONST_EXPECT_EXIST,
                'column_condition' => array (
                    'column_name' => 'attr3',
                    'value' => true,
                    'comparator' => ComparatorTypeConst::CONST_EQUAL 
                ) 
            ),
            'primary_key' => array (
                array('PK1', 50),
                array('PK2', 'a50')
            ),
            'attribute_columns' => array (
                array('attr5', false),
                array('attr6', false)
            ) 
        );
        try {
            $this->otsClient->putRow ( $put_query3 );
            $this->fail ( 'An expected exception has not been raised.' );
        } catch ( \Aliyun\OTS\OTSServerException $exc ) {
            $a = $exc->getMessage ();
            $c = 'Condition check failed.';
            $this->assertContains ( $c, $a );
        }
    }
}

