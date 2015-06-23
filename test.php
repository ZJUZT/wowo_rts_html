
<html>
	<head>
		<meta http-equiv="Content-type" content="text/html; charset=utf-8">
		<title>Conquer</title>

        <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css" />
        <script src="js/jquery-2.1.4.min.js" type="text/javascript" charset="utf-8"></script>
        <script src="bootstrap/js/bootstrap.min.js"></script>
        <script src="js/jquery.twbsPagination.min.js"></script>
	</head>
 <div class="text-center">
            <ul class="pagination">
            </ul>
        </div>
        <script>
            $('.pagination').twbsPagination({
                totalPages: 20,
                visiblePages: 10,
                href : '?page={{number}}'
//        onPageClick: function (event, page) {
//            $('#page-content').text('Page ' + page);
//        }
            });
        </script>