<?php
/*
  Plugin Name: wedstrijd
  Plugin URI: http://www.forwodians.nl
  Description: Basketball wedstrijd Plugin
  Version: 2.0
  Author: Gerard Edelaar
  Author URI: http://www.forwodians.nl
  License: GPLv2
 */
//error_reporting(E_ALL);
defined('ABSPATH') or die('No script kiddies please!');
define('WEDSTRIJDEN_WIDGET_ID', "widget_display_wedstrijden");
add_action('admin_menu', 'wedstrijd');

/**
 * Register with hook 'wp_enqueue_scripts', which can be used for front end CSS and JavaScript
 */
add_action('wp_enqueue_scripts', 'prefix_add_my_stylesheet');

/**
 * Enqueue plugin style-file
 */
function prefix_add_my_stylesheet() {
// Respects SSL, Style.css is relative to the current file
    wp_register_style('prefix-style', plugins_url('style.css', __FILE__));
    wp_enqueue_style('prefix-style');
}

/* function wpb_adding_scripts() {
  wp_register_script('my_calendar_script', plugins_url('js/calendar.js', __FILE__));
  wp_enqueue_script('my_calendar_script');
  wp_enqueue_script('jquery');
  wp_enqueue_script('jquery-ui-core');
  wp_enqueue_script('jquery-ui-datepicker');
  wp_enqueue_style('jquery-ui-css', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
  }

  add_action('wp_enqueue_scripts', 'wpb_adding_scripts');
 */

//include("../themes/kid/functions.php");
//include("././fwBar/iCall.php");

function tijd_opmaak($tijd) {
    if (strrpos($tijd, ":"))
        return $tijd;
    return(substr($tijd, 0, 2) . ":" . substr($tijd, 3, 2));
}

function naam_opmaak($voornaam, $tussenvoegsel, $achternaam) {
    if ($tussenvoegsel == "")
        return($voornaam . " " . $achternaam);
    return($voornaam . " " . $tussenvoegsel . " " . $achternaam);
}

function get_href_ical($datum, $tijd, $locatie, $oms, $reftxt) {
//$hUrl = "<a href='http://www.forwodians.nl/fwBar/iCall.php?dt=".$datum."&td=".$tijd."&lc=".str_replace(" ","+",$locatie)."&tp=".str_replace(" ","+",$oms)."'>".$reftxt."</a>";
    $hUrl = "<a href='../fwBar/iCall.php?dt=" . $datum . "&td=" . $tijd . "&lc=" . str_replace(" ", "+", $locatie) . "&tp=" . str_replace(" ", "+", $oms) . "'>" . $reftxt . "</a>";

//echo $hUrl;
//die; 
    return $hUrl;
}

function get_href_route($accomodatie, $adres, $postcode, $plaats, $tekst, $target) {
    $hUrl = "<a href=http://" . str_replace(" ", "+", routeplan($adres, $postcode, $plaats)) . " title='" . $accomodatie . " " . $adres . " " . $plaats . "' target=" . $target . ">" . $tekst . "</a>";
//echo $hUrl;
//die; 
    return $hUrl;
}

function routeplan($adres, $postcode, $woonplaats) {
    $sep = "/";
    $conc_q = "?";
    $conc_a = "&amp;amp;&amp;";
    $hUrl = "maps.google.nl/";
    $maps = "maps/dir/";
    $saddr = "Nijverheidsweg+6+2215+MH+Voorhout";
//$daddr = $adres."+".$postcode."+".$woonplaats;
    $daddr = $adres . "+" . $postcode;
//$zoom="z=13";
    $hUrl .= $maps . $saddr . $sep . $daddr;
    return $hUrl;
}

function wedstrijd() {
    $allowed_group = 'manage_options';

    if (function_exists('add_menu_page')) {
        add_menu_page(__('Wedstrijden', 'wedstrijden'), __('Wedstrijden', 'wedstrijden'), $allowed_group, 'wedstrijden', 'display_wedstrijd');
        add_submenu_page('wedstrijden', 'edit', 'Edit', $allowed_group, 'Edits', 'display_footer');
        add_submenu_page('wedstrijden', 'edit', 'Baroverzicht', $allowed_group, 'Baroverzicht', 'Baroverzicht_maken');
//add_submenu_page('wedstrijden','edit', 'Baroverzichtx', $allowed_group,'Baroverzichtx','bardienst_leden');
    }
}

