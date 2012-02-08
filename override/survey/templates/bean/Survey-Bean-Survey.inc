<!-- Survey -->
<?php if ($stepIndex == 0): ?>
<div class="survey-header">
<h2 change:h="" id="survey_survey_<?php echo($survey->getId()); ?>"><?php echo($survey->getLabelAsHtml()); ?></h2>
<div class="description"><?php echo($survey->getDescriptionAsHtml()); ?></div>
</div>
<?php else: ?>
<h2 change:h="" id="survey_survey_<?php echo($survey->getId()); ?>"><?php echo($survey->getLabelAsHtml()); ?></h2>
<?php endif; ?>
<form change:form="beanName surveyResponse" showErrors="true" id="<?php echo($survey->getId()); ?>" class="change-form">
	<div change:include="module 'survey'; template 'Survey-Inc-NavigationTop'; type 'html'; stepIndex '<?php echo($stepIndex); ?>' ; lastStepIndex '<?php echo($lastStepIndex); ?>'"></div>
	<?php $step->getDocumentService()->echoBeanTemplateContent($step, $questionsByStep, $stepIndex); ?>
	<div change:include="module 'survey'; template 'Survey-Inc-NavigationBottom'; type 'html'; stepIndex '<?php echo($stepIndex); ?>'  ; lastStepIndex '<?php echo($lastStepIndex); ?>'"></div>
</form>

<script type="text/javascript">
	jQuery('.nojs').hide();

	<?php foreach ($questionsByStep[$step->getId()] as $question): ?>
		<?php foreach ($question->getOnChangeInfoArray() as $infoArray): ?>
			<?php if ($question instanceof survey_persistentdocument_booleanquestion): ?>
			survey_conditionalQuestions.handleCheckboxBoolean('<?php echo($infoArray['zoneId']); ?>', '<?php echo($infoArray['conditionId']); ?>', '<?php echo($infoArray['value']); ?>');
			<?php elseif ($question instanceof survey_persistentdocument_listquestion && ($question->getMaxSelection() == 1)): ?>
			survey_conditionalQuestions.handleList('<?php echo($infoArray['zoneId']); ?>', '<?php echo($infoArray['conditionId']); ?>', '<?php echo($infoArray['value']); ?>');
			<?php elseif ($question instanceof survey_persistentdocument_listquestion): ?>
			survey_conditionalQuestions.handleCheckbox('<?php echo($infoArray['zoneId']); ?>', '<?php echo($infoArray['conditionId']); ?>', '<?php echo($infoArray['value']); ?>');
			<?php endif; ?>
		<?php endforeach; ?>
	<?php endforeach; ?>
</script>