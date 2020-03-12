# cjstoken
```
user token、oauth token

依赖仓库：
    1. redis扩展 及php代码库
        https://packagist.org/packages/cjs/redis
        {
            "require": {
                "cjs/redis":"dev-master"
            }
        }
    2. php pdo扩展
```

## composer包引用
```
{
    "require": {
        "cjs/redis":"dev-master",
        "cjs/token":"dev-master"
    }
}
```

## user token支持场景
```
1. 通过用户id生成token并插入 t_user_token_*表及放入redis
2. 通过token查询token信息，返回字段是t_user_token_*表的数据
3. 通过token退出
4. 通过用户ID退出
5. 设置活动时间，每隔一定阀值（暂定10分钟）更新一次db
6. 通过条件分页查询t_user_token_*表记录
```

## t_user_token_* 表结构参考
```

DROP TABLE IF EXISTS `t_user_token_1`;
CREATE TABLE IF NOT EXISTS `t_user_token_1` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'ID,自增主键',
  `user_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '用户ID t_user.user_id',
  `user_token` varchar(50) NOT NULL DEFAULT '' COMMENT '登录态',
  `expire_at` int(10) NOT NULL DEFAULT '0' COMMENT '登录态到期时间,时间戳 0 永不过期',
  `device_id` varchar(50) NOT NULL DEFAULT '' COMMENT '设备唯一ID',
  `active_time` int(10) NOT NULL DEFAULT '0' COMMENT '最后活跃时间',
  `app_platform` varchar(25) NOT NULL DEFAULT '' COMMENT '平台 p-PC i-IOS a-Android h5-H5 mp-小程序',
  `app_type` varchar(50) NOT NULL DEFAULT 'mqj' COMMENT 'APP类型',
  `out_system` varchar(255) NOT NULL DEFAULT '' COMMENT '外部系统调用生成token，可选',
  `invalid_type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '失效类型，0-未失效，1-自动失效 2-相同平台踢下线失效，3-用户主动退出,4-人工处理退出',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除 0-正常 1-删除',
  `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) NOT NULL DEFAULT '0' COMMENT '修改时间',
  `delete_time` int(10) NOT NULL DEFAULT '0' COMMENT '删除时间',
  `modify_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'mysql更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `unq_token` (`user_token`) USING BTREE,
  KEY `idx_user_id` (`user_id`) USING BTREE,
  KEY `idx_modify_time` (`modify_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户登录态表' ROW_FORMAT=COMPACT;

```

## user token使用
```
见源代码demo目录中示例

```


