
<!doctype html>
<html>
<head>
    <meta http-equiv="Content-Style-Type" content="text/css">
    <meta charset="utf-8">
    <meta name="description" content="Trascription factors for grasses ">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.0.3/jquery-confirm.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.0.3/jquery-confirm.min.js"></script>
    
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    
    <script src="/js/typeahead.min.js"></script>
    
    <link rel="stylesheet" href="/css/gramene_org.css" type="text/css">
    
    <link rel="stylesheet" href="/css/gdc_cancer_gov.css" type="text/css">
    <script type="text/javascript" src="/js/wfui.js"></script>
 
    
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.css">
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.js"></script>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js" integrity="sha512-57oZ/vW8ANMjR/KQ6Be9v/+/h6bq9/l3f0Oc7vn6qMqyhvPd1cvKBRWWpzu0QoneImqr2SkmO4MSqU+RpHom3Q==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css" integrity="sha512-ELV+xyi8IhEApPS/pSj66+Jiw+sOT1Mqkzlh8ExXihe4zfqbWkxPRi8wptXIO9g73FSlhmquFlUOuMSoXz5IRw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <link rel="stylesheet" href="/css/master.css" type="text/css">
    
    <!--   <script src="js/sorttable.js"></script> -->
    <title><?php echo $title ?></title>
    
</head>

<body>
    <div id="page-wrapper" class="page-wrapper">
        <div id="page" class="page">

            <?php require_once "header.php"; ?>

            <div class="welcome container" >
                <?php $this->renderSection('content') ?>
            </div>

            <?php require_once "footer.php"; ?>
        </div>
    </div>
</body>
</html>