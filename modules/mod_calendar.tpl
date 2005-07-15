{* $Header: /cvsroot/bitweaver/_bit_calendar/modules/mod_calendar.tpl,v 1.1 2005/07/15 12:25:01 bitweaver Exp $ *}

{php}
include_once( CALENDAR_PKG_PATH."Calendar.php");
global $dbTiki,$tikilib;
if(isset($_SESSION["thedate"])) {
  $day=date("d",$_SESSION["thedate"]);
  $mon=date("m",$_SESSION["thedate"]);
  $year=date("Y",$_SESSION["thedate"]);
} else {
	$day=date( "d", $tikilib->server_time_to_site_time( time() ) );
	$mon=date( "m", $tikilib->server_time_to_site_time( time() ) );
	$year=date( "Y", $tikilib->server_time_to_site_time( time() ) );
}
if(isset($_REQUEST["day"])) {
 $day = $_REQUEST["day"];
}

if(isset($_REQUEST["mon"])) {
 $mon = $_REQUEST["mon"];
}

if(isset($_REQUEST["year"])) {
 $year = $_REQUEST["year"];
} 

$thedate = mktime(23,59,59,$mon,$day,$year);
$_SESSION["thedate"] = $thedate;

// Calculate number of days in month
// The format is S M T W T F S
$c = new Calendar("en");
$v = mb_substr(tra($c->nameOfMonth($mon)),0,3);
$dayofweek = tra($c->dayOfWeekStr($day,$mon,$year));
if (false) { // to have the months collected by get_strings.php
	tra("January"); tra("February"); tra("March"); tra("April"); tra("May");tra("June"); tra("July"); tra("August"); tra("September"); tra("October"); tra("November"); tra("December" );
}

$parsed = parse_url($_SERVER["REQUEST_URI"]);
if (!isset($parsed["query"])) {
  $parsed["query"]='';
}
parse_str($parsed["query"],$query);
unset($query["day"]);
unset($query["mon"]);
unset($query["year"]);
$father=$parsed["path"];
if (count($query)>0) {
  $first=1;
  foreach ($query as $name => $val) {
    if ($first) {
      $first=false;
      $father.='?'.$name.'='.$val;
    } else {
      $father.='&amp;'.$name.'='.$val;
    }
  }
  $father.='&amp;';
} else {
  $father.='?';
}

if (!strstr($father,"?")) {
  $todaylink=$father."day=".date("d")."&amp;mon=".date("m")."&amp;year=".date("Y");
} else {
  $todaylink=$father."day=".date("d")."&amp;mon=".date("m")."&amp;year=".date("Y");
}
{/php}

{bitmodule title="$moduleTitle" name="tikicalendar"}
    <!-- THIS ROW DISPLAYS THE YEAR AND MONTH -->
    <div class="navigation">
{php}
        $mong=$mon-1;
        $url="$father"."day=$day&amp;mon=$mong&amp;year=$year";
        print( "<a href=\"".$url."\"> &laquo; </a>" );
        print( $v );
        $mong=$mon+1;
        $url="$father"."day=$day&amp;mon=$mong&amp;year=$year";
        print( "<a href=\"".$url."\"> &raquo; </a>" );
        print( "&nbsp;" );
        $mong=$year-1;
        $url="$father"."day=$day&amp;mon=$mon&amp;year=$mong";
        print( "<a href=\"".$url."\"> &laquo; </a>" );
        print( $year );
        $mong=$year+1;
        $url="$father"."day=$day&amp;mon=$mon&amp;year=$mong";
        print( "<a href=\"".$url."\"> &raquo; </a>" );
{/php}         
    </div>
{php}
    $mat = $c->getDisplayMatrix($day,$mon,$year);
    $pmat = $c->getPureMatrix($day,$mon,$year);
{/php}
      <table class="mother">
        <!-- DAYS OF THE WEEK -->
        <tr>
{php}
          for ($i=0;$i<7;$i++) {
            $dayW = tra($c->dayOfWeekStrFromNo($i+1));
            $dayp = mb_substr($dayW,0,1);
            print("<th>$dayp</th>");
          }
{/php}
        </tr>
        <!-- TRs WITH DAYS -->
{php}
          for ($i=0;$i<6;$i++) {
            print("<tr>");
            for ($j=0;$j<7;$j++) {
              $in = $i*7+$j;
              $pval = $pmat[$in];
              $val = $mat[$in];
              if (substr($val,0,1)=='+') {
                $val = substr($val,1,strlen($val)-1);
                $classval = "class=\"highlight\"";
              } else {
                $classval = "";
              }
              if ($val != "  ") {
                print( "<td>" );
                $url = $father."day=$pval&amp;mon=$mon&amp;year=$year";
                print( "<a $classval href=\"$url\">$val</a></td>");
              } else {
                print( "<td>&nbsp;</td>" );
              }
            }
            print("</tr>");
          }
{/php}
        </table>
        <div class="navigation">
{php}
         print( "<a href=\"".$todaylink."\">".tra("Today")."</a>" );
{/php}
        </div>
{/bitmodule}
