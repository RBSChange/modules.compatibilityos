<!-- Section -->
<fieldset>
<legend><?php echo($section->getLabelAsHtml()); ?></legend>
<?php if ($section->getDescription()): ?>
<div class="description"><?php echo($section->getDescriptionAsHtml()); ?></div>
<?php endif; ?>
<ul>
	<?php foreach ($children as $child): ?>
	<li<?php echo($sms->getBeanZoneAttributes($child)); ?>>
		<?php if ($conditionMessage = $sms->getConditionMessage($child)) : ?>		
		<p class="nojs"><?php echo($conditionMessage) ?></p>
		<?php endif; ?>
		<?php $child->getDocumentService()->echoBeanTemplateContent($child, $questions); ?>
	</li>
	<?php endforeach; ?>
</ul>
</fieldset>