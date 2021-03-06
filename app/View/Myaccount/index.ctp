<?php
	$this->Html->css('secondary', null, array('inline' => false));
	$this->Html->css('account', null, array('inline' => false));
?>
<script>
	$(window).resize(colSize);

	function colSize() {
		if($(window).width() > 650) {
			//$('.riLeft').css('width', '100%').css('width', '-=270px');
		}
		else {
			//$('.riLeft').css('width', '100%');
		}
	}

	$(document).ready(function() {
		colSize();
		$('.detailsTab').click(function() {
			var rid = $(this).attr('data-rid');
			$('#'+rid).slideToggle();
			$(this).toggleClass('active');
		});

		$('.approver .user-icon').on('mouseover click', function(){
			$(this).parent().find('.info').css('z-index', 20).toggle();
		});
		$('.approver .user-icon').mouseout(function(){
			$(this).parent().find('.info').hide();
		});
	});
</script>

<!-- Background image div -->
<div id="accountBody" class="libOnebg">
</div>

<!-- Request list -->
<div id="accountMid" class="innerLower">
	<div id="accountTop">
		<h1 class="headerTab">My Account</h1>
		<div id="atLower" class="whiteBox">
			<h2><?php echo $psName ?></h2>
			<div id="aiDept" class="accountInfo"><span class="aiLabel">Department:&nbsp;</span><?php echo $psDepartment ?></div>
			<div id="aiRole" class="accountInfo"><span class="aiLabel">Role:&nbsp;</span><?php echo $psRole ?></div>
			<a class="logout" href="/myaccount/logout">Logout</a>
			<div class="clear"></div>
		</div>
	</div>
	<div class="accountTabs">
		<a href="/myaccount"><div id="tabLeft" class="atTab <?php if($page=='current') echo 'active' ?>">Pending Requests</div></a>
		<a href="/myaccount/?s=2"><div id="tabright" class="atTab <?php if($page=='past') echo 'active' ?>">Completed Requests</div></a>
	</div>
	<div class="clear"></div>
	<div id="accountMain" class="whiteBox">
<?php
	if(sizeof($requests)==0){
		echo '<div class="requestItem"><div class="riLeft"><h4 class="riTitle">No Requests Found</h4></div></div>';
	}else{
		foreach($requests as $req){
			echo '<div class="requestItem">'.
				'    <div class="riLeft">'.
				'        <h4 class="riTitle">'.$req->signifier.'</h4>'.
				'        <p class="riDate"><span>Date Created:&nbsp;</span>'.date('n/j/Y', ($req->createdOn)/1000).'</p>'.
				'        <p class="riDate"><strong>Requested Data:</strong><br>';
			$termCount = 0;
			foreach($req->terms->aaData as $term){
				echo $term->termsignifier;
				$termCount++;
				if($termCount < sizeof($req->terms->aaData)){
					echo ',&nbsp;&nbsp;';
				}
			}
			echo '</p>';
			if($page=='current'){
				echo '<img src="/img/iconReview.png" alt="Request in review">';
			}else{
				echo '<img src="/img/iconApproved.png" alt="Approved requests">';
			}
			echo '</div>';

			// display approvers and their info
			////////////////////////////////////////
			echo '<div class="riRight">'.
				'<h4 class="riTitle">Reviewers of This Request</h4>';
			foreach($req->approvals->aaData as $approval){
				$approverName = $approval->Attr4331ec0988a248e6b0969ece6648aff3;
				if($approverName != ''){
					$approverEmail = $approval->Attr0cbbfd32fc9747cebef1a89ae4e77ee8;
					$approverPhone = $approval->Attr9a18e247c09040c3896eab97335ae759;
					$approverStatus = strtolower($approval->statusname);

					echo '<div class="approver '.$approverStatus.'">'.
						'	<div class="user-icon"></div>'.
						'	<div class="info">'.
						'		<div class="contactName">'.$approverName.'</div>'.
						'		<div class="contactNumber"><div class="icon"></div><a href="tel:'.$approverPhone.'">'.$approverPhone.'</a></div>'.
						'		<div class="contactEmail"><div class="icon"></div><a href="mailto:'.$approverEmail.'">'.$approverEmail.'</a></div>'.
						'		<div class="status"><div class="icon '.$approverStatus.'"></div><p>'.ucfirst($approverStatus).'</p></div>'.
						'	</div>'.
						'</div>';
				}
			}
			echo '</div>';
			////////////////////////////////////////

			// show request details
			////////////////////////////////////////
			echo '	<div class="detailsBody" id="'.$req->resourceId.'">';
?>

			<h3 class="headerTab">Requester</h3>
			<div class="clear"></div>
			<div class="data-col">
				<h5>Name:</h5>
				<p><?php echo $req->attributeReferences->attributeReference['Requester Name']->value ?>
			</div>
			<div class="data-col">
				<h5>Phone Number:</h5>
				<p><?php echo $req->attributeReferences->attributeReference['Requester Phone']->value ?>
			</div>
			<div class="data-col">
				<h5>Email:</h5>
				<p><?php echo $req->attributeReferences->attributeReference['Requester Email']->value ?>
			</div>
			<div class="data-col">
				<h5>Role:</h5>
				<p><?php echo $req->attributeReferences->attributeReference['Requester Role']->value ?>
			</div>
			<div class="data-col">
				<h5>Requesting Organization:</h5>
				<p><?php echo $req->attributeReferences->attributeReference['Requesting Organization']->value ?>
			</div>
			<div class="clear"></div>

			<h3 class="headerTab">Sponsor</h3>
			<div class="clear"></div>
			<div class="data-col">
				<h5>Sponsor Name:</h5>
				<p><?php echo $req->attributeReferences->attributeReference['Sponsor Name']->value ?>
			</div>
			<div class="data-col">
				<h5>Sponsor Role:</h5>
				<p><?php echo $req->attributeReferences->attributeReference['Sponsor Role']->value ?>
			</div>
			<div class="data-col">
				<h5>Sponsor Email:</h5>
				<p><?php echo $req->attributeReferences->attributeReference['Sponsor Email']->value ?>
			</div>
			<div class="data-col">
				<h5>Sponsor Phone:</h5>
				<p><?php echo $req->attributeReferences->attributeReference['Sponsor Phone']->value ?>
			</div>
			<div class="clear"></div>
<?php
			$arrNonDisplay = array(
				"Requester Name", 
				"Requester Email",
				"Requester Phone",
				"Information Elements",
				"Requester Role",
				"Requester PersonId",
				"Requesting Organization",
				"Sponsor Name",
				"Sponsor Role",
				"Sponsor Email",
				"Sponsor Phone",
				"Requester Person ID",
				"Request Date"
			);
			foreach($req->attributeReferences->attributeReference as $attrRef){
				//print_r($attrRef);exit;
				if(!in_array($attrRef->labelReference->signifier, $arrNonDisplay)){
					echo '<h3 class="headerTab">'.$attrRef->labelReference->signifier.'</h3><div class="clear"></div>'.
						'<p>'.$attrRef->value.'</p>';
				}
			}
			echo '</div>';
			////////////////////////////////////////

			echo '	<a class="detailsTab" data-rid="'.$req->resourceId.'"><span class="detailsLess">Fewer</span><span class="detailsMore">More</span>&nbsp;Details</a>'.
				'</div>';

		}
	}
?>
	</div>
</div>

<!-- Quick links -->
<?php echo $this->element('quick_links'); ?>
