<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: table_store.proto

namespace Aliyun\OTS\ProtoBuffer\Protocol;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>aliyun.OTS.ProtoBuffer.Protocol.PartitionRange</code>
 */
class PartitionRange extends \Aliyun\OTS\ProtoBuffer\Protocol\Message
{
    /**
     * encoded as SQLVariant
     *
     * Generated from protobuf field <code>required bytes begin = 1;</code>
     */
    private $begin = '';
    private $has_begin = false;
    /**
     * encoded as SQLVariant
     *
     * Generated from protobuf field <code>required bytes end = 2;</code>
     */
    private $end = '';
    private $has_end = false;

    public function __construct() {
        \GPBMetadata\TableStore::initOnce();
        parent::__construct();
    }

    /**
     * encoded as SQLVariant
     *
     * Generated from protobuf field <code>required bytes begin = 1;</code>
     * @return string
     */
    public function getBegin()
    {
        return $this->begin;
    }

    /**
     * encoded as SQLVariant
     *
     * Generated from protobuf field <code>required bytes begin = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setBegin($var)
    {
        GPBUtil::checkString($var, False);
        $this->begin = $var;
        $this->has_begin = true;

        return $this;
    }

    public function hasBegin()
    {
        return $this->has_begin;
    }

    /**
     * encoded as SQLVariant
     *
     * Generated from protobuf field <code>required bytes end = 2;</code>
     * @return string
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * encoded as SQLVariant
     *
     * Generated from protobuf field <code>required bytes end = 2;</code>
     * @param string $var
     * @return $this
     */
    public function setEnd($var)
    {
        GPBUtil::checkString($var, False);
        $this->end = $var;
        $this->has_end = true;

        return $this;
    }

    public function hasEnd()
    {
        return $this->has_end;
    }

}

