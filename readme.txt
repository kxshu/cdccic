需要扩展
curl、openssl
使用示例
require_once '/path/to/index.php';
$privateKeyFilePath = '/Users/xiehetu/WorkSpace/PHP/MySelf/test/keys/consumer_private_RSA.keystore';
$publicKeyFilePath = '/Users/xiehetu/WorkSpace/PHP/MySelf/test/keys/consumer_public_RSA.keystore';
$result = (new GetData1110())->getData($privateKeyFilePath,$publicKeyFilePath,'zc_prod','2024-10-29');
print_r($result);
第三个参数说明
zc_dev 招租-电子政务网
zc_prod 招租-互联网
zs_dev 招商-电子政务网
zs_prod 招商-互联网
第四个参数说明
传了是指定日期，不传或空字符串是昨日，时间格式为Y-m-d
$result = (new GetData1110())->getData($privateKeyFilePath,'zc_prod','2024-09-01');
print_r($result);
$result 直接是结果
渲染示例
foreach ($result as $item) {
		echo '公告主键_'.$item['pkid'];
		echo '公告标题_'.$item['infotitle'];
		echo '发布时间_'.$item['pubtime'];
		echo '标识，渲染得时候判断'.$item['isdel'];
		echo '更新时间_'.$item['updatetime'];
}
