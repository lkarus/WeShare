<!DOCTYPE html>
<html>
<head>
</head>
<body>

<table class="table table-hover">
    <thead>
        <tr>
            <th width="35"></th>
            <th class="text-primary">Name</th>
            <th width="89" class="text-primary text-center">Status</th>
        </tr>
    </thead>
    <?php
    require_once("{$_SERVER['DOCUMENT_ROOT']}/vshar/include/membersite_config.php");
    
    if(!$fgmembersite->CheckLogin())
    {
        $fgmembersite->RedirectToURL("../");
        exit;
    }

    $useremail = $_GET['q'];
    $result = $fgmembersite->getFriends($useremail);
    while ($row = mysql_fetch_array($result)) {
        echo "<tr><td></td><td>";
        echo $row['name'];
        echo "</td><td>";
        if ($row['online_status'] == 0)
            echo "Offline"; 
        else if ($row['online_status'] == 1)
            echo "Online";
        else if ($row['online_status'] == 2)
            echo "Watching";
        else
            echo $row['online_status'];
        echo "</td></tr>";
    }
    ?>
</table>
<table class="table table-hover">
    <thead>
        <tr>
            <th width="35"></th>
            <th class="text-primary">Name</th>
            <th width="89" class="text-primary text-center">Status</th>
        </tr>
    </thead>
    <?php
    $result = $fgmembersite->getRequestedFriend($useremail);
    while ($row = mysql_fetch_array($result)) {
        echo "<tr><td></td><td>";
        echo $row['name'];
        echo "</td><td>";
        echo "<form id='friendListSubmit' action='index.php' method='post' accept-charset = 'UTF-8'>";
        echo "<button name='acceptFriend' type='submit' value='".$row['email']."'>Accept</button>";
        echo "<button name='ignoreFriend' type='submit' value='".$row['email']."'>Ignore</button>";
        echo "</form>";
        echo "</td></tr>";
    }
    ?>
</table>
</body>
</html>