function Baroverzicht_maken() {
    global $wpdb;
    $sql = ' SELECT id, STR_TO_DATE(datum, "%d-%m-%Y") as dt_datum, tijd, thuis, lidnummer, voornaam, tussenvoegsel, naam, telnr, dienst,email ';
    $sql .= " FROM wedstrijden";
    $sql .= " left outer join bardienst on (wedstrijden.poule=bardienst.poule and wedstrijden.code=bardienst.code)";
    $sql .= " left outer join leden on bardienst.lidnummer=leden.Lidnr ";
    $sql .= " where (dienst<8  ) and accode='VHTSC' ";
    $sql .= ' and STR_TO_DATE(datum, "%d-%m-%Y")  >= DATE_SUB(CURDATE(), INTERVAL 1 DAY)';
    $sql .= ' and STR_TO_DATE(datum, "%d-%m-%Y")  < DATE_SUB(CURDATE(), INTERVAL -365 DAY)';
    $sql .= " order by dt_datum,tijd ASC";
//echo "sql = " . "$sql";
    $results = $wpdb->get_results($sql);
    if (!empty($results)) {
        ?>
        <table class=<?php echo $cl_option; ?>wd-pretty-tables>
            <thead>
            <th><?php _e('datum', 'wedstrijden') ?></th>
            <th><?php _e('tijd', 'wedstrijden') ?></th>
            <th><?php _e('team', 'wedstrijden') ?></th>
            <th><?php _e('ouder van', 'wedstrijden') ?></th>
            <th><?php _e('telefoonnr', 'wedstrijden') ?></th>
            <th><?php _e('email adres', 'wedstrijden') ?></th>
            <th><?php _e('Bevestiging', 'wedstrijden') ?></th>
            <th><?php _e('Weigering', 'wedstrijden') ?></th>
            <th><?php _e('Geen bardienst', 'wedstrijden') ?></th>
            <th><?php _e('huidige status', 'wedstrijden') ?></th>
        </thead>
        <tbody>
            <?php
            $class = '';
            foreach ($results as $result) {
                $class = ($class == 'alt') ? "" : 'alt';
                ?>

                <tr class="<?php echo $class; ?>">
                    <td scope="row">
                    <td scope="row"><?php echo $result->dt_datum; ?></td>
                    <td scope="row"><?php echo substr($result->tijd, 0, 2) . ":" . substr($result->tijd, 3, 2); ?></td>
                    <td scope="row"><?php echo $result->thuis; ?></td>
                    <td scope="row"><?php echo $result->lidnummer . " " . $result->voornaam . " " . $result->tussenvoegsel . " " . $result->naam; ?></td>
                    <td scope="row"><?php echo $result->telnr; ?></td>
                    <td scope="row">			<form>
                            <input type="text" name="email" value="<?php echo $result->email; ?>" ><br></td>				</form>
                    <td scope="row"><?php echo "<a href='http://www.forwodians.nl/fwBar/index.php/leden/email_aanpassing/" . $result->lidnummer . "/email'>" . email . "</a>"; ?></td>
                    <td scope="row"><?php echo "<a href='http://www.forwodians.nl/fwBar/index.php/leden/yesbardienst/" . strlen($result->id) . $result->id . "'>" . $result->id . "</a>"; ?></td>
                    <td scope="row"><?php echo "<a href='http://www.forwodians.nl/fwBar/index.php/leden/nobardienst/" . strlen($result->id) . $result->id . "'>" . $result->id . "</a>"; ?></td>
                    <td scope="row"><?php echo "<a href='http://www.forwodians.nl/fwBar/index.php/leden/neverbardienst/" . strlen($result->id) . $result->id . "'>" . $result->id . "</a>"; ?></td>
                    <td scope="row"><?php echo $result->dienst; ?><br><hr></td>
                </tr>

                <?php
            }
            ?>
        </tbody>
        </table>
        <?php
    }
//echo '<a href="/fwBar/index.php/leden/vul_diensten">Vulxxx bardiensten</a>';
}

function edit_options() {
    echo '<a href="/fwBar/index.php/leden/vul_diensten">Vul bardiensten</a>';
}

// gevuld via de widget
function display_wedstrijd() {
//var_dump(get_option("widget_display_wedstrijden[team]" ));
    $options = get_option("widget_display_wedstrijden[team]");
    $team = $options['team'];
    $mode = $options['mode'];

//echo "team = " . $team;
//die;
    if (!empty($team)) {
        toon_gegevens($team, "side_", $mode);
    } else {
        get_side_wedstrijd("all", "side_");
    }
//echo 'edit hello world';
}

function toon_gegevens($poule, $cl_option, $mode) {
//echo "mode = ". $mode . "<br>";
//echo "team = ". $poule . "<br>";
    if (empty($mode)) {
        $mode = $_GET['mode'];
    }
//	echo "mode = ". $mode . "<br>";
    switch ($mode) {
        case "tafelaars":
            get_tafelaars($poule, $cl_option);
            break;
        case "all":
            get_all($poule, $cl_option);
            break;
        case "training":
            get_training($poule, $cl_option);
            break;
        case "leden":
            get_leden($poule, $cl_option);
            get_not_reg_leden($poule, $cl_option);
            get_bankers($poule, $cl_option);
            break;
        case "stats":
            get_stats($poule, $cl_option);
            break;

        case "chauffeurs":
            get_chauffeurs($poule, $cl_option);
            break;
        case "bardienst":
            get_bardienst($poule, $cl_option);
            break;
        case "scheidsrechters":
            get_scheidsrechters($poule, $cl_option);
            break;
        case "wedstrijd":
            get_wedstrijd_div($poule, $cl_option);
            break;
        case "strookjes":
            create_strookjes();
            break;

        default:
            get_wedstrijd_div($poule, $cl_option);
    }
    if ($poule != 'all') {
//display_footer();
    }
}

function display_footer() {
    ?>
    <a href="<?php $url = plugins_url(); ?>?mode=chauffeurs">chauffeurs</a>
    <a href="<?php $url = plugins_url(); ?>?mode=bardienst">bardienst</a>
    <a href="<?php $url = plugins_url(); ?>?mode=scheidsrechters">scheidsrechters</a>
    <a href="<?php $url = plugins_url(); ?>?mode=tafelaars">tafelaars</a>
    <?php
}

function get_wedstrijd($poule, $cl_option) {
    global $wpdb;
    $poule = trim(htmlspecialchars(mysql_real_escape_string($poule)));
    if ($poule == "") {
        $sql = ' select *, STR_TO_DATE(datum, "%d-%m-%Y") as dt_datum from wedstrijden';
        $sql = $sql . ' where STR_TO_DATE(datum, "%d-%m-%Y") >= DATE_SUB(CURDATE(), INTERVAL 0 DAY)';
        $sql = $sql . ' order by dt_datum, tijd ASC';
    } else {
        $sql = 'select *, STR_TO_DATE(datum, "%d-%m-%Y") as dt_datum from wedstrijden where poule = "' . $poule . '"';
        $sql = $sql . " order by dt_datum,tijd ASC";
    }
//echo "sql = " . "$sql";
    $results = $wpdb->get_results($sql);
    if (!empty($results)) {
        ?>
        <table class=<?php echo $cl_option; ?>wd-pretty-table>
            <thead>
            <th class="wedstrijdlijst_header"  scope="row"><?php _e('datum ', 'wedstrijden') ?></th>
            <th class="wedstrijdlijst_header"><?php _e('tijd ', 'wedstrijden') ?></th>
            <th class="wedstrijdlijst_header"><?php _e('thuis ', 'wedstrijden') ?></th>
            <th class="wedstrijdlijst_header"><?php _e('uit ', 'wedstrijden') ?></th>
            <th class="wedstrijdlijst_header" ><?php _e('uitslag ', 'wedstrijden') ?></th>
            <th class="wedstrijdlijst_header"><?php _e('accommodatie ', 'wedstrijden') ?></th>
        </thead>
        <tbody>
            <?php
            $class = '';
            foreach ($results as $result) {
                if ($result->accode == "VHTSC") {
                    $class = '';
                } else {
                    $class = "alt";
                }
                //$class=($class=='alt')?"":'alt';
                ?>
                <tr class="<?php echo $class; ?>">
                    <td scope="row"><?php echo $result->datum; ?></td>
                    <td scope="row"><?php echo substr($result->tijd, 0, 2) . ":" . substr($result->tijd, 3, 2); ?></td>
                    <td scope="row"><?php echo $result->thuis; ?></td>
                    <td scope="row"><?php echo $result->uit; ?></td>
                    <td scope="row"><?php echo $result->uitslag; ?></td>
                    <td scope="row"><?php echo $result->accommodatie; ?></td>
                </tr>
                <?php
            }
            ?>
        </tbody>
        </table>
        <?php
    }
}

