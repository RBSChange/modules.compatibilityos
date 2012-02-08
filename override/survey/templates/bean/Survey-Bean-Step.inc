<!-- Step -->
<h3 change:h=""><?php echo($stepIndex+1); ?>. <?php echo($step->getLabelAsHtml()); ?></h3>
<?php if ($step->getDescription()): ?>
<div class="description"><?php echo($step->getDescriptionAsHtml()); ?></div>
<?php endif; ?>
<ul class="sections">
	<?php foreach ($sections as $section): ?>
	<li<?php echo($sms->getBeanZoneAttributes($section)); ?>>
		<?php if ($conditionMessage = $sms->getConditionMessage($section)) : ?>		
		<p class="nojs"><?php echo($conditionMessage) ?></p>
		<?php endif; ?>
		<?php $section->getDocumentService()->echoBeanTemplateContent($section, $questions); ?>
	</li>
	<?php endforeach; ?>
</ul>