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

<?php if ($this->countModules('top-left') || $this->countModules('top-right')) : ?>
	<!-- TOP BAR -->
	<div class="t3-top-bar">
		<div class="container">
			<div class="row">
				<div class="col-sm-6 col-md-6 top-left <?php $this->_c('top-left') ?>">
					<jdoc:include type="modules" name="<?php $this->_p('top-left') ?>" style="raw"/>
				</div>

				<div class="col-sm-6 col-md-6 top-right ">
					<div class="right-info <?php $this->_c('top-right') ?>">
						<jdoc:include type="modules" name="<?php $this->_p('top-right') ?>" style="raw" />
					</div>

					<?php if ($this->countModules('languageswitcherload')) : ?>
						<!-- LANGUAGE SWITCHER -->
						<div class="languageswitcherload">
							<jdoc:include type="modules" name="<?php $this->_p('languageswitcherload') ?>" style="raw" />
						</div>
						<!-- //LANGUAGE SWITCHER -->
					<?php endif ?>
				</div>
			</div>
		</div>
	</div>
	<!-- //TOP BAR -->
<?php endif ?>