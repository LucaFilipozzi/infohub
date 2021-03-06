<?php
	$this->Html->css('secondary', null, array('inline' => false));
	$this->Html->css('search', null, array('inline' => false));
?>
<script>
	$(document).ready(function() {
		$("#searchLink").addClass('active');
		resultsWidth();

		<?php
			if($submitErr){
				echo "alert('An error occured with your request. Please try again.');";
			}
		?>
	});

	$(window).resize(resultsWidth);

	function resultsWidth() {
		if ($(window).width() > 680) {
			$('.resultContent').css('width', '100%').css('width', '-=200px');
		}
		else {
			$('.resultContent').css('width', '95%').css('width', '-=60px');
		}
	}

	function validate(){
		var isValid = true;
		$('#request input, #request textarea').each(function() {
			if($(this).val()==''){
				isValid = false;
				$(this).focus();
				return false;
			}
		});
		if(!isValid) alert('All fields are requried.');
		return isValid;
	}

	function toggleDataNeeded(chk){
		var arrDataNeeded = $('#dataNeeded').val();
		var term = $(chk).val();
		if($(chk).prop('checked')){
			if(arrDataNeeded != '') arrDataNeeded += ', ';
			$('#dataNeeded').val(arrDataNeeded + term);
		}else{
			arrDataNeeded = arrDataNeeded.replace(', ' + term, '').replace(term, '');
			$('#dataNeeded').val(arrDataNeeded);
		}
	}
</script>

<!-- Background image div -->
<div id="searchBg" class="searchBg">
</div>

