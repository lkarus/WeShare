<table class="table table-hover">
  <thead>
    <tr>
      <th>Name</th>
      <th>Status</th>
      <th></th>
    </tr>
  </thead>
  <tbody>
  <?php
    require_once("{$_SERVER['DOCUMENT_ROOT']}/vshar/include/membersite_config.php");
    
    if(!$fgmembersite->CheckLogin())
    {
        $fgmembersite->RedirectToURL("../");
        exit;
    }

    $useremail = $_GET['useremail'];
    $q = $_GET['q'];
    $result = $fgmembersite->getRequestedFriend($useremail);
    while ($row = mysql_fetch_array($result)) {
        if ($q != "" && !(strpos( $row['email'], $q ) !== false) && !(strpos( $row['name'], $q ) !== false)){
          continue;
        }
        echo "<tr><td>";
        echo $row['name'];
        echo "</td><td>Pending</td><td>";
        echo "<form action='friends.php' method='post' accept-charset ='UTF-8'>";
        echo "<button class='btn btn-primary' name='acceptFriend' type='submit' value='".$row['email']."'>Accept</button>&nbsp;&nbsp;&nbsp;";
        echo "<button class='btn btn-warning' name='deleteFriend' type='submit' value='".$row['email']."'>Decline</button>";
        echo "</form>";
        echo "</td></tr>";
    }

    $result = $fgmembersite->getFriends($useremail);
    while ($row = mysql_fetch_array($result)) {
        if ($q != "" && !(strpos( $row['email'], $q ) !== false) && !(strpos( $row['name'], $q ) !== false)){
          continue;
        }
        echo "<tr><td>".$row['name']."</td><td>";
        if ($row['online_status'] == 0)
            echo "Offline"; 
        else if ($row['online_status'] == 1)
            echo "Online";
        else if ($row['online_status'] == 2)
            echo "Watching";
        else
            echo $row['online_status'];
        echo "<td><form action='friends.php' method='post' accept-charset ='UTF-8'>";
        echo "<button class='btn btn-danger' name='deleteFriend' type='submit' value='".$row['email']."'>Unfriend</button>";
        echo "</td></tr>";
    }
    ?>
  </tbody>
</table>