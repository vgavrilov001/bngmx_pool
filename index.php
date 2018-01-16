<?php
require_once ("header.php");
include ("classCity.php");
?>

<div id="mapMonitor">
    <div id="mapMonitorAjax">

<div id="status">
<?php
$run_file = file("data/const/run.txt");
    foreach ($run_file as &$v_run) {
        echo "<p> &nbsp;&nbsp;", $v_run, " &nbsp;&nbsp;</p>";
    } ?>
</div>

<div id="right-col">
    <table>
	<tr><td><font color=#00cd00>Name</font></td><td><font color=#00cd00>Total Usage</font></td><td><font color=#00cd00>Max Usage</font></td><td><font color=#00cd00>Free &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></td><td><font color=#00cd00>In Use</font></td></tr>
	<?php
	foreach($monitoringBNGMX->cityBras_1 as $key=>$val) {
		echo '<tr><td width=120>', $monitoringBNGMX->cityBras_1[$key], '</td><td>', $monitoringBNGMX->cityTot_1[$key], '</td><td>', $monitoringBNGMX->cityMax_1[$key], '</td><td>', $monitoringBNGMX->cityMax_1[$key]-$monitoringBNGMX->cityTot_1[$key], '</td><td>', $monitoringBNGMX->cityVal_1[$key], ' %</td></tr>';
		echo '<tr><td>', $monitoringBNGMX->cityBras_1_2[$key], '</td><td>', $monitoringBNGMX->cityTot_1_2[$key], '</td><td>', $monitoringBNGMX->cityMax_1_2[$key], '</td><td>', $monitoringBNGMX->cityMax_1_2[$key]-$monitoringBNGMX->cityTot_1_2[$key], '</td><td>', $monitoringBNGMX->cityVal_1_2[$key], ' %</td></tr>';
		} ?>
        <?php echo '<tr><td width=120>', $monitoringBNGMX->cityBras_1_3['16'], '</td><td>', $monitoringBNGMX->cityTot_1_3['16'], '</td><td>', $monitoringBNGMX->cityMax_1_3['16'], '</td><td>', $monitoringBNGMX->cityMax_1_3['16']-$monitoringBNGMX->cityTot_1_3['16'], '</td><td>', $monitoringBNGMX->cityVal_1_3['16'], ' %</td></tr>'; ?>
        <?php echo '<tr><td width=120>', $monitoringBNGMX->cityBras_1_4['16'], '</td><td>', $monitoringBNGMX->cityTot_1_4['16'], '</td><td>', $monitoringBNGMX->cityMax_1_4['16'], '</td><td>', $monitoringBNGMX->cityMax_1_4['16']-$monitoringBNGMX->cityTot_1_4['16'], '</td><td>', $monitoringBNGMX->cityVal_1_4['16'], ' %</td></tr>'; ?>

    </table>
</div>


<?php
foreach($monitoringBNGMX->cityBras_1 as $key=>$val) {
    ?>

	<div class="city" city="<?php echo $val;?>"
			city_rus="<?php echo $monitoringBNGMX->cityRus[$key];?>"
			city_1="<?php echo $monitoringBNGMX->cityBras_1[$key];?>"
			city_2="<?php echo $monitoringBNGMX->cityBras_1_2[$key];?>"
			city_3="<?php echo $monitoringBNGMX->cityBras_1_3[$key];?>"
			city_4="<?php echo $monitoringBNGMX->cityBras_1_4[$key];?>"
			style="<?php echo $monitoringBNGMX->koordinati[$key];?>"
			title="<?php
				echo $monitoringBNGMX->cityBras_1[$key], '&nbsp;&nbsp;', $monitoringBNGMX->cityVal_1[$key], '&nbsp;&nbsp;', '(',$monitoringBNGMX->cityTot_1[$key], ')', '&#013;';
				echo $monitoringBNGMX->cityBras_1_2[$key], '&nbsp;&nbsp;', $monitoringBNGMX->cityVal_1_2[$key], '&nbsp;&nbsp;', '(',$monitoringBNGMX->cityTot_1_2[$key], ')', '&#013;';
				echo $monitoringBNGMX->cityBras_1_3[$key], '&nbsp;&nbsp;', $monitoringBNGMX->cityVal_1_3[$key], '&nbsp;&nbsp;', $monitoringBNGMX->cityTot_1_3[$key], '&#013;';
				echo $monitoringBNGMX->cityBras_1_4[$key], '&nbsp;&nbsp;', $monitoringBNGMX->cityVal_1_4[$key], '&nbsp;&nbsp;', $monitoringBNGMX->cityTot_1_4[$key];
			?>">

	<div class="name"><?php echo $monitoringBNGMX->cityRus[$key];?></div>
	<div class="icoCity <?php if($monitoringBNGMX->cityVal_1[$key]>='98' || $monitoringBNGMX->cityVal_1_2[$key]>='98' || $monitoringBNGMX->cityVal_1_3[$key]>='98' || $monitoringBNGMX->cityVal_1_4[$key]>='98') {echo critical;} else
				  if($monitoringBNGMX->cityVal_1[$key]>='96' || $monitoringBNGMX->cityVal_1_2[$key]>='96' || $monitoringBNGMX->cityVal_1_3[$key]>='96' || $monitoringBNGMX->cityVal_1_4[$key]>='96') {echo warning;}
				  ?>"></div>
	</div>
    <?php

}

?>

    </div>
</div>

<div id="pelena">
	
	<div class="closePelena"></div>
	<div id="brasDivMain">

		<div class="list">
		    <div id="loadListAjax">
			<center><?php echo $_POST['city_rus']; ?>:</center><br>

			<p><font color=#00ff00><?php echo $_POST['city_1']; ?></font></p><br>
			<?php
			    $conf = file("data/set_conf/".$_POST['city_1'].".conf");
			    foreach ($conf as &$value) {
				echo $value, "<br>";
			    }
			?>

			<br><p><font color=#00ff00><?php echo $_POST['city_2']; ?></font></p><br>
			<?php
			    $conf = file("data/set_conf/".$_POST['city_2'].".conf");
			    foreach ($conf as &$value) {
				echo $value, "<br>";
			    }
			?>

			<br><p><font color=#00ff00><?php echo $_POST['city_3']; ?></font></p><br>
			<?php
			    $conf = file("data/set_conf/".$_POST['city_3'].".conf");
			    foreach ($conf as &$value) {
				echo $value, "<br>";
			    }
			?>

			<br><p><font color=#00ff00><?php echo $_POST['city_4']; ?></font></p><br>
			<?php
			    $conf = file("data/set_conf/".$_POST['city_4'].".conf");
			    foreach ($conf as &$value) {
				echo $value, "<br>";
			    }
			?>

			<?php
#			var_dump($_POST);
			?>
		    </div>
		</div>

	</div>

	<div style="clear:both;"></div>

</div>






<?php
require_once ("footer.php");
?>
