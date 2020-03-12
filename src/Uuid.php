<?php
namespace CjsToken;
/**
 * Created by PhpStorm.
 * User: jelly
 * Date: 2020-03-11
 * Time: 14:08
 */

class Uuid
{
    const VALID_PATTERN = '^[0-9A-Fa-f]{8}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{12}$';

    const USER_TOKEN_VALID_PATTERN = '^[0-9A-Fa-f]{8}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{12}-[0-9]{2}$-[0-9]{2}$';

    /**
     * 生成唯一
     * @param $helpVal 用于辅助生成唯一的值，如用户ID、手机号、邮箱、trace_id等
     * @return string
     */
    public static function generate4help($helpVal) {
        $uni = sprintf("%s%s%s%s",
                        uniqid('', true) ,
                        Util::randStr(10),
                        $helpVal,
                        microtime(true)
                      );
        return md5($uni);
    }

    public static function formatUuid($uuid) {
        if(mb_strlen($uuid)<32) {
            return $uuid;
        }
        return preg_replace(
            '~^(.{8})(.{4})(.{4})(.{4})(.{12,})$~',
            '\1-\2-\3-\4-\5',
            $uuid
        );
    }

}
