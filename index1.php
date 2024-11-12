<?php
require_once './GetData1110.php';
$privateKeyFilePath = 'keys/consumer_private_RSA.keystore';
$publicKeyFilePath = 'keys/consumer_public_RSA.keystore';
$result = (new GetData1110())->getData($privateKeyFilePath,$publicKeyFilePath,'zc_prod','2024-10-29');
$result2 = (new GetData1110())->getData($privateKeyFilePath,$publicKeyFilePath,'zc_prod','2024-11-07');
?>
<!DOCTYPE html>
<html lang="zh_cn">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>数据</title>
</head>
<body class="hold-transition sidebar-mini">
<ul>
    <?php foreach ($result2 as $item): ?>
        <li><?php echo $item['infotitle'];?>  -- <?php echo $item['pubtime'];?> </li>
    <?php endforeach; ?>
    <?php foreach ($result as $item): ?>
        <li><?php echo $item['infotitle'];?>  -- <?php echo $item['pubtime'];?> </li>
    <?php endforeach; ?>
</ul>
</body>
</html>