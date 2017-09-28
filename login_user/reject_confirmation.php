<?php
require_once("{$_SERVER['DOCUMENT_ROOT']}/vshar/include/membersite_config.php");
if (!$fgmembersite->CheckLogin()) {
    $fgmembersite->RedirectToURL("../");
    exit;
}
if (isset($_GET['confirm'])) {
    if ($fgmembersite->rejectFile($_GET['file_id']))
        echo "<script>parent.jQuery.fancybox.close()</script>";
    else
        echo "Error";
}
else {
?>

<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
    <script type="text/javascript" src="http://netdna.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
    <link href="http://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.3.0/css/font-awesome.min.css"
          rel="stylesheet" type="text/css">
    <link href="http://pingendo.github.io/pingendo-bootstrap/themes/default/bootstrap.css"
          rel="stylesheet" type="text/css">
</head>

<body>
<div class="section">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>Rejecting file</h1>
                <p>You are going to reject this shared file. Are you sure?</p>
                <div class="row">
                    <div class="col-md-12 text-right">
                        <div class="btn-group">
                            <a href="reject_confirmation.php?confirm=true&file_id=<?php echo $_GET['file_id']; ?>" class="btn btn-danger">Reject file</a>
                            <a href="javascript:parent.jQuery.fancybox.close();" class="btn btn-default">Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>

</html>
<?php } ?>