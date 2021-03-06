<?php

namespace Aliyun\OTS\Tests;

use Aliyun\OTS\Consts\ComparatorTypeConst;
use Aliyun\OTS\Consts\LogicalOperatorConst;
use Aliyun\OTS\Consts\PrimaryKeyTypeConst;
use Aliyun\OTS\Consts\RowExistenceExpectationConst;

require_once __DIR__ . '/TestBase.php';
require_once __DIR__ . '/../../vendor/autoload.php';


class GetRowTest extends SDKTestBase {

    private static $usedTables = array (
        'myTable'
    );

    public static function setUpBeforeClass()
    {
        SDKTestBase::cleanUp (self::$usedTables);
        SDKTestBase::createInitialTable (array (
            'table_meta' => array (
                'table_name' => self::$usedTables[0],
                'primary_key_schema' => array (
                    array('PK1', PrimaryKeyTypeConst::CONST_STRING),
                    array('PK2', PrimaryKeyTypeConst::CONST_INTEGER),
                    array('PK3', PrimaryKeyTypeConst::CONST_STRING),
                    array('PK4', PrimaryKeyTypeConst::CONST_INTEGER)
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
        ));
        SDKTestBase::waitForTableReady ();

        SDKTestBase::putInitialData (array (
            'table_name' => self::$usedTables[0],
            'condition' => RowExistenceExpectationConst::CONST_IGNORE,
            'primary_key' => array (
                array('PK1', 'a1'),
                array('PK2', 1),
                array('PK3','a11'),
                array('PK4', 11)
            ),
            'attribute_columns' => array (
                array('attr1', 1),
                array('attr2', 'aa'),
                array('attr3', 'tas'),
                array('attr4', 11)
            )
        ));
    }

    public static function tearDownAfterClass()
    {
        SDKTestBase::cleanUp (self::$usedTables);
    }
    /*
     *
     * GetRowWithDefaultColumnsToGet
     * ???PutRow??????4???????????????4?????????????????????GetRow??????ColumnsToGet?????????4?????????????????????????????????4???????????????
     */
    public function testGetRowWith4AttributeColumnsToGet() {
        $body = array (
            'table_name' => self::$usedTables[0],
            'primary_key' => array (
                array('PK1', 'a1'),
                array('PK2', 1),
                array('PK3','a11'),
                array('PK4', 11)
            ),
            'max_versions' => 1,
            'columns_to_get' => array (
                'attr1',
                'attr2',
                'attr3',
                'attr4'
            )
        );
        $tablename = array (
            'table_name' => self::$usedTables[0],
            'condition' => RowExistenceExpectationConst::CONST_IGNORE,
            'primary_key' => array (
                array('PK1', 'a1'),
                array('PK2', 1),
                array('PK3','a11'),
                array('PK4', 11)
            ),
            'attribute_columns' => array (
                array('attr1', 1),
                array('attr2', 'aa'),
                array('attr3', 'tas'),
                array('attr4', 11)
            )
        );
        $expectColumn = array(
            array('attr1', 1),
            array('attr2', 'aa'),
            array('attr3', 'tas'),
            array('attr4', 11)
        );

        $this->otsClient->putRow ($tablename);
        $getrow = $this->otsClient->getRow ($body);
        // NOTE:primary_key????????????????????????????????????
//        $this->assertEmpty ($getrow['row']['primary_key']);
        $this->assertColumnEquals($expectColumn, $getrow['attribute_columns']);
    }
    
    /*
     *
     * GetRowWithDefaultColumnsToGet
     * ???PutRow??????4???????????????4?????????????????????GetRow???????????????ColumnsToGet?????????????????????4???????????????4???????????????
     */
    public function testGetRowWithDefaultColumnsToGet() {
        $body = array (
            'table_name' => self::$usedTables[0],
            'primary_key' => array (
                array('PK1', 'a1'),
                array('PK2', 1),
                array('PK3','a11'),
                array('PK4', 11)
            ),
            'max_versions' => 1
        );
        $tablename = array (
            'table_name' => self::$usedTables[0],
            'condition' => RowExistenceExpectationConst::CONST_IGNORE,
            'primary_key' => array (
                array('PK1', 'a1'),
                array('PK2', 1),
                array('PK3','a11'),
                array('PK4', 11)
            ),
            'attribute_columns' => array (
                array('attr1', 1),
                array('attr2', 'aa'),
                array('attr3', 'tas'),
                array('attr4', 11)
            )
        );
        $this->otsClient->putRow ($tablename);
        $getrow = $this->otsClient->getRow ($body);
        $exceptColumn = array(
            array('attr1', 1),
            array('attr2', 'aa'),
            array('attr3', 'tas'),
            array('attr4', 11)
        );
        $this->assertEquals ($getrow['primary_key'], $tablename['primary_key']);
        $this->assertColumnEquals($exceptColumn, $getrow['attribute_columns']);
    }
    
    /*
     * GetRowWith0ColumsToGet
     * ???PutRow??????4???????????????4?????????????????????GetRow??????ColumnsToGet??????????????????????????????????????????
     */
    public function testGetRowWith0ColumsToGet() {
        $body = array (
            'table_name' => self::$usedTables[0],
            'primary_key' => array (
                array('PK1', 'a1'),
                array('PK2', 1),
                array('PK3','a11'),
                array('PK4', 11)
            ),
            'max_versions' => 1,
            'columns_to_get' => array ()
        );
        $tablename = array (
            'table_name' => self::$usedTables[0],
            'condition' => RowExistenceExpectationConst::CONST_IGNORE,
            'primary_key' => array (
                array('PK1', 'a1'),
                array('PK2', 1),
                array('PK3','a11'),
                array('PK4', 11)
            ),
            'attribute_columns' => array (
                array('attr1', 1),
                array('attr2', 'aa'),
                array('attr3', 'tas'),
                array('attr4', 11)
            )
        );
        $this->otsClient->putRow ($tablename);
        $getrow = $this->otsClient->getRow ($body);
        $exceptColumn = array(
            array('attr1', 1),
            array('attr2', 'aa'),
            array('attr3', 'tas'),
            array('attr4', 11)
        );

        $this->assertEquals ($getrow['primary_key'], $tablename['primary_key']);
        $this->assertColumnEquals($exceptColumn, $getrow['attribute_columns']);
    }
    
    /*
     * GetRowWith4ColumnsToGet
     * ???PutRow??????4???????????????4?????????????????????GetRow??????ColumnsToGet????????????2???????????????2???????????????????????????????????????????????????????????????
     */
    public function testGetRowWith4ColumnsToGet() {
        $body = array (
            'table_name' => self::$usedTables[0],
            'primary_key' => array (
                array('PK1', 'a1'),
                array('PK2', 1),
                array('PK3','a11'),
                array('PK4', 11)
            ),
            'max_versions' => 1,
            'columns_to_get' => array (
                'PK1',
                'PK2',
                'attr1',
                'attr2'
            )
        );
        $tablename = array (
            'table_name' => self::$usedTables[0],
            'condition' => RowExistenceExpectationConst::CONST_IGNORE,
            'primary_key' => array (
                array('PK1', 'a1'),
                array('PK2', 1),
                array('PK3','a11'),
                array('PK4', 11)
            ),
            'attribute_columns' => array (
                array('attr1', 1),
                array('attr2', 'aa'),
                array('attr3', 'tas'),
                array('attr4', 11)
            )
        );
        $this->otsClient->putRow ($tablename);
        $getrow = $this->otsClient->getRow ($body);
        $exceptColumn = array(
            array('attr1', 1),
            array('attr2', 'aa')
        );

        $this->assertEquals ($getrow['primary_key'], $tablename['primary_key']);
        $this->assertColumnEquals($exceptColumn, $getrow['attribute_columns']);
    }
    
    /*
     * GetRowWith1000ColumnsToGet
     * GetRow??????ColumnsToGet??????1025???????????????????????????????????????????????????, ??????1024???column
     */
    public function testGetRowWith1025ColumnsToGet() {
        for($a = 0; $a < 1025; $a ++) {
            $b[] = 'a' . $a;
        }
        // echo $b;
        $body = array (
            'table_name' => self::$usedTables[0],
            'primary_key' => array (
                array('PK1', 'a1'),
                array('PK2', 1),
                array('PK3','a11'),
                array('PK4', 11)
            ),
            'columns_to_get' => $b
        );

        try {
            $this->otsClient->getRow ($body);
            $this->fail ( 'An expected exception has not been raised.' );
        } catch ( \Aliyun\OTS\OTSServerException $exc ) {
            $c = 'The number of columns from the request exceeds the limit';
            $this->assertContains ( $c, $exc->getOTSErrorMessage () );
        }
    }
    
    /*
     * GetRowWithDuplicateColumnsToGet
     * GetRow??????ColumnsToGet??????2??????????????????,???????????????????????????
     */
    public function testGetRowWithDuplicateColumnsToGet() {
        $body = array (
            'table_name' => self::$usedTables[0],
            'primary_key' => array (
                array('PK1', 'a1'),
                array('PK2', 1),
                array('PK3','a11'),
                array('PK4', 11)
            ),
            'max_versions' => 1,
            'columns_to_get' => array (
                'PK1',
                'PK1'
            )
        );
        $tablename = array (
            'table_name' => self::$usedTables[0],
            'condition' => RowExistenceExpectationConst::CONST_IGNORE,
            'primary_key' => array (
                array('PK1', 'a1'),
                array('PK2', 1),
                array('PK3','a11'),
                array('PK4', 11)
            ),
            'attribute_columns' => array (
                array('attr1', 1),
                array('attr2', 'aa'),
                array('attr3', 'tas'),
                array('attr4', 11)
            )
        );
        $this->otsClient->putRow ($tablename);
        $getrow = $this->otsClient->getRow ($body);
        // if (is_array($getrow)) {
        // print_r($getrow);die;
        $this->assertEquals ($getrow['primary_key'], $body['primary_key']);
        // }
    }
    
    /**
     * ???????????????ColumnCondition?????????????????????????????????????????????????????????????????????
     */
    public function testGetRowWithColumnFilterToGet() {
        $putdata1 = array (
            'table_name' => self::$usedTables[0],
            'condition' => RowExistenceExpectationConst::CONST_IGNORE,
            'primary_key' => array (
                array('PK1', 'a1'),
                array('PK2', 1),
                array('PK3','a11'),
                array('PK4', 11)
            ),
            'attribute_columns' => array (
                array('attr1', 1),
                array('attr2', 'aa'),
                array('attr3', 'tas'),
                array('attr4', 11)
            )
        );
        $putdata2 = array (
            'table_name' => self::$usedTables[0],
            'condition' => RowExistenceExpectationConst::CONST_IGNORE,
            'primary_key' => array (
                array('PK1', 'a2'),
                array('PK2', 2),
                array('PK3','a22'),
                array('PK4', 22)
            ),
            'attribute_columns' => array (
                array('attr1', 2),
                array('attr2', 'aaa'),
                array('attr3', 'tass'),
                array('attr4', 22)
            )
        );
        $this->otsClient->putRow ($putdata1);
        $this->otsClient->putRow ($putdata2);
        $querybody = array (
            'table_name' => self::$usedTables[0],
            'primary_key' => array (
                array('PK1', 'a2'),
                array('PK2', 2),
                array('PK3','a22'),
                array('PK4', 22)
            ),
            'columns_to_get' => array (
                'PK1',
                'PK2',
                'PK3',
                'PK4'
            ),
            'max_versions' => 1,
            'column_filter' => array (
                'logical_operator' => LogicalOperatorConst::CONST_AND,
                'sub_filters' => array (
                    array (
                        'column_name' => 'attr1',
                        'value' => 1,
                        'comparator' => ComparatorTypeConst::CONST_GREATER_THAN
                    ),
                    array (
                        'column_name' => 'attr4',
                        'value' => 30,
                        'comparator' => ComparatorTypeConst::CONST_LESS_THAN
                    )
                )
            )
        );
        $getrowres = $this->otsClient->getRow ($querybody);
        $this->assertEquals ($getrowres['primary_key'], $putdata2['primary_key']);
    }
    
    /**
     * ???????????????ColumnCondition?????????????????????????????????????????????????????????????????????
     */
    public function testGetRowWithColumnFilterToGet2() {
        $putdata1 = array (
            'table_name' => self::$usedTables[0],
            'condition' => RowExistenceExpectationConst::CONST_IGNORE,
            'primary_key' => array (
                array('PK1', 'a1'),
                array('PK2', 1),
                array('PK3','a11'),
                array('PK4', 11)
            ),
            'attribute_columns' => array (
                array('attr1', 1),
                array('attr2', 'aa'),
                array('attr3', 'tas'),
                array('attr4', 11)
            )
        );
        $putdata2 = array (
            'table_name' => self::$usedTables[0],
            'condition' => RowExistenceExpectationConst::CONST_IGNORE,
            'primary_key' => array (
                array('PK1', 'a2'),
                array('PK2', 2),
                array('PK3','a22'),
                array('PK4', 22)
            ),
            'attribute_columns' => array (
                array('attr1', 2),
                array('attr2', 'aaa'),
                array('attr3', 'tass'),
                array('attr4', 22)
            )
        );
        $this->otsClient->putRow ($putdata1);
        $this->otsClient->putRow ($putdata2);
        $querybody = array (
            'table_name' => self::$usedTables[0],
            'primary_key' => array (
                array('PK1', 'a2'),
                array('PK2', 2),
                array('PK3','a22'),
                array('PK4', 22)
            ),
            'columns_to_get' => array (
                'attr1',
                'attr2',
                'attr3',
                'attr4'
            ),
            'max_versions' => 1,
            'column_filter' => array (
                'logical_operator' => LogicalOperatorConst::CONST_NOT,
                'sub_filters' => array (
                    array (
                        'column_name' => 'attr4',
                        'value' => 22,
                        'comparator' => ComparatorTypeConst::CONST_NOT_EQUAL
                    )
                )
            )
        );
        $getrowres = $this->otsClient->getRow ($querybody);

        $exceptColumn = array(
            array('attr1', 2),
            array('attr2', 'aaa'),
            array('attr3', 'tass'),
            array('attr4', 22)
        );

        $this->assertColumnEquals($exceptColumn, $getrowres['attribute_columns']);
    }
    
    /**
     * ???????????????ColumnCondition???????????????????????????????????????????????????????????????????????????????????????????????????
     */
    public function testGetRowWithColumnFilterAndMissingField() {
        $putdata1 = array (
            'table_name' => self::$usedTables[0],
            'condition' => RowExistenceExpectationConst::CONST_IGNORE,
            'primary_key' => array (
                array('PK1', 'a1'),
                array('PK2', 1),
                array('PK3','a11'),
                array('PK4', 11)
            ),
            'attribute_columns' => array (
                array('attr1', 1),
                array('attr2', 'aa'),
                array('attr3', 'tas'),
                array('attr4', 11)
            )
        );
        $putdata2 = array (
            'table_name' => self::$usedTables[0],
            'condition' => RowExistenceExpectationConst::CONST_IGNORE,
            'primary_key' => array (
                array('PK1', 'a2'),
                array('PK2', 2),
                array('PK3','a22'),
                array('PK4', 22)
            ),
            'attribute_columns' => array (
                array('attr1', 2),
                array('attr2', 'aaa'),
                array('attr3', 'tass'),
                array('attr4', 22)
            )
        );
        $this->otsClient->putRow ($putdata1);
        $this->otsClient->putRow ($putdata2);
        $querybody = array (
            'table_name' => self::$usedTables[0],
            'primary_key' => array (
                array('PK1', 'a2'),
                array('PK2', 2),
                array('PK3','a22'),
                array('PK4', 22)
            ),
            'columns_to_get' => array (
                'PK1',
                'PK2',
                'PK3',
                'PK4'
            ),
            'max_versions' => 1,
            'column_filter' => array (
                'logical_operator' => LogicalOperatorConst::CONST_AND,
                'sub_filters' => array (
                    array (
                        'column_name' => 'attr55',
                        'value' => 1,
                        'comparator' => ComparatorTypeConst::CONST_GREATER_THAN,
                        'pass_if_missing' => false
                    ),
                    array (
                        'column_name' => 'attr4',
                        'value' => 30,
                        'comparator' => ComparatorTypeConst::CONST_LESS_THAN
                    )
                )
            )
        );
        $getrowres = $this->otsClient->getRow ($querybody);
        $this->assertEquals (count ($getrowres['primary_key']), 0);
        $this->assertEquals (count ($getrowres['attribute_columns']), 0);
    }
    
    /**
     * ???????????????ColumnCondition?????????????????????????????????????????????????????????????????????????????????????????????
     */
    public function testGetRowWithColumnFilterAndMultipleLogicalOperatorsToGet() {
        $putdata1 = array (
            'table_name' => self::$usedTables[0],
            'condition' => RowExistenceExpectationConst::CONST_IGNORE,
            'primary_key' => array (
                array('PK1', 'a1'),
                array('PK2', 1),
                array('PK3','a11'),
                array('PK4', 11)
            ),
            'attribute_columns' => array (
                array('attr1', 1),
                array('attr2', 'aa'),
                array('attr3', 'tas'),
                array('attr4', 11)
            )
        );
        $putdata2 = array (
            'table_name' => self::$usedTables[0],
            'condition' => RowExistenceExpectationConst::CONST_IGNORE,
            'primary_key' => array (
                array('PK1', 'a2'),
                array('PK2', 2),
                array('PK3','a22'),
                array('PK4', 22)
            ),
            'attribute_columns' => array (
                array('attr1', 2),
                array('attr2', 'aaa'),
                array('attr3', 'tass'),
                array('attr4', 22)
            )
        );
        $this->otsClient->putRow ($putdata1);
        $this->otsClient->putRow ($putdata2);
        $querybody = array (
            'table_name' => self::$usedTables[0],
            'primary_key' => array (
                array('PK1', 'a2'),
                array('PK2', 2),
                array('PK3','a22'),
                array('PK4', 22)
            ),
            'columns_to_get' => array (
                'PK1',
                'PK2',
                'PK3',
                'PK4'
            ),
            'max_versions' => 1,
            'column_filter' => array (
                'logical_operator' => LogicalOperatorConst::CONST_AND,
                'sub_filters' => array (
                    array (
                        'column_name' => 'attr1',
                        'value' => 1,
                        'comparator' => ComparatorTypeConst::CONST_GREATER_THAN
                    ),
                    array (
                        'column_name' => 'attr4',
                        'value' => 30,
                        'comparator' => ComparatorTypeConst::CONST_LESS_THAN
                    ),
                    array (
                        'logical_operator' => LogicalOperatorConst::CONST_OR,
                        'sub_filters' => array (
                            array (
                                'column_name' => 'attr2',
                                'value' => 'aaaaa',
                                'comparator' => ComparatorTypeConst::CONST_EQUAL
                            ),
                            array (
                                'column_name' => 'attr3',
                                'value' => 'tass',
                                'comparator' => ComparatorTypeConst::CONST_EQUAL
                            )
                        )
                    )
                )
            )
        );
        $getrowres = $this->otsClient->getRow ($querybody);
        $this->assertEquals ($getrowres['primary_key'], $putdata2['primary_key']);
    }
}

