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
    $sql = "select count(*) from rank where win_rate > (select win_rate from rank where u_id = $u_id)";
    $query = mysql_query($sql);
    $row = mysql_fetch_array($query);
    $user_rank = $row[0] + 1;
    ?>
    <div id = "rank_container" class = "box_no_background">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?php echo $_SESSION['username'];?>当前排名:<?php echo $user_rank;?></h3>
            </div>
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>排名</th>
                    <th>指挥官</th>
                    <th>战役次数</th>
                    <th>获胜次数</th>
                    <th>胜率</th>
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
                    $sql = "select count(*) from rank";
                    $query = mysql_query($sql);
                    $row = mysql_fetch_array($query,MYSQL_BOTH);
                    $num_row = $row[0];
                    $topics_one_page = 10;
                    $num_page = floor($num_row / $topics_one_page) + 1;

                    $start = ($current_page-1)*$topics_one_page;

                    $sql = "select * from rank order by win_rate DESC limit $start,$topics_one_page";
                    $query = mysql_query($sql);
                    $i = 1;
                    while($row = mysql_fetch_array($query,MYSQL_BOTH)){
                        if($row['u_name']==$u_name)
                            echo "<tr class = 'success'>";
                        else
                            echo "<tr>";
                        echo "<td>".$i++."</td>";
                        echo "<td>".$row['u_name']."</td>";
                        echo "<td>".$row['play_count']."</td>";
                        echo "<td>".$row['win_count']."</td>";
                        echo "<td>".(100*$row['win_rate'])."%"."</td>";
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