<!-- Request Form -->
<form action="/request/submit" method="post" id="request" onsubmit="return validate();">
	<div id="searchBody" class="innerLower">

		<div id="requestForm">
			<h2 class="headerTab">Request Form</h2>

			<div id="srLower" class="whiteBox">
				<h3 class="headerTab">Requester Information</h3>
				<div class="clear"></div>
				<div class="fieldGroup">
					<!-- <div class="infoCol"> -->
						<div class="field-continer">
							<label for="name">Requester Name</label>
							<input type="text" id="name" name="name" class="inputShade noPlaceHolder" value="<?= h($psName) ?>">
						</div>
						<div class="field-continer">
							<label for="phone">Requester Phone</label>
							<input type="text" id="phone" name="phone" class="inputShade noPlaceHolder" value="<?= h($psPhone) ?>">
						</div>
					<!-- </div>
					<div class="infoCol"> -->
						<div class="field-continer">
							<label for="role">Requester Role</label>
							<input type="text" id="role" name="role" class="inputShade noPlaceHolder" value="<?= h($psRole) ?>">
						</div>
						<div class="field-continer">
							<label for="email">Requester Email</label>
							<input type="text" id="email" name="email" class="inputShade noPlaceHolder" value="<?= h($psEmail) ?>">
						</div>
						<div class="field-continer">
							<label for="requestingOrganization">Requester Organization</label>
							<input type="text" id="requestingOrganization" name="requestingOrganization" class="inputShade noPlaceHolder" value="<?= h($psDepartment) ?>">
						</div>
						<input type="hidden" name="requesterPersonId" value="<?= h($psPersonID) ?>" />
					<!-- </div> -->
				</div>

				<h3 class="headerTab">Sponsor Information</h3>
				<div class="clear"></div>
				<div class="fieldGroup">
					<div class="field-continer">
						<label for="sponsorName">Sponsor Name</label>
						<input type="text" id="sponsorName" name="sponsorName" class="inputShade noPlaceHolder" value="<?= empty($supervisorInfo->name) ? '' : h($supervisorInfo->name) ?>">
					</div>
					<div class="field-continer">
						<label for="sponsorPhone">Sponsor Phone</label>
						<input type="text" id="sponsorPhone" name="sponsorPhone" class="inputShade noPlaceHolder" value="<?= empty($supervisorInfo->phone) ? '' : h($supervisorInfo->phone) ?>">
					</div>
					<div class="field-continer">
						<label for="sponsorRole">Sponsor Role</label>
						<input type="text" id="sponsorRole" name="sponsorRole" class="inputShade noPlaceHolder" value="<?= empty($supervisorInfo->job_title) ? '' : h($supervisorInfo->job_title) ?>">
					</div>
					<div class="field-continer">
						<label for="sponsorEmail">Sponsor Email</label>
						<input type="text" id="sponsorEmail" name="sponsorEmail" class="inputShade noPlaceHolder" value="<?= empty($supervisorInfo->email) ? '' : h($supervisorInfo->email) ?>">
					</div>

				</div>

				<h3 class="headerTab">Information Requested</h3>
				<div class="clear"></div>
				<div class="resultItem">
					<div class="irLower">
						<div class="checkBoxes">
							<div class="checkCol">
						<?php
							//for($i=0; $i<sizeof($siblingTerms->termReference)-1; $i++){
							$i = 0;
							foreach($termDetails->aaData as $term){
								$termName = $term->termsignifier;
								$termID = $term->termrid;
								$community = $term->communityname;
								$domain = $term->domainname;
								$termID = $term->termrid;
								$termDef = addslashes(strip_tags($term->Attr00000000000000000000000000000202longExpr));
								if($i>0 && $i%2==0){
									echo '</div>';
									echo '<div class="checkCol">';
								}
								echo '    <input type="checkbox" onclick="toggleDataNeeded(this)" value="'.$termID.'" name="terms[]" id="'.$termID.'" checked="checked">'.
									'    <label for="'.$termID.'">'.$community.' > '.$domain.' > '.$termName.'</label><div onmouseover="showTermDef(this)" onmouseout="hideTermDef()" data-definition="'.$termDef.'" class="info"><img src="/img/iconInfo.png"></div>';
								if($i%2==0){
									echo '<br/>';
								}
								$i++;
							}
							foreach ($apiAllTerms as $termId) {
								echo "<input type='hidden' value='{$termId}' name='apiTerms[]'/>";
							}
						?>
							</div>
							<div class="clear"></div>
						</div>
					</div>
				</div>

				<?php
					$arrNonDisplay = array(
						"requesterName",
						"requesterEmail",
						"requesterPhone",
						"requesterRole",
						"requesterPersonId",
						"requestingOrganization",
						"sponsorName",
						"sponsorRole",
						"sponsorEmail",
						"sponsorPhone",
						Configure::read('Collibra.isaWorkflow.requiredElementsString'),
						Configure::read('Collibra.isaWorkflow.additionalElementsString')
					);
					foreach($formFields->formProperties as $field){
						if(in_array($field->id, $arrNonDisplay)){
							continue;
						}
						echo '<label class="headerTab" for="'.$field->id.'">'.$field->name.'</label>'.
							'<div class="clear"></div>'.
							'<div class="taBox">';

						$val = empty($preFilled[$field->id]) ? '' : h($preFilled[$field->id]);
						if($field->type == 'textarea'){
							echo '<textarea name="'.$field->id.'" id="'.$field->id.'"  class="inputShade noPlaceHolder">'.$val.'</textarea>';
						}elseif($field->type == 'user'){
							echo '<select name="'.$field->id.'" id="'.$field->id.'">';
							foreach($sponsors->user as $sponsor){
								if($sponsor->enabled==1){
									echo '<option value="'.$sponsor->resourceId.'">'.$sponsor->firstName.' '.$sponsor->lastName.'</option>';
								}
							}
							echo '</select>';
						}else{
							echo '<input type="text" name="'.$field->id.'" id="'.$field->id.'" value="'.$val.'" class="inputShade full noPlaceHolder" />';
						}

						echo '</div>';
					}
				?>
				<label for="requestSubmit" id="mobileReqd">*All Fields Required</label>
				<div class="clear"></div>
			</div>
		</div>
		<div class="clear"></div>
	</div>
	<div id="formSubmit" class="innerLower">
		<input type="submit" value="Submit Request" id="requestSubmit" name="requestSubmit" class="grow">
		<label for="requestSubmit" class="mobileHide">*All Fields Required</label>
		<div class="clear"></div>
	</div>
	<div class="clear"></div>
</form>

<!-- Quick links -->
<?php echo $this->element('quick_links'); ?>
