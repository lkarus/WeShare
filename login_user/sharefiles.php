<?php
$mode = $_GET['mode'];
require_once("{$_SERVER['DOCUMENT_ROOT']}/vshar/include/membersite_config.php");

if(!$fgmembersite->CheckLogin())
{
    $fgmembersite->RedirectToURL("../");
    exit;
}
?>
<table class="table table-hover">
    <tbody>
    <?php if ($mode == "fromMe") {
        $result = $fgmembersite->shareByMeFiles();
        while ($row = mysql_fetch_array($result)) {

            echo "<tr><td><a href=\"../local_video/index.php?path=/global_share".$fgmembersite->getPath() . "/" . rawurlencode($row['filename']) . '">';
            echo $row['filename'];
            echo "</a></td><td>";
            if ($row['number'] > 1)
                echo $row['name']." and ".((string) $row['number']-1)." more friend(s)";
            else
                echo $row['name'];
            echo "</td><td>";
            echo "<a class=\"btn btn-primary\"
               onclick=\"$.fancybox.open({href: 'sharewith.php?mode=modify&useremail=".$fgmembersite->UserEmail()."&file_id=".$row['file_id']."', type: 'iframe', autoResize: true});\">
                   Option
            </a>";
            echo "</td></tr>";
        }
    }
    else{
        $result = $fgmembersite->shareWithMe();
        while ($row = mysql_fetch_array($result)) {

            echo "<tr><td><a href=\"../local_video/index.php?path=/global_share/".$row['username']."_".$row['id_user']. "/" . rawurlencode($row['filename']) . '">';
            echo $row['filename'];
            echo "</a></td><td>";
            echo $row['name'];
            echo "</td><td>";
            echo "<a class=\"btn btn-primary\"
onclick=\"$.fancybox.open({href: 'reject_confirmation.php?file_id=".$row['file_id']."', type: 'iframe', autoResize: true});\">
                Reject
            </a>";
            echo "</td></tr>";
        }
    }?>
    </tbody>
    <thead>
    <tr>
        <th>File</th>
        <th><?php if ($mode == "fromMe") echo "Share with"; else echo "Share from"; ?></th>
        <th></th>
    </tr>
    </thead>
</table>