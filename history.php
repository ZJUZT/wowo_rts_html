<!DOCTYPE html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1.0" />
    <title>Conquer</title>

    <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" href="css/styles.css" type="text/css" media="screen" charset="utf-8">
    <script src="js/jquery-2.1.4.min.js" type="text/javascript" charset="utf-8"></script>
    <script src="js/bootstrap.min.js"></script>

    <script src="js/jquery.twbsPagination.min.js"></script>

</head>
<body>
<?php
require('checkvalid.php');

require ('connect.php');
require ('navigation.php');
$u_id = $_SESSION['u_id'];
$u_name = $_SESSION['username'];
$sql = "select * from rank where u_id = $u_id";
$query = mysql_query($sql);
$row = mysql_fetch_array($query,MYSQL_BOTH);
$user_win = $row['win_count'];
$user_lose = $row['play_count'] - $row['win_count'];
?>
<div id = "rank_container" class = "box_no_background">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $_SESSION['username'];?>历史战绩:<?php echo $user_win;?>胜<?php echo $user_lose; ?>负</h3>
        </div>
        <table class="table table-hover">
            <thead>
            <tr>

                <th>开始时间</th>
                <th>持续时间</th>
                <th>对手</th>
                <th>收集资源数</th>
                <th>摧毁敌人单位数</th>
                <th>战役结果</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if(!isset($_GET['page'])){
                $current_page = 1;
            }
            else{
                $current_page = $_GET['page'];
            }
            $sql = "select count(*) from battle where user1_name = '$u_name' or user2_name = '$u_name''";
            $query = mysql_query($sql);
            $row = mysql_fetch_array($query,MYSQL_BOTH);
            $num_row = $row[0];
            $topics_one_page = 10;
            $num_page = floor($num_row / $topics_one_page) + 1;

            $start = ($current_page-1)*$topics_one_page;

            $sql = "select * from battle where user1_name = '$u_name' or user2_name = '$u_name' ORDER BY begin_time DESC  limit $start,$topics_one_page";
            $query = mysql_query($sql);
            //$i = 1;
            while($row = mysql_fetch_array($query,MYSQL_BOTH)){
                if($row['winner_name']==$u_name)
                    echo "<tr class = 'success'>";
                else
                    echo "<tr>";
                echo "<td>".(date("Y-m-d H:i:s",$row['begin_time']))."</td>";
                echo "<td>".(floor($row['last_time']/60))." minutes ".($row['last_time']-floor($row['last_time']/60)*60)." seconds"."</td>";
                if($row['user1_name']!=$u_name){
                    echo "<td>".$row['user1_name']."</td>";
                    echo "<td>".$row['user2_money']."</td>";
                    echo "<td>".$row['user2_destroy']."</td>";
                }
                else{
                    echo "<td>".$row['user2_name']."</td>";
                    echo "<td>".$row['user1_money']."</td>";
                    echo "<td>".$row['user1_destroy']."</td>";
                }
                if($row['winner_name']==$u_name)
                    echo "<td>"."Win"."</td>";
                else
                    echo "<td>"."lose"."</td>";
                echo "</tr>";
            }
            ?>
            </tbody>
        </table>
    </div>
    <div class="text-center">
        <ul class="pagination">
        </ul>
    </div>
    <script>
        $('.pagination').twbsPagination({
            totalPages: <?php echo $num_page;?>,
            visiblePages: 10,
            href : '?page={{number}}'
//        onPageClick: function (event, page) {
//            $('#page-content').text('Page ' + page);
//        }
        });
    </script>
</div>



</body>