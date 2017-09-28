<?php
require_once("{$_SERVER['DOCUMENT_ROOT']}/vshar/include/membersite_config.php");
if (!$fgmembersite->CheckLogin()) {
    $fgmembersite->RedirectToURL("../");
    exit;
}

if (isset($_GET['mode']))
    $mode = $_GET['mode'];
if (isset($_POST['share'])) {
    if ($fgmembersite->shareFile($_POST['useremail'], $_POST['receiver'], $_POST['file_name'], $_POST['current_dir']))
        echo "<script>parent.jQuery.fancybox.close()</script>";
}
else if (isset($_POST['modify'])){
    if ($fgmembersite->modifyRecievers($_POST['receiver'], $_POST['file_id']))
        echo "<script>parent.jQuery.fancybox.close()</script>";
}
else if (isset($_POST['revoke_all'])){
    if ($fgmembersite->revokeAllRecievers($_POST['file_id']))
        echo "<script>parent.jQuery.fancybox.close()</script>";
}
else {?>

<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
    <script type="text/javascript" src="https://netdna.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
    <link href="http://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.3.0/css/font-awesome.min.css"
          rel="stylesheet" type="text/css">
    <link href="../css/sharewith.css" rel="stylesheet" type="text/css">
</head>

<body>
<div class="section">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>Share with...</h1>
                <form class="form-horizontal text-left" role="form">
                    <div class="form-group has-feedback">
                        <div class="col-sm-2">
                            <label for="inputEmail3" class="control-label">Search</label>
                        </div>
                        <div class="col-sm-10">
                            <input type="email" class="form-control" id="inputEmail3" placeholder="Email">
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="row">
            <form action="sharewith.php" method="post">
                <div class="col-md-12 text-right">
                    <div class="container" style="height:300px; overflow-y: auto;">
                        <table class="table">
                            <tbody>
                            <?php
                            $useremail = $_GET['useremail'];
                            if ($mode == "share")
                                $result = $fgmembersite->getFriends($useremail);
                            else
                                $result = $fgmembersite->getSharingFriends($useremail, $_GET['file_id']);
                            while ($row = mysql_fetch_array($result)) {
                                echo "<tr><td>";
                                echo $row['name'];
                                echo "</td><td>";
                                if ($mode == "share")
                                    echo "<input type='checkbox' name='receiver[]' value='" . $row['id_user'] . "'>";
                                else{
                                    if ($row['sharing'])
                                        echo "<input type='checkbox' checked='checked' name='receiver[]' value='" . $row['id_user'] . "'>";
                                    else
                                        echo "<input type='checkbox' name='receiver[]' value='" . $row['id_user'] . "'>";
                                }
                                echo "</td></tr>";
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-right">
                            <div class="btn-group">
                                <?php if ($mode == "share") : ?>
                                    <input type="hidden" name="useremail" value="<?php echo $useremail; ?>"/>
                                    <input type="hidden" name="file_name" value="<?php echo $_GET['file_name']; ?>"/>
                                    <input type="hidden" name="current_dir"
                                           value="<?php echo $_GET['current_dir']; ?>"/>
                                    <input type="submit" name="share" class="btn btn-default" Value="Share"/>
                                <?php else : ?>
                                    <input type="hidden" name="file_id" value="<?php echo $_GET['file_id']; ?>"/>
                                    <input type="submit" name="modify" class="btn btn-default" Value="Modify"/>
                                    <input type="submit" name="revoke_all" class="btn btn-default" Value="Revoke all"/>
                                <?php endif ?>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
</body>

</html>

<?php } ?>