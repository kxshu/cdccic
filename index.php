<?php
require_once './GetData1110.php';
$privateKeyFilePath = 'keys/consumer_private_RSA.keystore';
$publicKeyFilePath = 'keys/consumer_public_RSA.keystore';

// 获取今天的日期
$today = date('Y-m-d');
$result = (new GetData1110())->getData($privateKeyFilePath, $publicKeyFilePath, 'zc_prod', $today);
$result2 = (new GetData1110())->getData($privateKeyFilePath, $publicKeyFilePath, 'zs_prod', $today);

// 检查是否有日期选择
if (isset($_POST['date'])) {
    $selectedDate = $_POST['date'];
    $result = (new GetData1110())->getData($privateKeyFilePath, $publicKeyFilePath, 'zc_prod', $selectedDate);
    $result2 = (new GetData1110())->getData($privateKeyFilePath, $publicKeyFilePath, 'zs_prod', $selectedDate);
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>数据</title>
    <style>
        /* banner样式 */
        .banner {
            width: 100%;
            height: 200px;
            background-color: #f0f0f0;
            margin-bottom: 20px;
        }
        /* tab容器样式 */
        .tab-container {
            width: 100%;
            border-bottom: 1px solid #ddd;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        /* tab列表样式 */
        .tab-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
        }
        /* tab项目样式 */
        .tab-item {
            padding: 10px 20px;
            cursor: pointer;
            border: 1px solid #ddd;
            margin-bottom: -1px;
            background-color: white;
        }
        /* 选中状态的tab样式 */
        .tab-item.active {
            color: white;
            background-color: red;
            border: 1px solid red;
            border-bottom: 1px solid red;
        }
        /* tab内容区域样式 */
        .tab-content {
            padding: 20px;
            display: none;
        }
        .tab-content.active {
            display: block;
        }
        /* 添加新的列表样式 */
        .content-list {
            list-style: none;
            padding: 0;
        }
        .list-item {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #eee;
        }
        .list-item a {
            color: #333;
            text-decoration: none;
            flex: 1;
        }
        .list-item a:hover {
            color: red;
        }
        .list-date {
            color: #999;
            margin-left: 20px;
        }
        /* 添加新的布局样式 */
        .container {
            width: 1280px;
            margin: 0 auto;
        }
        /* 日期筛选样式 */
        .date-filter {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .date-filter input {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .date-filter button {
            padding: 8px 15px;
            background-color: red;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .date-filter button:hover {
            background-color: darkred;
        }
    </style>
</head>
<body>
    <!-- banner区域 -->
    <div class="banner">
        Banner区域
    </div>

    <!-- 主容器 -->
    <div class="container">
        <!-- tab区域 -->
        <div class="tab-container">
            <ul class="tab-list">
                <li class="tab-item active" onclick="switchTab(0)">资产招租</li>
                <li class="tab-item" onclick="switchTab(1)">资产招商</li>
            </ul>
            
            <!-- 修改日期筛选区域 -->
            <div class="date-filter">
                <form method="POST" id="dateForm">
                    <input type="date" id="datePicker" name="date" value="<?php echo $today; ?>">
                    <button type="submit">查询</button>
                </form>
            </div>
        </div>

        <!-- tab内容区域 -->
        <div class="tab-content active">
            <ul class="content-list" id="contentList1">
                <?php foreach ($result as $item): ?>
                <li class="list-item">
                    <a href="https://www.cdggzy.com/sitenew/notice/ZCZY/zsNoticeContent.aspx?id=<?php echo $item['pkid'];?>"><?php echo $item['infotitle'];?></a>
                    <span class="list-date"><?php echo $item['pubtime'];?></span>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="tab-content">
            <ul class="content-list" id="contentList2">
                <?php foreach ($result2 as $item): ?>
                <li class="list-item">
                    <a href="https://www.cdggzy.com/sitenew/notice/ZCZY/zsNoticeContent.aspx?id=<?php echo $item['pkid'];?>"><?php echo $item['infotitle'];?></a>
                    <span class="list-date"><?php echo $item['pubtime'];?></span>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <script>
        function switchTab(index) {
            const $tabs = document.querySelectorAll('.tab-item');
            const $contents = document.querySelectorAll('.tab-content');
            
            $tabs.forEach(tab => tab.classList.remove('active'));
            $contents.forEach(content => content.classList.remove('active'));
            
            $tabs[index].classList.add('active');
            $contents[index].classList.add('active');
        }

        document.querySelectorAll('.tab-item').forEach((tab, index) => {
            tab.addEventListener('click', () => switchTab(index));
        });
    </script>
</body>
</html> 