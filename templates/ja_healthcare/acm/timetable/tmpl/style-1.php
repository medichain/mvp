<?php
/**
 * ------------------------------------------------------------------------
 * JA Healthcare Template
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2011 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - Copyrighted Commercial Software
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites:  http://www.joomlart.com -  http://www.joomlancers.com
 * This file may not be redistributed in whole or significant part.
 * ------------------------------------------------------------------------
*/
defined('_JEXEC') or die;
?>

<?php
$timelineStyle = $helper->get('timeline-style');
?>

<div class="acm-timeline">
	<h3 class="container acm-timeline-title text-center">
		<span><?php echo $helper->get('timeline-title'); ?></span>
		<p class="container-sm acm-timeline-dates"><?php echo $helper->get('timeline-intro'); ?></p>
	</h3>
	<div class="container timeline-table style-3">
		<div class="row row-header">
			<?php
			$colols_count = $helper->getCols('data');
			$features_count = $helper->getRows('data');
			if (!$colols_count || !$features_count) {
				$colols_count = $helper->count('timeline-col-name');
				$features_count = $helper->count('timeline-row-name');
			}
			?>
			<div class="col no-padding" style="width: <?php echo 2*100*(12 / ($colols_count + 2))/12; ?>%">
        <div class="col-header text-center">
          <h2><?php echo JText::_( 'TPL_DOCTOR' ); ?></h2>
          <p><span class="big-number"></span></p>
        </div>
      </div>
			<?php for ($i = 0; $i < $colols_count; $i++) : ?>
				<div
					class="col <?php if ($helper->get('data.timeline-col-featured', $i)): ?> col-featured <?php endif ?> no-padding" style="width: <?php echo 100*(12 / ($colols_count + 2))/12; ?>%">
					<div class="col-header text-center">
						<h2><?php echo $helper->get('data.timeline-col-name', $i) ?></h2>
						<p><span class="big-number"><?php echo $helper->get('data.timeline-col-des', $i) ?></span></p>
					</div>
				</div>
			<?php endfor; ?>
		</div>

		<div class="row row-body">
			<div class="col no-padding" style="width: <?php echo 2*100*(12 / ($colols_count + 2))/12; ?>%">
				<ul>
					<?php for ($row = 0; $row < $features_count; $row++) :
						$feature = $helper->getCell('data', $row, 0);
						if (!$feature) $feature = $helper->get('data.timeline-row-name', $row);
						$furl = '#';
						if (preg_match('/(link\=)/', $feature)) {
							preg_match('/(.*?)link\=/', $feature, $m1);
							preg_match('/link\=(.*?)$/', $feature, $m2);
							if (!empty($m1[1]))
								$feature = $m1[1];
							if (!empty($m2[1]))
								$furl = $m2[1];
						}
						?>
						<li class="row<?php echo($row % 2); ?> yes">
							<a href="<?php echo $furl; ?>">
								<?php echo $feature; ?>
							</a>
						</li>
					<?php endfor; ?>
				</ul>
			</div>

			<?php for ($col = 0; $col < $colols_count; $col++) : ?>
				
				<div class="col no-padding" style="width: <?php echo 100*(12 / ($colols_count + 2))/12; ?>%">
					<ul>
						<?php for ($row = 0; $row < $features_count; $row++) :
							$feature = $helper->getCell('data', $row, 0);
							$value = $helper->getCell('data', $row, $col + 1);
							$type = $value[0];
							if (!$feature) {
								$feature = $helper->get('timeline-row-name', $row);
								$tmp = $helper->get('timeline-row-supportfor', $row);
								$value = ($tmp & pow(2, $col)) ? 'b1' : 'b0'; // b1: yes, b0: no
								$type = 'b'; // boolean
							}
							?>
						<?php if ($type == 't'): ?>
							<li class="row<?php echo($row % 2); ?>"><?php echo substr($value, 1) ?></li>
						<?php elseif ($value == 'b1'): ?>
							<li class="row<?php echo($row % 2); ?>">
									<i class="fa fa-check-circle"></i>
							</li>
						<?php
						else: ?>
							<li class="row<?php echo($row % 2); ?> no"><i class="fa fa-times-circle"></i></li>
						<?php endif ?>

						<?php endfor ?>
					</ul>
				</div>
			<?php endfor ?>

		</div>
	</div>
</div>