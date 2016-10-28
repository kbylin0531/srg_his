<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Category</title>
    <!-- style -->
    <link href="http://cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
    <link href="http://cdn.bootcss.com/datatables/1.10.12/css/dataTables.bootstrap.min.css" rel="stylesheet">

    <!-- script -->
    <script src="http://cdn.bootcss.com/jquery/1.11.0/jquery.js"></script>
    <script src="http://cdn.bootcss.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="http://cdn.bootcss.com/datatables/1.10.12/js/jquery.dataTables.min.js"></script>
    <script src="http://cdn.bootcss.com/datatables/1.10.12/js/dataTables.bootstrap.min.js"></script>

    <script src="<?php echo BASE_URL;?>/assets/js/dev.js"></script>
    <style>
        tbody tr.selected {
            background-color: #b0bed9;
        }
    </style>
</head>
<body>
<?php echo $content;?>
</body>
</html>