<?php // misc.php :: Random functions that really don't fit anywhere else.

function iddqd() { 
    
    global $acctrow;
    doquery("UPDATE {{table}} SET extra1='1' WHERE id='".$acctrow["id"]."' LIMIT 1", "accounts");
    $page = "\"Please don't take everything I say so seriously.\"<br /><i><a href=\"http://www.nin.com\">---Trent Reznor</a></i>";
    display("LMFAO!", $page);
    
}

function version() {
    
    global $version, $bname, $bdate, $bnumber;
    
$page = <<<END
<table width="90%">
<tr><td width="30%">Version Number:</td><td><b>$version</b></td></tr>
<tr><td>Build Number:</td><td><b>$bnumber</b></td></tr>
<tr><td>Build Name:</td><td><b>$bname</b></td></tr>
<tr><td>Build Date:</td><td><b>$bdate</b></td></tr>
<tr><td colspan="2"><a href="changelog.txt" target="_new">View the Changelog</a></td></tr>
</table>
END;

    display("Version Information",$page);
    
}

function babblebox2() {
    
    global $userrow, $controlrow;
    
    if (isset($_GET["g"])) {
        $guild = $userrow["guild"];
        $g = "WHERE guild='$guild'";
        $g2 = ", guild='$guild'";
        $row["guild"] = "&g=yes";
    } else {
        $g = "WHERE guild='0'";
        $row["guild"] = "";
    }
    
    if (isset($_POST["babble"])) {
        
        // Add new shout.
        if (trim($_POST["babble"]) != "") { 
            $insert = doquery("INSERT INTO {{table}} SET id='', posttime=NOW(), charid='".$userrow["id"]."', charname='".$userrow["charname"]."', content='".$_POST["babble"]."' $g2", "babblebox");
        }
        
        // Only keep 20 shouts in DB at any one time.
        $check = doquery("SELECT * FROM {{table}} $g", "babblebox");
        if (mysql_num_rows($check) > 20) {
            $delete1 = dorow(doquery("SELECT id FROM {{table}} $g ORDER BY id LIMIT 1", "babblebox"));
            $delete2 = doquery("DELETE FROM {{table}} WHERE id='".$delete1["id"]."' LIMIT 1", "babblebox");
        }
        
        // And we're done.
        die(header("Location: index.php?do=babblebox".$row["guild"]));
        
    }
    
    $shouts = dorow(doquery("SELECT * FROM {{table}} $g ORDER BY id LIMIT 20", "babblebox"), "id");
    $row["shouts"] = "";
    $background = 1;
    if ($shouts != false) {
        foreach ($shouts as $a => $b) {
            $row["shouts"] .= "<div class=\"babble$background\">[<a href=\"users.php?do=profile&uid=".$b["charid"]."\" target=\"_parent\">".$b["charname"]."</a>] ".$b["content"]."</div>\n";
            if ($background == 1) { $background = 2; } else { $background = 1; }
        }
    } else {
        $row["shouts"] = "<div class=\"babble$background\">No shouts.</div>";
    }

    $page = parsetemplate(gettemplate("misc_babblebox"),$row);
    if ($controlrow["compression"] == 1) { ob_start("ob_gzhandler"); }
    echo $page;
    die();
    
}

function showmap() { 
    
    global $controlrow;
    
    $page = gettemplate("misc_showmap");
    if ($controlrow["compression"] == 1) { ob_start("ob_gzhandler"); }
    echo $page;
    die();

}
    

?>