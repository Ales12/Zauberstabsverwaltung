<?php
if(!defined("IN_MYBB"))
{
    die("Direct initialization of this file is not allowed.");
}


function wand_info()
{
    return array(
        "name"			=> "Zauberstabverwaltung",
        "description"	=> "Hier können die User ihre Zauberstäbe eintragen und es soll einen kurzen Überblick geben, welche Zauberstäbe es schon wie oft gibt.",
        "website"		=> "",
        "author"		=> "",
        "authorsite"	=> "",
        "version"		=> "1.0",
        "guid" 			=> "",
        "codename"		=> "",
        "compatibility" => "*"
    );
}

function wand_install()
{
    global $db, $templates, $mybb;

    //Tabelle für die Datenbank generieren
    $db->query("CREATE TABLE ".TABLE_PREFIX."wand (
   `wid` int(11) NOT NULL AUTO_INCREMENT,
   `uid` int(11) NOT NULL,
   `wood` varchar(500) NOT NULL,
   `core` varchar(500) NOT NULL,
   `length` varchar(500) NOT NULL,
   `flex` varchar(500) NOT NULL,
   PRIMARY KEY (`wid`),
   KEY `wid` (`wid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1");

    //Templates
    $insert_array = array(
        'title'        => 'wand',
        'template'    => $db->escape_string('<html>
<head>
<title>{$mybb->settings[\'bbname\']} - Zauberstabverwaltung</title>
{$headerinclude}
</head>
<body>
{$header}
<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
<tr>
<td class="thead" ><h2>Zauberstabverwaltung</h2></td>
</tr>
<tr>
<td class="trow1" align="center">
	{$wand_form}
</td>
</tr>
	<tr><td class="trow1" align="center">
			<form id="wand_filter" method="post" action="misc.php?action=wand">
				<table><td class="smalltext">Filtern nach:</td>
			<td><select name="wood_type">
				<option value="%" selected>Alle Hölzer</option>
{$wood_filter}
				</select></td>
		<td><select name="core_type">
				<option value="%" selected>Alle Kerne</option>
{$core_filter}
				</select></td>
			<td><select name="flex_type">
							<option value="%" selected>beide Flexibilitäten</option>
				<option value="Flexibel">Flexibel</option>
				<option value="Unflexibel">Unflexibel</option>
				</select>
			</td>
			<td  align="center">
<input type="submit" name="wand_filter" value="Filtern" id="submit" class="button"></td></tr></table>
</form>
		<table width="90%">
		<td class="thead">
			<h2>Besitzer</h2>
		</td>
				<td class="thead">
			<h2>Holz</h2>
		</td>
				<td class="thead">
			<h2>Kern</h2>
		</td>
				<td class="thead">
			<h2>Länge</h2>
		</td>
				<td class="thead">
			<h2>Flexibilität</h2>
		</td>
	</tr>
		{$wand_bit}
	</table>
	
	</td></tr>
</table>
{$footer}
</body>
</html>'),
        'sid'        => '-1',
        'version'    => '',
        'dateline'    => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);

    $insert_array = array(
        'title'        => 'wand_bit',
        'template'    => $db->escape_string('<td class="trow1" align="center">
	{$user}
</td>
	<td class="trow1" align="center">
	{$wood}
</td>
	<td class="trow1" align="center">
	{$core}
</td>
<td class="trow1" align="center">
	{$length}
</td>
<td class="trow1" align="center">
	{$flex}
</td></tr>'),
        'sid'        => '-1',
        'version'    => '',
        'dateline'    => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);

    $insert_array = array(
        'title'        => 'wand_form',
        'template'    => $db->escape_string('	<form id="wand" method="post" action="misc.php?action=wand">
		<table><tr>
			<td class="thead"><h3>Zauberstabholz</h3></td>
				<td class="thead"><h3>Zauberstabkern</h3></td>
				<td class="thead"><h3>Zauberstablänge</h3></td>
				<td class="thead"><h3>Flexibilität</h3></td>
			</tr>
			<tr>
			<td><input type="text" name="wood" id="wood" value="{$row[\'wood\']}" class="textbox" /></td>
		<td><input type="text" name="core" id="core" value="{$row[\'core\']}" class="textbox" /></td>
			<td><input type="text" name="length" id="length" value="{$row[\'length\']}" class="textbox" /></td>
			<td><select name="flex">
				<option value="Flexibel">Flexibel</option>
				<option value="Unflexibel">Unflexibel</option>
				</select>
			</td>
			</tr>
			<tr><td colspan="4" align="center">
<input type="submit" name="wand_submit" value="eintragen" id="submit" class="button"></td></tr></table>
</form>'),
        'sid'        => '-1',
        'version'    => '',
        'dateline'    => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);

}

function wand_is_installed()
{
    global $db;
    if($db->table_exists("wand"))
    {
        return true;
    }
    return false;
}

function wand_uninstall()
{
    global $db;
    if($db->table_exists("wand"))
    {
        $db->drop_table("wand");
    }
    rebuild_settings();

    // Templates entfernen
    $db->delete_query("templates", "title LIKE '%wand%'");

}

function wand_activate()
{

}

function wand_deactivate()
{

}
//wer ist wo
$plugins->add_hook('fetch_wol_activity_end', 'wand_user_activity');
$plugins->add_hook('build_friendly_wol_location_end', 'wand_location_activity');

function wand_user_activity($user_activity){
    global $user;

    if(my_strpos($user['location'], "misc.php?action=wand") !== false) {
        $user_activity['activity'] = "wand";
    }

    return $user_activity;
}

function wand_location_activity($plugin_array) {
    global $db, $mybb, $lang;

    if($plugin_array['user_activity']['activity'] == "wand")
    {
        $plugin_array['location_name'] = "Trägt einen <b><a href='misc.php?action=wand'>Zauberstab</a></b> in die Verwaltung ein.";
    }


    return $plugin_array;
}


//MiSC Seite für die Zauberstäbe

$plugins->add_hook('misc_start', 'wand_show');

function wand_show(){
    global $mybb, $templates, $lang, $header, $headerinclude, $footer, $page, $db, $user, $wood, $core, $length, $flex, $wand_bit, $wood_filter, $core_filter, $character, $delete;

    if($mybb->get_input('action') == 'wand')
    {
        // Do something, for example I'll create a page using the hello_world_template

        // Add a breadcrumb
        add_breadcrumb('Zauberstabverwaltung', "misc.php?action=wand");

        $uid = $mybb->user['uid'];
        if($uid  != '0'){
            $wand_user = $db->simple_select("wand", "*", "uid = '$uid'");
            $row = $db->fetch_array($wand_user);
            $character = $mybb->user['username'];
            eval("\$wand_form = \"".$templates->get("wand_form")."\";");



            if(isset($_POST['wand_submit'])){

                $wood = $_POST['wood'];
                $core = $_POST['core'];
                $length = $_POST['length'];
                $flex = $_POST['flex'];

                $submit = array(
                    "uid" => $db->escape_string($uid),
                    "wood" => $db->escape_string($wood),
                    "core" => $db->escape_string($core),
                    "length" => $db->escape_string($length),
                    "flex" => $db->escape_string($flex)
                );

                if(empty($row)) {
                    $db->insert_query("wand", $submit);
                } else{
                    $db->update_query("wand", $submit, "uid = '$uid'");
                }

                redirect ("misc.php?action=wand");
            }

        }
        //filtern :D

        $wood_query = $db->query("SELECT DISTINCT wood
        from ".TABLE_PREFIX."wand
        ");

        while($wood = $db->fetch_array($wood_query)){
            $wood_filter .= "<option value='{$wood['wood']}'>{$wood['wood']}</option>";
        }

        $core_query = $db->query("SELECT DISTINCT core
        from ".TABLE_PREFIX."wand
        ");

        while($core = $db->fetch_array($core_query)){
            $core_filter .= "<option value='{$core['core']}'>{$core['core']}</option>";
        }

        //filter generieren
        $wood_type = '%';
        $core_type= '%';
        $flex_type = '%';

        if(isset($_POST['wand_filter'])) {
            $wood_type = $db->escape_string($_POST['wood_type']);
            if ($wood_type != '%') {
                $wood_type = $wood_type;
            }

            $core_type = $db->escape_string($_POST['core_type']);
            if ($core_type != '%') {
                $core_type = $core_type;
            }
            $flex_type = $db->escape_string($_POST['flex_type']);
            if ($flex_type != '%') {
                $flex_type = $flex_type;
            }
        }


        $wand_query = $db->query("SELECT *
        FROM ".TABLE_PREFIX."wand w
        LEFT JOIN ".TABLE_PREFIX."users u
        on (u.uid = w.uid)
        WHERE w.wood like '".$wood_type."'
        AND   w.core like '".$core_type."'
        AND w.flex like '".$flex_type."'
        ORDER BY u.username asc, w.wood asc, w.core asc, w.length desc
        ");

        while($row = $db->fetch_array($wand_query)){

            //Verwaltung (löschen)
            if($mybb->usergroup['canmodcp'] == 1) {

                    $delete = "<a href=\"misc.php?action=wand&del=$row[wid]\"><i class=\"fas fa-trash-alt\"></i></a> ";


            }

            $username = format_name($row['username'], $row['usergroup'], $row['displaygroup']);
            $user = build_profile_link($username, $row['uid']);
            $wood = $row['wood'];
            $core= $row['core'];
            $length = $row['length'];
            $flex = $row['flex'];

            eval("\$wand_bit .= \"".$templates->get("wand_bit")."\";");
        }


        $del = $mybb->input['del'];
        if($del){
            $db->delete_query("wand", "wid = '$del'");
            redirect("misc.php?action=wand");
        }

        eval("\$page = \"".$templates->get("wand")."\";");
        output_page($page);
    }

}