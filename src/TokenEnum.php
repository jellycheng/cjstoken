<?php
/**
 * Created by PhpStorm.
 * User: jelly
 * Date: 2020-03-12
 * Time: 14:30
 */

namespace CjsToken;


class TokenEnum
{
    //invalid_type字段的常量值 =============================
    //0-正常
    const INVALID_TYPE_NORMAL = 0;

    //1-自动失效
    const INVALID_TYPE_AUTO = 1;

    //2-相同平台踢下线失效
    const INVALID_TYPE_EXCLUDE = 2;

    //3-用户主动退出
    const INVALID_TYPE_USERLOGOUT = 3;

    //4-人工处理退出
    const INVALID_TYPE_MAN = 4;


    //is_delete字段的常量值  ==================
    //正常
    const IS_DELETE_NORMAL = 0;
    //删除
    const IS_DELETE_DEL = 1;


}
