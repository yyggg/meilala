<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | PHP version 5                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2004 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 3.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available through the world-wide-web at the following url:           |
// | http://www.php.net/license/3_0.txt.                                  |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Original Author <author@example.com>                        |
// |          Your Name <you@example.com>                                 |
// +----------------------------------------------------------------------+
//
// $Id:$

if (!defined('ROOT')) die('Access denied.');
class Websocket {
    var $host = '127.0.0.1';
    var $port = 10088;
    var $path = '/';
    var $domain = '';
    var $socket = null;
    var $accept = array();
    var $cycle = array();
    var $type = array();
    var $class = array();
    var $admin = array();
    var $guest = array();
    function __construct($host, $port, $path = '') {
        $this->host = $host;
        $this->port = $port;
        if ($path) $this->path = $path;
        $this->class[1] = new class_websocket_1;
        $this->class[2] = new class_websocket_2;
    }
    function run() {
        if (!$this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) return false;
        socket_set_option($this->socket, SOL_SOCKET, SO_REUSEADDR, true);
        socket_set_option($this->socket, SOL_SOCKET, SO_RCVBUF, WEBSOCKET_MAX);
        socket_set_option($this->socket, SOL_SOCKET, SO_SNDBUF, WEBSOCKET_MAX);
        if (!socket_bind($this->socket, $this->host, $this->port)) return false;
        if (!socket_listen($this->socket, WEBSOCKET_MAX)) return false;
        while (true) {
            $this->cycle = $this->accept;
            $this->cycle[] = $this->socket;
            socket_select($this->cycle, $write = null, $except = null, null);
            foreach ($this->cycle as $v) {
                if ($v === $this->socket) {
                    if (!$accept = socket_accept($v)) continue;
                    $this->accept[] = $accept;
                    $index = array_keys($this->accept);
                    $index = end($index);
                    $this->type[$index] = false;
                    continue;
                }
                if (($index = $this->search($v)) === false) continue;
                if (!socket_recv($v, $data, WEBSOCKET_MAX, 0) || !$data) {
                    $this->close($v, $index);
                    continue;
                }
                $type = $this->type[$index];
                if ($type === false) {
                    $type = $this->header($data, $v, $index);
                    if ($type === false) {
                        $this->close($v, $index, 0);
                        continue;
                    }
                    $this->type[$index] = $type;
                    continue;
                }
                if (!$data = $this->class[$type]->decode($data)) {
                    $this->close($v, $index);
                    continue;
                }
                $this->welive_call($data, $v, $index);
            }
        }
        return true;
    }
    function welive_call($data, $accept, $index) {
        $data = string_to_array($data);
        switch ($data['x']) {
            case 4:
                if (!$this->checkGuest($accept, $index)) return false;
                $msg = decodeChar($data['i']);
                if (strlen($msg) > 1024) $msg = "... too long ...";
                $aix = $this->guest[$index]['aix'];
                $this->send(array(
                    'x' => 4,
                    'g' => $this->guest[$index]['gid'],
                    'i' => $msg
                ) , $this->accept[$aix], $aix);
                break;

            case 1:
                if (!$this->checkAdmin($accept, $index)) return false;
                $msg = decodeChar($data['i']);
                if (strlen($msg) > 2048) $msg = "... too long ...";
                if ($this->admin[$index]['type']) {
                    $spec = 0;
                    switch ($msg) {
                        case 'system die':
                            die();
                            break;

                        case 'all':
                            $spec = 1;
                            $msg = 'Total connections = ' . count($this->accept) . '<br>Total admins = ' . count($this->admin) . '<br>Total guests = ' . count($this->guest);
                            break;

                        case 'admin':
                            $spec = 1;
                            $msg = 'Total admins = ' . count($this->admin);
                            foreach ($this->admin AS $a) {
                                $msg.= "<br>$a[fullname] = $a[guests]";
                            }
                            break;

                        case 'guest':
                            $spec = 1;
                            $msg = 'Total guests = ' . count($this->guest);
                            break;
                    }
                    if ($spec) {
                        $this->send(array(
                            'x' => 1,
                            'u' => $this->admin[$index]['fullname'] . ' (' . $this->admin[$index]['post'] . ')',
                            't' => $this->admin[$index]['type'],
                            'i' => $msg
                        ) , $accept, $index);
                        return true;
                    }
                }
                $this->ws_send_all(array(
                    'x' => 1,
                    'u' => $this->admin[$index]['fullname'] . ' (' . $this->admin[$index]['post'] . ')',
                    't' => $this->admin[$index]['type'],
                    'i' => $msg
                ) , $index);
                break;

            case 2:
                if ($data['a'] != 8 AND !$this->checkAdmin($accept, $index)) return false;
                switch ($data['a']) {
                    case 3:
                        if (isset($this->admin[$index])) $this->admin[$index]['busy'] = 1;
                        $this->ws_send_all(array(
                            'x' => 2,
                            'a' => 3,
                            'ix' => $index
                        ));
                        break;

                    case 4:
                        if (isset($this->admin[$index])) $this->admin[$index]['busy'] = 0;
                        $this->ws_send_all(array(
                            'x' => 2,
                            'a' => 4,
                            'ix' => $index
                        ));
                        break;

                    case 5:
                        $gid = ForceInt($data['g']);
                        if ($gid) {
                            $guest = APP::$DB->getOne("SELECT ipzone, fromurl, grade, fullname, address, phone, email, remark FROM " . TABLE_PREFIX . "guest WHERE gid = '$gid'");
                            if (!empty($guest)) {
                                $this->send(array(
                                    'x' => 2,
                                    'a' => 5,
                                    'g' => $gid,
                                    'd' => $guest
                                ) , $accept, $index);
                            }
                        }
                        break;

                    case 6:
                        $gid = ForceInt($data['g']);
                        if ($gid) {
                            $grade = ForceInt($data['grade']);
                            $fullname = ForceData($data['fullname']);
                            $address = ForceData($data['address']);
                            $phone = ForceData($data['phone']);
                            $email = ForceData($data['email']);
                            $remark = ForceData($data['remark']);
                            APP::$DB->exe("UPDATE " . TABLE_PREFIX . "guest SET grade = '$grade', fullname = '$fullname', address = '$address', phone = '$phone', email = '$email', remark = '$remark' WHERE gid = '$gid'");
                            $this->send(array(
                                'x' => 2,
                                'a' => 6,
                                'g' => $gid,
                                'n' => $fullname
                            ) , $accept, $index);
                        }
                        break;

                    case 8:
                        $aid = ForceInt($data['id']);
                        $sid = $data['s'];
                        $agent = $data['ag'];
                        if (!$aid OR !IsAlnum($sid) OR !IsAlnum($agent)) {
                            $this->close($accept, $index, 0);
                            return false;
                        }
                        $sql = "SELECT a.aid, a.type, a.username, a.fullname, a.fullname_en, a.post, a.post_en, a.lastip AS ip FROM " . TABLE_PREFIX . "session s LEFT JOIN " . TABLE_PREFIX . "admin a ON a.aid = s.aid WHERE s.sid    = '$sid' AND s.aid = '$aid' AND s.agent = '$agent' AND a.activated = 1";
                        $admin = APP::$DB->getOne($sql);
                        if (!$admin OR !$admin['aid']) {
                            $this->close($accept, $index, 0);
                            return false;
                        }
                        $avatar = GetAvatar($admin['aid'], 1);
                        $this->ws_send_all(array(
                            'x' => 2,
                            'a' => 1,
                            'ix' => $index,
                            'id' => $admin['aid'],
                            't' => $admin['type'],
                            'n' => $admin['fullname'],
                            'p' => $admin['post'],
                            'av' => $avatar
                        ));
                        $this->admin[$index] = $admin;
                        $this->admin[$index]['busy'] = 0;
                        $this->admin[$index]['avatar'] = $avatar;
                        $guest_list = array();
                        foreach ($this->guest AS $k => $g) {
                            if ($g['aid'] == $aid) {
                                $this->guest[$k]['aix'] = $index;
                                $this->send(array(
                                    'x' => 6,
                                    'a' => 1
                                ) , $this->accept[$k], $k);
                                $guest_list[] = array(
                                    'g' => $g['gid'],
                                    'n' => $g['n'],
                                    'l' => $g['l']
                                );
                            }
                        }
                        $this->admin[$index]['guests'] = count($guest_list);
                        $admin_list = array();
                        foreach ($this->admin AS $k => $a) {
                            $admin_list[] = array(
                                'ix' => $k,
                                'id' => $a['aid'],
                                't' => $a['type'],
                                'n' => $a['fullname'],
                                'p' => $a['post'],
                                'av' => $a['avatar'],
                                'b' => $a['busy'],
                                'gs' => $a['guests']
                            );
                        }
                        $this->send(array(
                            'x' => 2,
                            'a' => 8,
                            'ix' => $index,
                            'al' => $admin_list,
                            'gl' => $guest_list
                        ) , $accept, $index);
                        break;

                    case 9:
                        if ($this->admin[$index]['type'] == 1) die();
                        break;
                    }
                    break;

                case 5:
                    $msg = decodeChar($data['i']);
                    if (strlen($msg) > 2048) $msg = "... too long ...";
                    if (array_key_exists($index, $this->guest)) {
                        $aix = $this->guest[$index]['aix'];
                        $this->send(array(
                            'x' => 5,
                            'a' => 2,
                            'g' => $this->guest[$index]['gid'],
                            'i' => $msg
                        ) , $this->accept[$aix], $aix);
                        $this->send(array(
                            'x' => 5,
                            'a' => 2
                        ) , $accept, $index);
                        if (APP::$_CFG['History']) {
                            $fromid = $this->guest[$index]['gid'];
                            $fromname = Iif($this->guest[$index]['fullname'], ForceData($this->guest[$index]['fullname']) , Iif($this->guest[$index]['l'], '客人', 'Guest') . $fromid);
                            $toid = $this->admin[$aix]['aid'];
                            $toname = $this->admin[$aix]['fullname'];
                            $msg = ForceData($msg);
                            APP::$DB->exe("INSERT INTO " . TABLE_PREFIX . "msg (type, fromid, fromname, toid, toname, msg, time)
VALUES (0, '$fromid', '$fromname', '$toid', '$toname', '$msg', '" . time() . "')");
                        }
                    } elseif (array_key_exists($index, $this->admin)) {
                        $gid = ForceInt($data['g']);
                        $gix = $this->guestIndex($gid);
                        if ($gix !== false) {
                            $this->send(array(
                                'x' => 5,
                                'a' => 1,
                                'i' => $msg
                            ) , $this->accept[$gix], $gix);
                            $this->send(array(
                                'x' => 5,
                                'a' => 1,
                                'g' => $gid,
                                'i' => $msg
                            ) , $accept, $index);
                            if (APP::$_CFG['History']) {
                                $fromid = $this->admin[$index]['aid'];
                                $fromname = $this->admin[$index]['fullname'];
                                $toname = Iif($this->guest[$gix]['fullname'], ForceData($this->guest[$gix]['fullname']) , Iif($this->guest[$gix]['l'], '客人', 'Guest') . $gid);
                                $msg = ForceData($msg);
                                APP::$DB->exe("INSERT INTO " . TABLE_PREFIX . "msg (type, fromid, fromname, toid, toname, msg, time)
VALUES (1, '$fromid', '$fromname', '$gid', '$toname', '$msg', '" . time() . "')");
                            }
                        }
                    } else {
                        $this->close($accept, $index, 0);
                    }
                    break;

                case 6:
                    switch ($data['a']) {
                        case 8:
                            $key = $data['k'];
                            $code = decodeChar($data['c']);
                            $decode = authcode($code, 'DECODE', $key);
                            if (0) {
                                $this->close($accept, $index, 0);
                                return false;
                            }
                            $gid = ForceInt($data['gid']);
                            $aid = ForceInt($data['aid']);
                            $fullname = decodeChar($data['fn']);
                            $first = Iif($aid, 0, 1);
                            $hasRecord = 0;
                            if ($gid AND $first) {
                                $guest = APP::$DB->getOne("SELECT aid, fullname FROM " . TABLE_PREFIX . "guest WHERE gid = '$gid'");
                                if ($guest AND $guest['aid']) {
                                    $aid = $guest['aid'];
                                    $fullname = $guest['fullname'];
                                    $hasRecord = 1;
                                }
                            }
                            $admin_index = $this->select_admin($aid);
                            if ($admin_index === false) {
                                $this->send(array(
                                    'x' => 6,
                                    'a' => 9
                                ) , $accept, $index);
                                $this->close($accept, $index, 0);
                                return false;
                            }
                            if (isset($this->admin[$admin_index])) $this->admin[$admin_index]['guests']+= 1;
                            $aid = $this->admin[$admin_index]['aid'];
                            $lang = ForceInt($data['l']);
                            $fromurl = ForceData($data['fr']);
                            $browser = ForceData($data['ag']);
                            $lastip = $this->ip($accept);
                            $ipzone = convertip($lastip);
                            $timenow = time();
                            if ($gid) $this->clearGuest($gid);
                            $recs = array();
                            if ($first AND $gid AND $hasRecord) {
                                APP::$DB->exe("UPDATE " . TABLE_PREFIX . "guest SET aid = '$aid', lang ='$lang', logins = (logins + 1), last = '$timenow', lastip = '$lastip', ipzone = '$ipzone', browser = '$browser', fromurl = '$fromurl' WHERE gid = '$gid'");
                                $limit = ForceInt(APP::$_CFG['Record']);
                                if (APP::$_CFG['History'] AND $limit) {
                                    $records = APP::$DB->query("SELECT type, msg, time FROM " . TABLE_PREFIX . "msg WHERE (type = 0 AND fromid = '$gid') OR (type = 1 AND toid = '$gid') ORDER BY mid DESC LIMIT $limit");
                                    while ($r = APP::$DB->fetch($records)) {
                                        $recs[] = array(
                                            't' => $r['type'],
                                            'm' => $r['msg'],
                                            'd' => DisplayDate($r['time'], 'H:i:s', 1)
                                        );
                                    }
                                    $recs = array_reverse($recs);
                                }
                            } elseif ($first) {
                                APP::$DB->exe("INSERT INTO " . TABLE_PREFIX . "guest (aid, lang, last, lastip, ipzone, browser, fromurl)
VALUES ('$aid', '$lang', '$timenow', '$lastip', '$ipzone', '$browser', '$fromurl')");
                                $gid = APP::$DB->insert_id;
                            }
                            $this->guest[$index] = array(
                                'gid' => $gid,
                                'aid' => $aid,
                                'aix' => $admin_index,
                                'n' => $fullname,
                                'l' => $lang
                            );
                            $this->send(array(
                                'x' => 6,
                                'a' => 8,
                                'g' => $gid,
                                'n' => $fullname,
                                'l' => $lang,
                                're' => $recs
                            ) , $this->accept[$admin_index], $admin_index);
                            if ($lang) {
                                $a_n = $this->admin[$admin_index]['fullname'];
                                $a_p = $this->admin[$admin_index]['post'];
                            } else {
                                $a_n = $this->admin[$admin_index]['fullname_en'];
                                $a_p = $this->admin[$admin_index]['post_en'];
                            }
                            $this->send(array(
                                'x' => 6,
                                'a' => 8,
                                'gid' => $gid,
                                'fn' => $fullname,
                                'aid' => $aid,
                                'an' => $a_n,
                                'p' => $a_p,
                                'av' => $this->admin[$admin_index]['avatar'],
                                're' => $recs
                            ) , $accept, $index);
                            break;

                        case 5:
                            if (!$this->checkGuest($accept, $index)) return false;
                            $this->send(array(
                                'x' => 6,
                                'a' => 5
                            ) , $accept, $index);
                            $this->close($accept, $index);
                            break;

                        case 6:
                            if (!$this->checkAdmin($accept, $index)) return false;
                            $gid = ForceInt($data['g']);
                            $gix = $this->guestIndex($gid);
                            if ($gix !== false) {
                                if (isset($this->admin[$index])) $this->admin[$index]['guests']-= 1;
                                $this->send(array(
                                    'x' => 6,
                                    'a' => 6
                                ) , $this->accept[$gix], $gix);
                                unset($this->guest[$gix]);
                                $this->close($this->accept[$gix], $gix, 0);
                            }
                            if ($gid) APP::$DB->exe("UPDATE " . TABLE_PREFIX . "guest SET banned = (banned + 1) WHERE gid = '$gid'");
                            break;

                        case 7:
                            if (!$this->checkAdmin($accept, $index)) return false;
                            $gid = ForceInt($data['g']);
                            $gix = $this->guestIndex($gid);
                            if ($gix !== false) $this->send(array(
                                'x' => 6,
                                'a' => 7
                            ) , $this->accept[$gix], $gix);
                            break;

                        case 10:
                            if (!$this->checkAdmin($accept, $index)) return false;
                            $gid = ForceInt($data['g']);
                            $gix = $this->guestIndex($gid);
                            if ($gix !== false) $this->send(array(
                                'x' => 6,
                                'a' => 10
                            ) , $this->accept[$gix], $gix);
                            break;

                        case 11:
                            if (!$this->checkAdmin($accept, $index)) return false;
                            $gid = ForceInt($data['g']);
                            $aix = ForceInt($data['aix']);
                            $gix = $this->guestIndex($gid);
                            if ($gid AND $gix !== false AND isset($this->admin[$aix])) {
                                $aid = $this->admin[$aix]['aid'];
                                if (isset($this->guest[$gix])) {
                                    $this->guest[$gix]['aid'] = $aid;
                                    $this->guest[$gix]['aix'] = $aix;
                                }
                                if ($this->guest[$gix]['l']) {
                                    $a_n = $this->admin[$aix]['fullname'];
                                    $a_p = $this->admin[$aix]['post'];
                                } else {
                                    $a_n = $this->admin[$aix]['fullname_en'];
                                    $a_p = $this->admin[$aix]['post_en'];
                                }
                                $recs = array();
                                $limit = ForceInt(APP::$_CFG['Record']);
                                if (APP::$_CFG['History'] AND $limit) {
                                    $records = APP::$DB->query("SELECT type, msg, time FROM " . TABLE_PREFIX . "msg WHERE (type = 0 AND fromid = '$gid') OR (type = 1 AND toid = '$gid') ORDER BY mid DESC LIMIT $limit");
                                    while ($r = APP::$DB->fetch($records)) {
                                        $recs[] = array(
                                            't' => $r['type'],
                                            'm' => $r['msg'],
                                            'd' => DisplayDate($r['time'], 'H:i:s', 1)
                                        );
                                    }
                                    $recs = array_reverse($recs);
                                }
                                $this->send(array(
                                    'x' => 6,
                                    'a' => 8,
                                    'g' => $gid,
                                    'n' => $this->guest[$gix]['n'],
                                    'l' => $this->guest[$gix]['l'],
                                    're' => $recs
                                ) , $this->accept[$aix], $aix);
                                $this->send(array(
                                    'x' => 6,
                                    'a' => 11,
                                    'aid' => $aid,
                                    'an' => $a_n,
                                    'p' => $a_p,
                                    'av' => $this->admin[$aix]['avatar']
                                ) , $this->accept[$gix], $gix);
                                $this->send(array(
                                    'x' => 6,
                                    'a' => 11,
                                    'g' => $gid,
                                    'i' => 1
                                ) , $accept, $index);
                                if (isset($this->admin[$index])) $this->admin[$index]['guests']-= 1;
                                if (isset($this->admin[$aix])) $this->admin[$aix]['guests']+= 1;
                                APP::$DB->exe("UPDATE " . TABLE_PREFIX . "guest SET aid = '$aid' WHERE gid = '$gid'");
                            } else {
                                $this->send(array(
                                    'x' => 6,
                                    'a' => 11,
                                    'g' => $gid,
                                    'i' => 0
                                ) , $accept, $index);
                            }
                            break;
                        }
                        break;

                    default:
                        $this->close($accept, $index, 0);
                        break;
                    }
                }
                function clearGuest($gid) {
                    foreach ($this->guest AS $k => $g) {
                        if ($g['gid'] == $gid) {
                            $this->send(array(
                                'x' => 6,
                                'a' => 4
                            ) , $this->accept[$k], $k);
                            socket_close($this->accept[$k]);
                            unset($this->accept[$k]);
                            unset($this->cycle[$k]);
                            unset($this->type[$k]);
                            $aix = $g['aix'];
                            if (isset($this->admin[$aix])) $this->admin[$aix]['guests']-= 1;
                            unset($this->guest[$k]);
                        }
                    }
                }
                function guestIndex($gid) {
                    foreach ($this->guest AS $index => $g) {
                        if ($g['gid'] == $gid) return $index;
                    }
                    return false;
                }
                function checkAdmin($accept, $index) {
                    if (array_key_exists($index, $this->admin)) return true;
                    $this->close($accept, $index, 0);
                    return false;
                }
                function checkGuest($accept, $index) {
                    if (array_key_exists($index, $this->guest)) return true;
                    $this->close($accept, $index, 0);
                    return false;
                }
                function select_admin($aid) {
                    $aix = false;
                    $min = 100000;
                    foreach ($this->admin as $k => $a) {
                        if ($aid AND $a['aid'] == $aid) return $k;
                        if (!$a['busy']) {
                            if ($a['guests'] < $min) {
                                $min = $a['guests'];
                                $aix = $k;
                            }
                        }
                    }
                    if ($aix === false) {
                        foreach ($this->admin as $k => $a) {
                            if ($a['guests'] < $min) {
                                $min = $a['guests'];
                                $aix = $k;
                            }
                        }
                    }
                    return $aix;
                }
                function ws_send_all($arr, $index = false) {
                    foreach ($this->admin as $k => $a) {
                        if ($index === $k) {
                            $this->send(array(
                                'x' => 1,
                                'u' => 0,
                                'i' => $arr['i']
                            ) , $this->accept[$k], $k);
                        } else {
                            $this->send($arr, $this->accept[$k], $k);
                        }
                    }
                }
                function search($accept) {
                    $search = array_search($accept, $this->accept, true);
                    if ($search === null) $search = false;
                    return $search;
                }
                function close($accept, $index, $info = 1) {
                    socket_close($accept);
                    unset($this->accept[$index]);
                    unset($this->cycle[$index]);
                    unset($this->type[$index]);
                    if (!$info) return true;
                    if (array_key_exists($index, $this->guest)) {
                        $aix = $this->guest[$index]['aix'];
                        if (isset($this->admin[$aix])) {
                            $this->admin[$aix]['guests']-= 1;
                            $this->send(array(
                                'x' => 6,
                                'a' => 3,
                                'g' => $this->guest[$index]['gid']
                            ) , $this->accept[$aix], $aix);
                        }
                        unset($this->guest[$index]);
                    } elseif (array_key_exists($index, $this->admin)) {
                        $fullname = $this->admin[$index]['fullname'];
                        unset($this->admin[$index]);
                        foreach ($this->guest AS $k => $g) {
                            if ($g['aix'] == $index) $this->send(array(
                                'x' => 6,
                                'a' => 2
                            ) , $this->accept[$k], $k);
                        }
                        $this->ws_send_all(array(
                            'x' => 2,
                            'a' => 2,
                            'ix' => $index,
                            'i' => $fullname
                        ));
                    }
                    return true;
                }
                function send($dataArr, $accept, $index) {
                    if (!$accept) return false;
                    $type = $this->type[$index];
                    if (empty($this->class[$type])) return false;
                    if (!$data = $this->class[$type]->encode($dataArr)) return false;
                    if (!$write = socket_write($accept, $data, strlen($data))) {
                        $this->close($accept, $index, 0);
                        return false;
                    }
                    return true;
                }
                function ip($accept) {
                    socket_getpeername($accept, $ip);
                    return $ip;
                }
                function error() {
                    if (!$this->socket) return -1;
                    return socket_last_error($this->socket);
                }
                function header($data, $accept, $index = 0) {
                    $header = parse_header($data, true);
                    if (strlen($data) >= 4096) return false;
                    if (count($this->accept) > WEBSOCKET_ONLINE) return false;
                    $msg = '';
                    if (trim(implode('', $header)) == '<policy-file-request/>') {
                        $msg.= '<?xml version="1.0"?>';
                        $msg.= '<cross-domain-policy>';
                        $msg.= '<allow-access-from domain="' . ($this->domain ? '*.' . $this->domain : '*') . '" to-ports="*"/>';
                        $msg.= '</cross-domain-policy>';
                        $msg.= "\0";
                        socket_write($accept, $msg, strlen($msg));
                        return false;
                    }
                    $origin = empty($header['origin']) ? empty($header['websocket-origin']) ? '' : $header['websocket-origin'] : $header['origin'];
                    $parse = parse_url($origin);
                    $scheme = empty($parse['scheme']) || $parse['scheme'] != 'https' ? '' : 's';
                    $origin = $origin && !empty($parse['host']) ? 'http' . $scheme . '://' . $parse['host'] : '';
                    if ($this->domain && !empty($parse['host']) && !preg_match('/(^|\.)' . preg_quote($this->domain, '/') . '$/i', $parse['host'])) {
                        return false;
                    }
                    if (!empty($header['sec-websocket-key'])) {
                        $a = base64_encode(sha1(trim($header['sec-websocket-key']) . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11', true));
                        $msg.= "HTTP/1.1 101 Switching Protocols\r\n";
                        $msg.= "Upgrade: websocket\r\n";
                        $msg.= "Connection: Upgrade\r\n";
                        if ($origin) {
                            $msg.= "Sec-WebSocket-Origin: {$origin}\r\n";
                        }
                        $msg.= "Sec-WebSocket-Accept: $a\r\n";
                        $msg.= "\r\n";
                        if (!socket_write($accept, $msg, strlen($msg))) {
                            return false;
                        }
                        return 2;
                    }
                    if (!empty($header['sec-websocket-key1']) && !empty($header['sec-websocket-key2']) && !empty($header['html'])) {
                        $key1 = $header['sec-websocket-key1'];
                        $key2 = $header['sec-websocket-key2'];
                        $key3 = $header['html'];
                        if (!preg_match_all('/([\d]+)/', $key1, $key1_num) || !preg_match_all('/([\d]+)/', $key2, $key2_num)) {
                            return false;
                        }
                        $key1_num = implode($key1_num[0]);
                        $key2_num = implode($key2_num[0]);
                        if (!preg_match_all('/([ ]+)/', $key1, $key1_spc) || !preg_match_all('/([ ]+)/', $key2, $key2_spc)) {
                            return false;
                        }
                        $key1_spc = strlen(implode($key1_spc[0]));
                        $key2_spc = strlen(implode($key2_spc[0]));
                        $key1_sec = pack("N", $key1_num / $key1_spc);
                        $key2_sec = pack("N", $key2_num / $key2_spc);
                        $msg.= "HTTP/1.1 101 Web Socket Protocol Handshake\r\n";
                        $msg.= "Upgrade: WebSocket\r\n";
                        $msg.= "Connection: Upgrade\r\n";
                        if ($origin) {
                            $msg.= "Sec-WebSocket-Origin: {$origin}\r\n";
                        }
                        $msg.= "Sec-WebSocket-Location: ws{$scheme}://{$this->host}:{$this->port}{$this->path}\r\n";
                        $msg.= "\r\n";
                        $msg.= md5($key1_sec . $key2_sec . $key3, true);
                        if (!socket_write($accept, $msg, strlen($msg))) {
                            return false;
                        }
                        return 1;
                    }
                    return false;
                }
            }
            class class_websocket_1 {
                function decode($data) {
                    $len = strlen($data);
                    if ($len < 3) {
                        return false;
                    }
                    $r = '';
                    $k = - 1;
                    $str = '';
                    for ($i = 0; $i < $len; $i++) {
                        $ord = ord($data[$i]);
                        if ($ord == 0) {
                            $k++;
                            $str = '';
                            continue;
                        }
                        if ($ord == 255) {
                            $r = $str;
                            continue;
                        }
                        $str.= $data[$i];
                    }
                    return $r;
                }
                function encode($data) {
                    $data = is_array($data) || is_object($data) ? json_encode($data) : (string)$data;
                    return chr(0) . $data . chr(255);
                }
            }
            class class_websocket_2 {
                function decode($data) {
                    if (strlen($data) < 6) {
                        return '';
                    }
                    $r = '';
                    $back = $data;
                    while ($back) {
                        $type = bindec(substr(sprintf('%08b', ord($back[0])) , 4, 4));
                        $encrypt = (bool)substr(sprintf('%08b', ord($back[1])) , 0, 1);
                        $payload = ord($back[1]) & 127;
                        $datalen = strlen($back);
                        if ($payload == 126) {
                            if ($datalen <= 8) {
                                break;
                            }
                            $len = substr($back, 2, 2);
                            $len = unpack('n*', $len);
                            $len = end($len);
                            if ($datalen < 8 + $len) {
                                break;
                            }
                            $mask = substr($back, 4, 4);
                            $data = substr($back, 8, $len);
                            $back = substr($back, 8 + $len);
                        } elseif ($payload == 127) {
                            if ($datalen <= 14) {
                                break;
                            }
                            $len = substr($back, 2, 8);
                            $len = unpack('N*', $len);
                            $len = end($len);
                            if ($datalen < 14 + $len) {
                                break;
                            }
                            $mask = substr($back, 10, 4);
                            $data = substr($back, 14, $len);
                            $back = substr($back, 14 + $len);
                        } else {
                            $len = $payload;
                            if ($datalen < 6 + $len) {
                                break;
                            }
                            $mask = substr($back, 2, 4);
                            $data = substr($back, 6, $len);
                            $back = substr($back, 6 + $len);
                        }
                        if ($type != 1) {
                            continue;
                        }
                        $str = '';
                        if ($encrypt) {
                            $len = strlen($data);
                            for ($i = 0; $i < $len; $i++) {
                                $str.= $data[$i] ^ $mask[$i % 4];
                            }
                        } else {
                            $str = $data;
                        }
                        $r = $str;
                    }
                    return $r;
                }
                function encode($data) {
                    $data = is_array($data) || is_object($data) ? json_encode($data) : (string)$data;
                    $len = strlen($data);
                    $head[0] = 129;
                    if ($len <= 125) {
                        $head[1] = $len;
                    } elseif ($len <= 65535) {
                        $split = str_split(sprintf('%016b', $len) , 8);
                        $head[1] = 126;
                        $head[2] = bindec($split[0]);
                        $head[3] = bindec($split[1]);
                    } else {
                        $split = str_split(sprintf('%064b', $len) , 8);
                        $head[1] = 127;
                        for ($i = 0; $i < 8; $i++) {
                            $head[$i + 2] = bindec($split[$i]);
                        }
                        if ($head[2] > 127) {
                            return false;
                        }
                    }
                    foreach ($head as $k => $v) {
                        $head[$k] = chr($v);
                    }
                    return implode('', $head) . $data;
                }
            }
            function parse_header($html = '', $strtolower = false) {
                if (!$html) return array();
                $html = str_replace("\r\n", "\n", $html);
                $html = explode("\n\n", $html, 2);
                $header = explode("\n", $html[0]);
                $r = array();
                foreach ($header as $k => $v) {
                    if ($v) {
                        $v = explode(':', $v, 2);
                        if (isset($v[1])) {
                            if ($strtolower) {
                                $v[0] = strtolower($v[0]);
                            }
                            if (substr($v[1], 0, 1) == ' ') {
                                $v[1] = substr($v[1], 1);
                            }
                            $r[trim($v[0]) ] = $v[1];
                        } elseif (empty($r['status']) && preg_match('/^(HTTP|GET|POST)/', $v[0])) {
                            $r['status'] = $v[0];
                        } else {
                            $r[] = $v[0];
                        }
                    }
                }
                if (!empty($html[1])) $r['html'] = $html[1];
                return $r;
            }
            function string_to_array($s) {
                if (get_magic_quotes_gpc()) $s = stripslashes($s);
                $s = str_replace('+', '||6||', $s);
                parse_str($s, $r);
                foreach ($r AS $k => $v) {
                    $r[$k] = htmlspecialchars($v, ENT_QUOTES);
                }
                return $r;
            }
            function decodeChar($s) {
                $s = str_replace(array(
                    '||4||',
                    '||6||'
                ) , array(
                    '&',
                    '+'
                ) , $s);
                return $s;
            }
            function ForceData($str) {
                $str = str_replace(array(
                    '\0',
                    '　',
                    '&',
                    '||4||',
                    '||6||'
                ) , array(
                    '',
                    '',
                    '&amp;',
                    '&amp;',
                    '+'
                ) , trim($str));
                if (function_exists('mysql_real_escape_string')) {
                    $str = mysql_real_escape_string($str);
                } else if (function_exists('mysql_escape_string')) {
                    $str = mysql_escape_string($str);
                } else {
                    $str = addslashes($str);
                }
                return $str;
            } ?>
