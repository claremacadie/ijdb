<h3>
	<p>Welcome, <?php echo($message); ?> to Clare's database project</p>
</h3>

<?php if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == TRUE && $lockedDown): ?>
<!-- Admin post result section -->
<p>Hello Admin!</p>
<?php endif; ?>

<?php foreach($arrPredictions as $result): ?>

	<?php if($j==0): ?>
		<div class="row">
	<?php endif; ?>

	<?php $j++; $i++; ?>
	<input
		type="checkbox"
		value="<?php htmlout($i - 1); ?>"
		<?php
		  if (isset($_SESSION['loggedIn']) &&
			  $_SESSION['loggedIn'] == TRUE &&
			  isset($_SESSION['displayName']) &&
			  $_SESSION['displayName'] == $result['DisplayName'])
			{ echo('checked'); }?>>
	<?php htmlout($result['DisplayName']); ?>

	<?php if($j==4): ?>
		<?php $j=0; ?>
		</div>
	<?php endif; ?>

<?php endforeach; ?>
