
<?php echo \View::instance()->render('header.php') ?>

<div id="main" class="columns-1 content" role="main">
<div class="col">

	<h1><?php echo Base::instance()->get('lang.WeeklyReviewTitle') ?></h2>

	<h2><?php echo Base::instance()->get('lang.GetClearSubtitle') ?></h2>

	<ol class="weekly-review">
		<li>
			<h3><?php echo Base::instance()->get('lang.CollectLoosePapers') ?></h3>
			<p><?php echo Base::instance()->get('lang.CollectLoosePapersDetails') ?></p>
		</li>
		<li>
			<h3><?php echo Base::instance()->get('lang.GetInZero') ?></p></h3>
			<p><?php echo Base::instance()->get('lang.GetInZeroDetails') ?></p></p>
			<ul>
				<li><?php echo $inboxItems ?> Inbox item(s) need processing</li>
			</ul>
		</li>
		<li>
			<h3><?php echo Base::instance()->get('lang.EmptyHead') ?></p></h3>
			<p><?php echo Base::instance()->get('lang.EmptyHeadDetails') ?></p></p>
		</li>
	</ol>

	<h2><?php echo Base::instance()->get('lang.GetCurrentSubtitle') ?></h2>

	<ol class="weekly-review">
		<li>
			<h3><?php echo Base::instance()->get('lang.ReviewActions') ?></p></h3>
			<p><?php echo Base::instance()->get('lang.ReviewActionsDetails') ?></p></p>
		</li>
		<li>
			<h3><?php echo Base::instance()->get('lang.ReviewPrevCalendar') ?></p></h3>
			<p><?php echo Base::instance()->get('lang.ReviewPrevCalendarDetails') ?></p></p>
		</li>
		<li>
			<h3><?php echo Base::instance()->get('lang.ReviewUpcomingCalendar') ?></p></h3>
			<p><?php echo Base::instance()->get('lang.ReviewUpcomingCalendarDetails') ?></p></p>
		</li>
		<li>
			<h3><?php echo Base::instance()->get('lang.ReviewWaitingFors') ?></p></h3>
			<p><?php echo Base::instance()->get('lang.ReviewWaitingForsDetails') ?></p></p>
		</li>
		<li>
			<h3><?php echo Base::instance()->get('lang.ReviewProjects') ?></p></h3>
			<p><?php echo Base::instance()->get('lang.ReviewProjectsDetails') ?></p></p>
			<ul>
				<li>3 Project(s) are missing outcomes</li>
				<li>5 Project(s) have no Next Action defined</li>
			</ul>
		</li>
		<li>
			<h3><?php echo Base::instance()->get('lang.ReviewChecklists') ?></p></h3>
			<p><?php echo Base::instance()->get('lang.ReviewChecklistsDetails') ?></p></p>
		</li>
	</ol>

	<h2><?php echo Base::instance()->get('lang.GetCreativeSubtitle') ?></h2>

	<ol class="weekly-review">
		<li>
			<h3><?php echo Base::instance()->get('lang.ReviewSomeday') ?></p></h3>
			<p><?php echo Base::instance()->get('lang.ReviewSomedayDetails') ?></p></p>
		</li>
		<li>
			<h3><?php echo Base::instance()->get('lang.BeCreative') ?></p></h3>
			<p><?php echo Base::instance()->get('lang.BeCreativeDetails') ?></p></p>
		</li>
	</ol>

</div>
</div>

<?php echo \View::instance()->render('footer.php') ?>