function get_wedstrijd_div($poule, $cl_option) {
    global $wpdb;
    /* $everything = get_defined_vars();
      ksort($everything);
      echo ' <

      pre>';
      print_r($everything);
      echo '</pre>'; */
    $poule = trim(htmlspecialchars(mysql_real_escape_string($poule)));
    if ($poule == "") {
        $sql = 'select *, STR_TO_DATE(datum, "%d-%m-%Y") as dt_datum from wedstrijden';
        $sql = $sql . ' where STR_TO_DATE(datum, "%d-%m-%Y") >= DATE_SUB(CURDATE(), INTERVAL 0 DAY)';
        $sql = $sql . ' order by dt_datum, tijd ASC';
    } else {
        $sql = 'select *, STR_TO_DATE(datum, "%d-%m-%Y") as dt_datum from wedstrijden where poule = "' . $poule . '"';
        $sql = $sql . " order by dt_datum,tijd ASC";
    }
//echo "sql = " . "$sql";
    $results = $wpdb->get_results($sql);
    if (!empty($results)) {
        ?>


        <div class="divTable">
            <div class="divTableHeading">
                <div class="divTableRowHead">

                    <div class="divTableHead fwd_datum"><?php _e('datum ', 'wedstrijden') ?></div>
                    <div class="divTableHead fwd_tijd"><?php _e('tijd ', 'wedstrijden') ?></div>
                    <div class="divTableHead fwd_thuis"><?php _e('thuis ', 'wedstrijden') ?></div>
                    <div class="divTableHead fwd_uit"><?php _e('uit ', 'wedstrijden') ?></div>
                    <div class="divTableHead fwd_officials"><?php _e('officials ', 'wedstrijden') ?></div>
                    <div class="divTableHead fwd_accomodatie"><?php _e('accommodatie ', 'wedstrijden') ?></div>
                </div>
            </div>
            <div class="divTableBody">
                <?php
                $class = '';
                foreach ($results as $result) {
                    if ($result->accode == "VHTSC") {
                        $class = '';
                    } else {
                        $class = "alt";
                    }
                    //$class=($class=='alt')?"":'alt';

                    $off_namen = "";
                    $s_off_namen = "";
                    $t_off_namen = "";
                    if (isset($result->naam01)) {
                        $off_namen .= $result->naam01 . "(S) ";
                        $s_off_namen .= $result->naam01 . "(S)\r\n ";
                    }
                    if (isset($result->naam02)) {
                        $off_namen .= $result->naam02 . "(S) / ";
                        $s_off_namen .= $result->naam02 . "(S)\r\n ";
                    }
                    if (isset($result->naam03)) {
                        $off_namen .= $result->naam03 . "(T) ";
                        $t_off_namen .= $result->naam03 . "(T)\r\n ";
                    }
                    if (isset($result->naam04)) {
                        $off_namen .= $result->naam04 . "(T)\r\n ";
                        $t_off_namen .= $result->naam04 . "(T)\r\n ";
                    }
                    if (isset($result->naam05)) {
                        $off_namen .= $result->naam05 . "(T)";
                        $t_off_namen .= $result->naam05 . "(T)";
                    }
                    ?>

                    <div class="divTableRowFw <?php echo $class; ?>">
                        <div class="divTableCell fwd_col1">
                            <div class="divTableCellx fwd_wedstrijdId"><?php echo $result->poule . "-" . $result->code; ?></div>
                            <div class="divTableCellx fwd_datum"><?php echo get_href_ical($result->datum, $result->tijd, $result->accommodatie . "," . $result->adres . "," . $result->plaats, "Wedstrijd " . $result->poule . "-" . $result->code . " " . $result->thuis . "-" . $result->uit, $result->datum); ?></div>
                            <div class="divTableCellx fwd_tijd"><?php echo substr($result->tijd, 0, 2) . ":" . substr($result->tijd, 3, 2); ?></div>
                            <div class="divTableCellx fwd_accomodatie_1"><?php echo get_href_route($result->accommodatie, $result->adres, $result->postcode, $result->plaats, $result->accommodatie, "_blank"); ?></div>
                        </div>
                        <div class="divTableCell fwd_col2">
                            <div class="divTableCellx fwd_thuis"><?php echo $result->thuis; ?></div>                            
                            <div class="divTableCellx fwd_officials_1"><?php echo $s_off_namen; ?></div>
                        </div>
                        <div class="divTableCell fwd_col3">
                            <div class="divTableCellx fwd_uit"><?php echo $result->uit; ?></div>
                            <div class="divTableCellx fwd_officials_1"><?php echo $t_off_namen; ?></div>
                        </div>
                        <div class="divTableCell fwd_col4">
                            <div class="divTableCellx fwd_officials_2"><?php echo $off_namen; ?></div>
                        </div>
                        <div class="divTableCell fwd_col5">
                            <div class="divTableCellx fwd_accomodatie_2"><?php echo get_href_route($result->accommodatie, $result->adres, $result->postcode, $result->plaats, $result->accommodatie, "_blank"); ?></div>
                        </div>


                        <?php
                    }
                    ?>      </div>
            </div>
            <?php
        }
    }

    function get_side_wedstrijd($poule, $cl_option) {
        global $wpdb;
        $poule = trim(htmlspecialchars(mysql_real_escape_string($poule)));
        if ($poule == "all") {
            $sql = 'select *, STR_TO_DATE ( datum, "%d-%m-%Y" )

as dt_datum from wedstrijden';
            $sql = $sql . ' where STR_TO_DATE(datum, "%d-%m-%Y") >= DATE_SUB(CURDATE(), INTERVAL 0 DAY)';
            $sql = $sql . ' order by dt_datum, tijd ASC';
        } else {
            $sql = 'select *, STR_TO_DATE(datum, "%d-%m-%Y") as dt_datum from wedstrijden where poule = "' . $poule . '"';
            $sql = $sql . " order by dt_datum,tijd ASC";
        }
//	echo "sql = " . "$sql";
        $results = $wpdb->get_results($sql);
        if (!empty($results)) {
            ?>
            <table class=<?php echo $cl_option; ?>wd-pretty-table>
                <thead>
                </thead>
                <tbody>
                    <?php
                    $class = '';
                    foreach ($results as $result) {
                        $class = ($class == 'alt') ? "" : 'alt';
                        ?>
                        <tr class="<?php echo $class; ?>"><td scope="rowdatum"><?php echo $result->datum; ?> om <?php echo substr($result->tijd, 0, 2) . ":" . substr($result->tijd, 3, 2); ?></td></tr>
                        <tr class="<?php echo $class; ?>"><td scope="row">thuis: <?php echo $result->thuis; ?></td></tr>
                        <tr class="<?php echo $class; ?>"><td scope="row">uit: <?php echo $result->uit; ?></td></tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
            <?php
        }
    }

    function get_tafelaars($poule, $cl_option) {
        global $wpdb;
        $poule = trim(htmlspecialchars(mysql_real_escape_string($poule)));
        if ($poule == "") {

            $sql = 'select *, ';
            $sql .= 'e.naam as enm, e.tussenvoegsel as etv, e.voornaam as evn, ';
            $sql .= 'f.naam as fnm, f.tussenvoegsel as ftv, f.voornaam as fvn, ';
            $sql .= 'g.naam as gnm, g.tussenvoegsel as gtv, g.voornaam as gvn, ';
            $sql .= 'h.naam as hnm, h.tussenvoegsel as htv, h.voornaam as hvn, ';
            $sql .= 'i.naam as inm, i.tussenvoegsel as itv, i.voornaam as ivn, ';
            $sql .= 'j.naam as jnm, j.tussenvoegsel as jtv, j.voornaam as jvn, ';
            $sql .= 'STR_TO_DATE(datum, "%d-%m-%Y") as dt_datum from wedstrijden a';
//$sql=$sql . ' left JOIN leden c ON c.lidnr = a.official01';
//$sql=$sql . ' left JOIN leden d ON d.lidnr = a.official02';
            $sql = $sql . ' left JOIN leden e ON e.lidnr = a.official03';
            $sql = $sql . ' left JOIN leden f ON f.lidnr = a.official04';
            $sql = $sql . ' left JOIN leden g ON g.lidnr = a.official05';
            $sql = $sql . ' left JOIN leden h ON h.lidnr = a.official06';
            $sql = $sql . ' left JOIN leden i ON i.lidnr = a.official07';
            $sql = $sql . ' left JOIN leden j ON j.lidnr = a.official08';
            $sql = $sql . ' where STR_TO_DATE(datum, "%d-%m-%Y") >= DATE_SUB(CURDATE(), INTERVAL 1 DAY)';
            $sql .= " and naam03<>''";
            $sql = $sql . ' order by dt_datum, tijd ASC';
//echo $sql;
//die;

            /*
              $sql='select *, STR_TO_DATE(datum, "%d-%m-%Y") as dt_datum from wedstrijden';
              $sql=$sql . ' where STR_TO_DATE(datum, "%d-%m-%Y") >= DATE_SUB(CURDATE(), INTERVAL 1 DAY)';
              $sql .= " and naam03<>''";
              $sql=$sql . ' order by dt_datum, tijd ASC';
             */
        } else {
            $sql = 'select *, STR_TO_DATE(datum, "%d-%m-%Y") as dt_datum from wedstrijden where poule = "' . $poule . '"';
            $sql = $sql . " order by dt_datum,tijd ASC";
        }
//echo "sql = " . "$sql";
        $results = $wpdb->get_results($sql);
        if (!empty($results)) {
            ?>
            <table class=<?php echo $cl_option; ?>wd-pretty-table>
                <thead class='wedstrijdlijst_header'>
                <th><?php _e('datum', 'wedstrijden') ?></th>
                <th><?php _e('tijd', 'wedstrijden') ?></th>
                <th><?php _e('thuis', 'wedstrijden') ?></th>
                <th><?php _e('uit', 'wedstrijden') ?></th>
                <th><?php _e('tafelaars', 'wedstrijden') ?></th>
                </thead>
                <tbody>
                    <?php
                    $class = '';
                    foreach ($results as $result) {
                        $class = ($class == 'alt') ? "" : 'alt';
                        ?>
                        <tr class="<?php echo $class; ?>">
                            <td scope="row"><?php echo $result->datum; ?></td>
                            <td scope="row"><?php echo substr($result->tijd, 0, 2) . ":" . substr($result->tijd, 3, 2); ?></td>
                            <td scope="row"><?php echo $result->thuis; ?></td>
                            <td scope="row"><?php echo $result->uit; ?></td>
                            <?php
                            $enaam = naam_opmaak($result->evn, $result->etv, $result->enm);
                            $fnaam = naam_opmaak($result->fvn, $result->ftv, $result->fnm);
                            $gnaam = naam_opmaak($result->gvn, $result->gtv, $result->gnm);
                            ?>
                            <td scope="row"><?php echo get_href_ical($result->datum, $result->tijd, $result->accommodatie . "," . $result->adres . "," . $result->plaats, "Tafelaars " . $result->poule . "-" . $result->code . " " . $result->thuis . "-" . $result->uit, $enaam . "<br />" . $fnaam . "<br />" . $gnaam); ?></td>

                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
            <?php
        }
    }

    function get_bardienst($poule, $cl_option) {
//echo "bardienst";
        global $wpdb;
        $poule = trim(htmlspecialchars(mysql_real_escape_string($poule)));
//echo "<br>poule=". $poule;
        if ($poule == "") {
            $sql = 'select *, ';
            $sql .= 'c.Naam as znaam, c.tussenvoegsel as ztussenvoegsel, c.voornaam as zvoornaam, ';
            $sql .= 'STR_TO_DATE(datum, "%d-%m-%Y") as dt_datum from wedstrijden a';
            $sql = $sql . ' INNER JOIN bardienst b ON a.poule = b.poule AND a.code = b.code';
            $sql = $sql . ' left JOIN leden c ON b.lidnummer = c.lidnr';
            $sql = $sql . ' where STR_TO_DATE(datum, "%d-%m-%Y") >= DATE_SUB(CURDATE(), INTERVAL 1 DAY)';
            $sql = $sql . ' and b.dienst < 8';
            $sql = $sql . ' order by dt_datum, tijd ASC';
        } else {
            $sql = 'select *, STR_TO_DATE(datum, "%d-%m-%Y") as dt_datum from wedstrijden a';
            $sql = $sql . ' INNER JOIN bardienst b ON a.poule = b.poule AND a.code = b.code';
            $sql = $sql . ' left JOIN leden c ON b.lidnummer = c.lidnr';
            $sql = $sql . ' where a.poule = "' . $poule . '"';
            $sql = $sql . ' and b.dienst < 8';
            $sql = $sql . " order by dt_datum,tijd ASC";
        }
//echo "sql = " . "$sql";

        $results = $wpdb->get_results($sql);
        if (!empty($results)) {
            ?>
            <table class=<?php echo $cl_option; ?>wd-pretty-table>
                <thead class='wedstrijdlijst_header'>
                <th><?php _e('datum', 'wedstrijden') ?></th>
                <th><?php _e('tijd', 'wedstrijden') ?></th>
                <th><?php _e('thuis', 'wedstrijden') ?></th>
                <th><?php _e('uit', 'wedstrijden') ?></th>
                <th><?php _e('bardienst ouder', 'wedstrijden') ?></th>
                </thead>
                <tbody>
                    <?php
                    $class = '';
                    foreach ($results as $result) {
                        $class = ($class == 'al t') ? "" : 'alt';
                        ?>
                        <tr class="<?php echo $class; ?>">
                            <td scope="row"><?php echo $result->datum; ?></td>
                            <td scope="row"><?php echo substr($result->tijd, 0, 2) . ":" . substr($result->tijd, 3, 2); ?></td>
                            <td scope="row"><?php echo $result->thuis; ?></td>
                            <td scope="row"><?php echo $result->uit; ?></td>
                            <?php $naam06 = naam_opmaak($result->zvoornaam, $result->ztussenvoegsel, $result->znaam); ?>
                            <td scope="row"><?php echo get_href_ical($result->datum, $result->tijd, $result->accommodatie . "," . $result->adres . "," . $result->plaats, "Bardienst " . $result->poule . "-" . $result->code . " " . $result->thuis . "-" . $result->uit, $naam06); ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
            <?php
        }
    }

    function get_chauffeurs($poule, $cl_option) {
        echo "chauffeurs";
    }

    function get_scheidsrechters($poule, $cl_option) {
//echo "scheidrechters";
        global $wpdb;
        $poule = trim(htmlspecialchars(mysql_real_escape_string($poule)));
        if ($poule == "") {
            $sql = 'select *, ';
            $sql .= 'e.naam as enm, e.tussenvoegsel as etv, e.voornaam as evn, ';
            $sql .= 'f.naam as fnm, f.tussenvoegsel as ftv, f.voornaam as fvn, ';
            $sql .= 'STR_TO_DATE(datum, "%d-%m-%Y") as dt_datum from wedstrijden a';
            $sql = $sql . ' left JOIN leden e ON e.lidnr = a.official01';
            $sql = $sql . ' left JOIN leden f ON f.lidnr = a.official02';
            $sql = $sql . ' where STR_TO_DATE(datum, "%d-%m-%Y") >= DATE_SUB(CURDATE(), INTERVAL 1 DAY)';
            $sql .= " and naam01<>''";
            $sql = $sql . ' order by dt_datum, tijd ASC';

            /*
              $sql='select *, STR_TO_DATE(datum, "%d-%m-%Y") as dt_datum from wedstrijden';
              $sql=$sql . ' where STR_TO_DATE(datum, "%d-%m-%Y") >= DATE_SUB(CURDATE(), INTERVAL 1 DAY)';
              $sql .= " and naam01<>''";
              $sql=$sql . ' order by dt_datum, tijd ASC';
             */
        } else {
            $sql = 'select *, STR_TO_DATE(datum, "%d-%m-%Y") as dt_datum from wedstrijden where poule = "' . $poule . '"';
            $sql = $sql . " order by dt_datum,tijd ASC";
        }
//echo "sql = " . "$sql";
        $results = $wpdb->get_results($sql);
        if (!empty($results)) {
            ?>
            <table class=<?php echo $cl_option; ?>wd-pretty-table>
                <thead class='wedstrijdlijst_header'>
                <th><?php _e('datum', 'wedstrijden') ?></th>
                <th><?php _e('tijd', 'wedstrijden') ?></th>
                <th><?php _e('thuis', 'wedstrijden') ?></th>
                <th><?php _e('uit', 'wedstrijden') ?></th>
                <th><?php _e('scheidsrechters', 'wedstrijden') ?></th>

                </thead>
                <tbody>
                    <?php
                    $class = '';
                    foreach ($results as $result) {
                        $class = ($class == 'alt') ? "" : 'alt';
                        ?>
                        <tr class="<?php echo $class; ?>">
                            <td scope="row"><?php echo $result->datum; ?></td>
                            <td scope="row"><?php echo substr($result->tijd, 0, 2) . ":" . substr($result->tijd, 3, 2); ?></td>
                            <td scope="row"><?php echo $result->thuis; ?></td>
                            <td scope="row"><?php echo $result->uit; ?></td>
                            <?php
                            $enaam = naam_opmaak($result->evn, $result->etv, $result->enm);
                            $fnaam = naam_opmaak($result->fvn, $result->ftv, $result->fnm);
                            ?>
                            <td scope="row"><?php echo get_href_ical($result->datum, $result->tijd, $result->accommodatie . "," . $result->adres . "," . $result->plaats, "Scheidsrechters " . $result->poule . "-" . $result->code . " " . $result->thuis . "-" . $result->uit, $enaam . "<br />" . $fnaam); ?></td>

                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
            <?php
        }
    }

    function get_all($team, $cl_option) {
//echo "all";
        global $wpdb;
        $team = trim(htmlspecialchars(mysql_real_escape_string($team)));
//$sql='select *, STR_TO_DATE(datum, "%d-%m-%Y") as dt_datum from wedstrijden where team = "'. $team .'"';
//$sql=$sql . " order by dt_datum,tijd ASC";

        $sql = 'select *, STR_TO_DATE(datum, "%d-%m-%Y") as dt_datum, b.CMP_ID as cmp_id, b.PLG_ID as plg_id, ';
        $sql .= 'z.Naam as znaam, z.tussenvoegsel as ztussenvoegsel, z.voornaam as zvoornaam, ';
        $sql .= 'y.Naam as ynaam, y.tussenvoegsel as ytussenvoegsel, y.voornaam as yvoornaam, ';
        $sql .= 'w.Naam as wnaam, w.tussenvoegsel as wtussenvoegsel, w.voornaam as wvoornaam, ';
        $sql .= 'x.Naam as xnaam, x.tussenvoegsel as xtussenvoegsel, x.voornaam as xvoornaam ';
        $sql .= ' from wedstrijden a';
        $sql = $sql . ' INNER JOIN teams b ON a.poule = b.poulid';
        $sql = $sql . ' LEFT JOIN bardienst c ON a.poule = c.poule and a.code = c.code';
        $sql = $sql . ' LEFT JOIN leden z ON c.lidnummer = z.lidnr';
        $sql = $sql . ' LEFT JOIN chauffeur d ON a.poule = d.poule and a.code = d.code';
        $sql = $sql . ' LEFT JOIN leden y ON d.lidnummer1 = y.lidnr';
        $sql = $sql . ' LEFT JOIN leden w ON d.lidnummer2 = w.lidnr';
        $sql = $sql . ' LEFT JOIN leden x ON d.lidnummer3 = x.lidnr';
//$sql=$sql . ' where STR_TO_DATE(datum, "%d-%m-%Y") >= DATE_SUB(CURDATE(), INTERVAL 1 DAY)';
        $sql = $sql . ' where b.code = "' . $team . '"';
        $sql = $sql . ' and (c.dienst < 8 or isnull(c.dienst))';
        $sql = $sql . ' and (d.dienst < 8 or isnull(d.dienst))';
        $sql = $sql . ' order by dt_datum, tijd ASC';


//echo "sql = <br>" . "$sql";

        $results = $wpdb->get_results($sql);
        if (!empty($results)) {
            ?>
            <table class=<?php echo $cl_option; ?>wd-pretty-table>
                <thead>
                <th class="wedstrijdlijst_header"><?php _e('datum', 'wedstrijden') ?></th>
                <th class="wedstrijdlijst_header"><?php _e('tijd', 'wedstrijden') ?></th>
                <th class="wedstrijdlijst_header"><?php _e('thuis', 'wedstrijden') ?></th>
                <th class="wedstrijdlijst_header"><?php _e('uit', 'wedstrijden') ?></th>
                <th class="wedstrijdlijst_header"><?php _e('uitslag', 'wedstrijden') ?></th>
                <th class="wedstrijdlijst_header"><?php _e('scheidsrechters', 'wedstrijden') ?></th>
                <th class="wedstrijdlijst_header"><?php _e('tafelaars', 'wedstrijden') ?></th>
                <th class="wedstrijdlijst_header"><?php _e('bardienst', 'wedstrijden') ?></th>
                <th class="wedstrijdlijst_header"><?php _e('chauffeurs', 'wedstrijden') ?></th>

                </thead>
                <tbody>
                    <?php
                    $class = '';
                    foreach ($results as $result) {
                        $hlp_cmp_id = $result->cmp_id;
                        $hlp_plg_id = $result->plg_i_d;
                        $class = ($class == 'alt') ? "" : 'alt';
                        ?>
                        <tr class="<?php echo $class; ?>">
                            <td scope="row"><?php echo get_href_ical($result->datum, $result->tijd, $result->accommodatie . "," . $result->adres . "," . $result->plaats, "Wedstrijd " . $result->poule . "-" . $result->code . " " . $result->thuis . "-" . $result->uit, $result->datum); ?></td>
                            <td scope="row"><?php echo get_href_ical($result->datum, $result->tijd, $result->accommodatie . "," . $result->adres . "," . $result->plaats, "Wedstrijd " . $result->poule . "-" . $result->code . " " . $result->thuis . "-" . $result->uit, tijd_opmaak($result->tijd)); ?></td>
                            <td scope="row"><?php echo get_href_route($result->accommodatie, $result->adres, $result->postcode, $result->plaats, $result->thuis, "_blank"); ?></td>
                            <td scope="row"><?php echo $result->uit; ?></td>
                            <td scope="row"><?php echo $result->uitslag; ?></td>
                            <td scope="row"><?php echo get_href_ical($result->datum, $result->tijd, $result->accommodatie . "," . $result->adres . "," . $result->plaats, "Scheidsrechters " . $result->poule . "-" . $result->code . " " . $result->thuis . "-" . $result->uit, $result->naam01 . "<br />" . $result->naam02); ?></td>
                            <td scope="row"><?php echo get_href_ical($result->datum, $result->tijd, $result->accommodatie . "," . $result->adres . "," . $result->plaats, "Tafelaars " . $result->poule . "-" . $result->code . " " . $result->thuis . "-" . $result->uit, $result->naam03 . "<br />" . $result->naam04 . "<br />" . $result->naam05); ?></td>
                            <?php $naam06 = naam_opmaak($result->zvoornaam, $result->ztussenvoegsel, $result->znaam); ?>
                            <td scope="row"><?php echo get_href_ical($result->datum, $result->tijd, $result->accommodatie . "," . $result->adres . "," . $result->plaats, "Bardienst " . $result->poule . "-" . $result->code . " " . $result->thuis . "-" . $result->uit, $naam06); ?></td>
                            <?php
                            $naam07 = naam_opmaak($result->yvoornaam, $result->ytussenvoegsel, $result->ynaam);
                            $naam07 = $naam07 . ', ' . naam_opmaak($result->wvoornaam, $result->wtussenvoegsel, $result->wnaam);
                            $naam07 = $naam07 . ', ' . naam_opmaak($result->xvoornaam, $result->xtussenvoegsel, $result->xnaam);
                            ?>
                            <td scope="row"><?php echo get_href_ical($result->datum, $result->tijd, $result->accommodatie . "," . $result->adres . "," . $result->plaats, "Chauffeur " . $result->poule . "-" . $result->code . " " . $result->thuis . "-" . $result->uit, $naam07); ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
            <a href="http://db.basketball.nl/db/wedstrijd/ical.pl?cmp_ID=<?php echo $hlp_cmp_id; ?>&plg_ID=<?php echo $hlp_plg_id; ?>">Wedstrijden voor in de agenda</a>
            <?php
        }
    }

    function get_training($team, $cl_option) {
//echo "training";
        global $wpdb;
        $team = trim(htmlspecialchars(mysql_real_escape_string($team)));
        $sql = 'select * from teams where code = "' . $team . '"';

//echo "sql = " . "$sql";
        $results = $wpdb->get_results($sql);
        if (!empty($results)) {
            ?>
            <table class=<?php echo $cl_option; ?>wd-pretty-table>
                <thead class='wedstrijdlijst_header'>
                <th><?php _e('training', 'wedstrijden') ?></th>
                <th><?php _e('trainer', 'wedstrijden') ?></th>
                <th><?php _e('coach', 'wedstrijden') ?></th>

                </thead>
                <tbody>
                    <?php
                    $class = '';
                    foreach ($results as $result) {
                        $class = ($class == 'alt') ? "" : 'alt';
                        ?>
                        <tr class="<?php echo $class; ?>">
                            <td scope="row"><?php echo $result->TRAINING; ?></td>
                            <td scope="row"><?php echo $result->TRAINER; ?></td>
                            <td scope="row"><?php echo $result->COACHE; ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
            <?php
        }
    }

    function get_leden($team, $cl_option) {
     	//error_reporting(E_ALL);
	
        //echo "Teamleden";
        global $wpdb;
        $team = trim(htmlspecialchars(mysql_real_escape_string($team)));
        $sql = 'select *, r1.awgr as ref, r2.awgr as tafel, b.naam as bname, tussenvoegsel, voornaam from teamindeling a inner join leden b ';
        $sql .= 'on a.lidnummer = b.lidnr ';
        $sql .= ' join referee as r1 on a.lidnummer = r1.lidnr and r1.categorie = "R" ';
        $sql .= ' join referee as r2 on a.lidnummer = r2.lidnr and r2.categorie = "T" ';

        $sql .= 'where huidig_team = "' . $team . '"';
        $sql .= 'and lidsoort <> "NS"';

		/*$sql = 'select *, b.naam as bname, tussenvoegsel, voornaam from teamindeling a inner join leden b ';
        $sql .= 'on a.lidnummer = b.lidnr ';
        
        $sql .= 'where huidig_team = "' . $team . '"';
        $sql .= 'and lidsoort <> "NS"';*/
		//echo "sql = " . "$sql";
        $results = $wpdb->get_results($sql); 
		//print_r($results);
        if (!empty($results)) {
		//die;
            ?>
            <table class=<?php echo $cl_option; ?>wd-pretty-table>
                <thead class='wedstrijdlijst_header'>
                <th><?php _e('lidnr', 'wedstrijden') ?></th>
                <th><?php _e('naam', 'wedstrijden') ?></th>

                </thead>
                <tbody>
                    <?php
                    $class = '';
                    foreach ($results as $result) {
                        //$class=($class=='alt')?"":'alt';
                        $class = 'alt';
                        ?>
                        <tr class= "<?php echo $class; ?>">
                            <td scope="row">
                                <a href="../temp/<?php echo $result->lidnummer; ?>.ics">
                                    <?php echo $result->lidnummer; ?>
                                </a>
                            </td>
                            <td scope="row" title="<?php echo $result->ref . $result->tafel; ?>"><?php echo naam_opmaak($result->voornaam, $result->tussenvoegsel, $result->bname); ?></td>

                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
            <?php
        }//die;
    }

    function get_not_reg_leden($team, $cl_option) {
//echo "Teamleden";
        global $wpdb;
        $team = trim(htmlspecialchars(mysql_real_escape_string($team)));
//SELECT * FROM `systab` where syscode = 'team' and key_value = "X0 1"
        $sql = 'select * from systab';

        $sql .= ' where syscode = "team"';
        $sql .= ' and key_value = "' . $team . '"';

//echo "sql = " . "$sql";
        $results = $wpdb->get_results($sql);
        if (!empty($results)) {
            ?>
            <table class=<?php echo $cl_option; ?>wd-pretty-table>
                <tbody>
                    <?php
                    $class = '';
                    foreach ($results as $result) {
                        //$class=($class=='alt')?"":'alt';
                        $class = 'alt';
                        ?>
                        <tr  class="<?php echo $class; ?>">
                            <td scope="row">

                                <?php echo ""; ?>
                                </a>
                            </td>
                            <td scope="row" title=""><?php echo $result->value; ?></td>

                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
            <?php
        }
    }

    function get_bankers($team, $cl_option) {
        echo "Bankzitters";
        global $wpdb;
        $team = trim(htmlspecialchars(mysql_real_escape_string($team)));
        $sql = 'select *, b.naam as bname, tussenvoegsel, voornaam from teamindeling a inner join leden b ';
        $sql .= 'on a.lidnummer = b.lidnr ';
        $sql .= 'where bank_team = "' . $team . '"';

//echo "sql = " . "$sql";
        $results = $wpdb->get_results($sql);
        if (!empty($results)) {
            ?>
            <table class=<?php echo $cl_option; ?>wd-pretty-table>
                <thead>
                <th><?php _e('lidnr', 'wedstrijden') ?></th>
                <th><?php _e('naam', 'wedstrijden') ?></th>

                </thead>
                <tbody>
                    <?php
                    $class = '';
                    foreach ($results as $result) {
                        //$class=($class=='alt')?"":'alt';
                        $class = 'alt'
                        ?>
                        <tr class="<?php echo $class; ?>">
                            <td scope="row"><?php echo $result->lidnummer; ?></td>
                            <td scope="row"><?php echo naam_opmaak($result->voornaam, $result->tussenvoegsel, $result->bname); ?></td>

                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
            <?php
        }
    }

    function get_stats($team, $cl_option) {
//echo "stats";

        global $wpdb;
        $team = trim(htmlspecialchars(mysql_real_escape_string($team)));
        $sql = 'select * from teams where code = "' . $team . '"';


//echo "sql = " . "$sql";
        $results = $wpdb->get_results($sql);
        if (!empty($results)) {
            foreach ($results as $result) {
                $hUrl = "http://db.basketball.nl/db/wedstrijd/stand.pl?szn_Naam=";
                $hUrl .= $result->SEIZOEN;
                $hUrl .= "&amp;amp;&amp ;title=off&amp;amp;&amp;menubalk=off&amp;amp;&amp;link=on";
                $hUrl .= "&amp;amp;&amp;cmp_ID=" . $result->CMP_ID;
                $hUrl .= "&CSS=www.forwodians.nl/wp-content/plugins/wedstrijd/nbb.css";
//echo "hUrl = " . "$hUrl";
//$hUrl.="&amp;amp;&amp;"
            }
            ?>
            <iframe	src="<?php echo $hUrl; ?>" height="800" width="100%" frameborder="0"></iframe>
            <?php
        }
    }

    function widget_display_wedstrijden($args) {
        extract($args, EXTR_SKIP);
//echo $args[0];
        echo $before_widget;
//add_action("EC ","display_wedstrijd");
        display_wedstrijd();
        echo $after_widget;
    }

    function widget_display_wedstrijden_init() {
        wp_register_sidebar_widget(WEDSTRIJDEN_WIDGET_ID, __('Wedstrijden'), 'widget_display_wedstrijden');
        wp_register_widget_control(WEDSTRIJDEN_WIDGET_ID, __('Wedstrijden'), 'widget_display_wedstrijden_control');
    }

// Register widget to WordPress
    add_action("plugins_loaded", "widget_display_wedstrijden_init");

// in de admin room
    function widget_display_wedstrijden_control() {
        $options = get_option(WEDSTRIJDEN_WIDGET_ID);
        if (!is_array($options)) {
            $options = array();
        }

        $widget_data = $_POST[WEDSTRIJDEN_WIDGET_ID];
        if ($widget_data['submit']) {
            $options['team'] = $widget_data['team'];

            update_option(WEDSTRIJDEN_WIDGET_ID . "[team]", $options);
        }

// Render form
        $team = $options['team'];
//$coming_up_text = $options['coming_up_text'];
//$show_excerpt = $options['show_excerpt'];
// The HTML form will go here
        ?>
        <label for="<?php echo WEDSTRIJDEN_WIDGET_ID; ?>-poule">
            poule to show:
        </label>
        <input class="widefat"
               type="text"
               name="<?php echo WEDSTRIJDEN_WIDGET_ID; ?>[poule]"
               id="<?php echo WEDSTRIJDEN_WIDGET_ID; ?>-poule"
               value="<?php echo $poule; ?>"/>

        <input type="hidden"
               name="<?php echo WEDSTRIJDEN_WIDGET_ID; ?>[submit]"
               value="1"/>
               <?php
           }

// register the shortpoule: [poule="2012"  voor de HS 1]
// eerste element is de short-code zoals deze in page of post gebruikt wordt
// tweede element is de functie die aangeroepen wordt
           add_shortcode('wedstrijd', 'sh_get_wedstrijd');

           function sh_get_wedstrijd($attr) {
               if (isset($attr['team'])) {
                   $options['team'] = $attr['team'];
               }
               if (isset($attr['mode'])) {
                   $mode = $attr['mode'];
               }
               if (empty($mode)) {
                   $mode = $_GET['mode'];
               }
               toon_gegevens($options['team'], 'page_', $mode);
           }

           function bardienst_leden() {
//include("reg_form.php");
               ?>
        <form id="frmRegistration" name="frmRegistration" action="<?php echo str_replace('%7E', '~', $_SERVER['REQUEST_URI']); ?>" method="POST">
            <table style="align:center; width:500px; margin: 0 0 0 0;">
                <tr>
                    <td>Lidnummer </td>
                    <td><input type="text" name="lidnummer" id="lidnummer" maxlength="10" value= ><b><font color="red">*</font></b></td>
                </tr>
                <tr>
                    <td>Place </td>
                    <td><input type="text" name="place" id="place" maxlength="50" ><b><font color="red">*</font></b></td>
                </tr>
                <tr>
                    <td>Email </td>
                    <td><input type="text" name="email" id="email" maxlength="50" ><b><font color="red">*</font></b></td>
                </tr>
                <tr>
                    <td>Mobile No</td>
                    <td><input type="text" name="mobile_no" id="mobile_no" maxlength="50"><b><font color="red">*</font></b></td>
                </tr>
                <tr>
                    <td>About Yourself</td>
                    <td><textarea rows="4" cols="50" name="about" id="about" >

                        </textarea> <b><font color="red">*</font></b></td>
                </tr>
                <tr>
                    <td><input type="submit" name="btnSubmit" id="btnSubmit" value="Submit Details" style="color:blue;"></td>
                    <td align="right"><b><font color="red">*</font></b> fileds are mandatory</td></tr>
            </table>
        </form>

        <?PHP
        global $wpdb;
        $table = $wpdb->prefix . "reg_details";
        if (isset($_POST['btnSubmit'])) {
// process $_POST data here
            $name = $_POST['name'];
            $place = $_POST['place'];
            $email = $_POST['email'];

            $mobile_no = $_POST['mobile_no'];
            $about_yourself = $_POST['about'];
            echo $name . " " . $place . " " . $email . " " . $mobile_no . " " . $about_yourself;
//$wpdb->query("INSERT INTO $table(name, place,email,mobno,about_me,date_apply)
//    VALUES('$name', '$place',' $email', '$mobile_no', '$about_yourself',now())");
        }
    }
    ?>
