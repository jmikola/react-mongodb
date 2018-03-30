<?php

namespace Jmikola\React\MongoDB\Protocol;

interface MessageInterface
{
    const MSG_HEADER_SIZE = 16;

    const ZERO = 0;

    const OP_REPLY = 1;
    const OP_MSG = 1000;
    const OP_UPDATE = 2001;
    const OP_INSERT = 2002;
    const OP_QUERY = 2004;
    const OP_GET_MORE = 2005;
    const OP_DELETE = 2006;
    const OP_KILL_CURSORS = 2007;

    const QF_TAILABLE_CURSOR = 2;
    const QF_SLAVE_OK = 4;
    const QF_OPLOG_REPLAY = 8;
    const QF_NO_CURSOR_TIMEOUT = 16;
    const QF_AWAIT_DATA = 32;
    const QF_EXHAUST = 64;
    const QF_PARTIAL = 128;

    const RF_CURSOR_NOT_FOUND = 1;
    const RF_QUERY_FAILURE = 2;
    const RF_SHARD_CONFIG_STALE = 4;
    const RF_AWAIT_CAPABLE = 8;

    public function getOpCode();
}
