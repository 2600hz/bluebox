<?php defined('SYSPATH') OR die('No direct access allowed.');

$lang = array
(
	// Class errors
	'error_format'  => '你的错误信息必须包含 {message} .',
	'invalid_rule'  => '验证规则: %s 无效',

	// General errors
	'unknown_error' => '验证栏位 %s 时, 未知错误发生.',
	'required'      => '栏位 %s 必填.',
	'min_length'    => '栏位 %s  最少 %d 字符.',
	'max_length'    => '栏位 %s  最多 %d 字符.',
	'exact_length'  => '栏位 %s  必须 %d 字符.',
	'in_array'      => '栏位 %s  需在下拉列表中选中.',
	'matches'       => '栏位 %s  必须与 %s 栏位一致.',
	'valid_url'     => '栏位 %s  无效URL, 起始字符%s://.',
	'valid_email'   => '栏位 %s  无效email地址.',
	'valid_ip'      => '栏位 %s  无效IP地址.',
	'valid_type'    => '栏位 %s  只可以包含 %s 字符.',
	'range'         => '栏位 %s  越界指定范围.',
	'regex'         => '栏位 %s  与给定输入模式不匹配.',
	'depends_on'    => '栏位 %s  依赖于 %s 栏位.',

	// Upload errors
	'user_aborted'  => '文件 %s  上传中被中断.',
	'invalid_type'  => '文件 %s  非法文件格式.',
	'max_size'      => '文件 %s  超出最大允许范围. 最大文件大小 %s.',
	'max_width'     => '文件 %s  的最大允许宽度 %s 是 %spx.',
	'max_height'    => '文件 %s  的最大允许高度 %s 是 %spx.',
);
