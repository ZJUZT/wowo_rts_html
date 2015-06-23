<!DOCTYPE html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1.0" />
<title>Conquer</title>

<link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css" />
<link rel="stylesheet" href="css/styles.css" type="text/css" media="screen" charset="utf-8">
<link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css" />
    <script src="js/jquery-2.1.4.min.js" type="text/javascript" charset="utf-8"></script>
<script src="js/bootstrap.min.js"></script>

<script src="js/jquery.twbsPagination.min.js"></script>

</head>
<body>
    <?php
    require('checkvalid.php');

    require ('connect.php');
    require ('navigation.php');
    ?>
    <div id = "rank_container" class = "box_no_background">
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
                    echo "<tr>";
                    echo "<td>".$i++."</td>";
                    echo "<td>".$row['u_name']."</td>";
                    echo "<td>".$row['play_count']."</td>";
                    echo "<td>".$row['win_count']."</td>";
                    echo "<td>".$row['win_rate']."</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
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