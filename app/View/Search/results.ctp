<?php
	$this->Html->css('secondary', null, array('inline' => false));
	$this->Html->css('search', null, array('inline' => false));
?>
<script>
	$(document).ready(function() {
		$("#searchLink").addClass('active');

		$('.detailsTab').click(function() {
			$(this).siblings('.resultContent').children('.resultBody').slideToggle();
			$(this).toggleClass('active');
			$(this).parent().find('.mainRequestBtn, .unrequestable').toggle();
			$(this).parent().find('.detailsRequestBtn').toggle();
			
			if($(this).hasClass('active')){
				if($(this).parent().find('.checkBoxes').html() == ''){
					var thisElem = $(this);
					var rid = $(this).attr('data-rid');
					var vocabRid = $(this).attr('data-vocabRid');
					
					// load term definition
					$.get("/search/getTermDefinition",{vocabRid:vocabRid, searchInput:'<?php echo $searchInput ?>'})
						.done(function(data) {
							var termDesc = '<p>'+data+'</p><h5>Also included in this selection (check all that apply to your request).</h5>';
							
							// load term sibling checkboxes
							$.get( "/search/getFullVocab",{rid:rid})
								.done(function( data ) {
									thisElem.parent().find('.term-desc').html(termDesc);
									thisElem.parent().find('.resultBodyLoading').hide()
									thisElem.parent().find('.checkBoxes').html(data);
									getCurrentRequestTerms();
									thisElem.parent().find('.checkBoxes input').click(function(){
										if(thisElem.parent().find('.requestAccess').hasClass('inactive')){
											thisElem.parent().find('.requestAccess').attr('value','Update Request').removeClass('inactive');
										}
									});
							});
					});
				}else{
					getCurrentRequestTerms();
				}
			}
		});

		pageResults(1);
		generatePageNav();
	});

	var resultsPerPage = 10;
	function generatePageNav(){
		var resultNum = $('.resultItem').size();
		var totalPages = Math.ceil(resultNum/resultsPerPage);
		if(totalPages>1){
			for(i=0; i<totalPages; i++){
				$('.page-nav').append('<li><a href="javascript:pageResults('+(i+1)+')">'+(i+1)+'</a></li>');
			}
		}
	}
	function pageResults(pg){
		if(!pg) pg = 1;
		var startIdx = (pg-1) * resultsPerPage;
		var endIdx = startIdx + resultsPerPage;
		var resultNum = $('.resultItem').size();
		$('.resultItem').hide();
		$('.resultItem').each(function(i){
			if(i>=startIdx & i<endIdx){
				$(this).show();
			}
		});
		// scroll user to top of results
		if($(window).scrollTop() > $("#searchResults").offset().top){
			$('html, body').delay(200).animate({scrollTop: $("#searchResults").offset().top}, 500);
		}
	}
</script>

<!-- Background image div -->
<div id="searchBg" class="searchBg">
</div>

<!-- Request list -->
<div id="searchBody" class="innerLower">
	<div id="searchTop">
		<h1 class="headerTab">Search Information</h1>
		<div class="clear"></div>
		<div id="stLower" class="whiteBox">
			<form action="#" onsubmit="document.location='/search/results/'+this.searchInput.value.replace('+','&'); return false;" method="post">
				<input id="searchInput" type="text" class="inputShade" value="<?php echo $searchInput ?>" placeholder="Search keyword, topic, or phrase" maxlength="50" autocomplete="off" >
				<?php echo $this->element('auto_complete'); ?>
				<input type="submit" value="Search" class="inputButton">
			</form>
			<div class="clear"></div>
		</div>
	</div>

	<!--<a href="/search/catalog" id="catalogLink" class="grow"><img src="/img/catalogLink2.png" alt="See full catealog"></a>-->
	<div class="clear"></div>

	<div id="searchResults">
		<h2 class="headerTab" >Results</h2>
		<div class="clear"></div>
		<div id="srLower" class="whiteBox">
<?php
	if(!empty($communities->communityReference)){
?>
			<div id="searchFilters">
				<label for="filerBy">Filter By:</label>
				<select name="filterBy" id="filerBy" class="inputShade" onchange="document.location='/search/results/<?php echo $searchInput ?>?s=<?php echo $sort ?>&f='+this.value">
					<option value="">All</option>
<?php
		for($i=0; $i<sizeof($communities->communityReference); $i++){
			$community = $communities->communityReference[$i];
			$selected = '';
			if($filter == $community->resourceId){
				$selected = 'selected';
			}
			echo '<option value="'.$community->resourceId.'" '.$selected.'>'.$community->name.'</option>';
		}
?>
				</select>
				<label for="filerBy">Sort By:</label>
				<select name="filterBy" id="filerBy" class="inputShade" onchange="document.location='/search/results/<?php echo $searchInput ?>?s='+this.value+'&f=<?php echo $filter ?>'">
					<option value="0" <?php if($sort==0) echo 'selected'; ?>>Relevance</option>
					<option value="1" <?php if($sort==1) echo 'selected'; ?>>Alphabetical</option>
					<option value="2" <?php if($sort==2) echo 'selected'; ?>>Last Modified</option>
					<option value="3" <?php if($sort==3) echo 'selected'; ?>>Classification</option>
				</select>
			</div>
			
<?php
	}

	if(empty($terms->aaData)){
		echo '<h1>No results found.</h1><h3>Please try a different search term.</h3>';
	}else{
		for($i=0; $i<sizeof($terms->aaData); $i++){
			$term = $terms->aaData[$i];
			$notRequestable = $term->requestable == 'false';
			// don't display non-requestable terms
			if(!$notRequestable){
				$lastModified = $term->lastModified/1000;
				$lastModified = date('m/d/Y', $lastModified);
				$classification = $term->classification;
				$classificationTitle = '';
				$txtColor = 'blueText';
				switch($classification){
					case '1 - Public':
						$classificationTitle = 'Public';
						$classification = 'public';
						$txtColor = 'greenText';
						break;
					case '2 - Internal':
						$classificationTitle = 'Internal';
						$classification = 'internal';
						$txtColor = 'blueText';
						break;
					case '3 - Confidential':
						$classificationTitle = 'Confidential';
						$classification = 'classified';
						$txtColor = 'orangeText';
						break;
					case '4 - Highly Confidential':
						$classificationTitle = 'Highly Confidential';
						$classification = 'highlyClassified';
						$txtColor = 'redText';
						break;
				}

				$termRequestID = $term->termrid;
				$termRequestTitle = $term->termsignifier;
				$synonymFor = '';
				if(sizeof($term->synonym_for)!=0){
					$synonymFor = $term->synonym_for[0]->synonymname;
					$termRequestTitle = $synonymFor;
					$termRequestID = $term->synonym_for[0]->synonymid;
				}
?>
			<div id="term<?php echo $term->termrid; ?>" class="resultItem">
				<div class="<?php echo $classification ?>" title="<?php echo $classificationTitle ?>"></div>
				<form action="/request/index/<?php echo $term->termrid; ?>" method="post">
					<h4><?php echo $term->termsignifier; ?></h4>
					<h5 class="<?php echo $txtColor ?>"><?php echo $term->communityname.' <span class="arrow-separator">&gt;</span> <a href="/search/listTerms/'.$term->domainrid.'">'.$term->domainname.'</a>' ?></h5>
					<div class="resultContent">
						<ul>
						   <?php
								if(sizeof($term->Role00000000000000000000000000005016)>0){
									$stewardName = $term->Role00000000000000000000000000005016[0]->userRole00000000000000000000000000005016fn.' '.$term->Role00000000000000000000000000005016[0]->userRole00000000000000000000000000005016ln;
							?>
							<li><span class="listLabel">Data Steward:&nbsp;</span><?php echo $stewardName; ?></li>
							<?php
								}
							?>
							<li><span class="listLabel">Last Updated:&nbsp;</span><?php echo $lastModified; ?></li>
							<li><span class="listLabel">Classification: </span><span class="classificationTitle"><?php echo $classificationTitle ?></span></li>
							<?php
								if($synonymFor != ''){
									echo '<li class="new-line synonym"><span class="listLabel">Synonym For: </span><span class="classificationTitle">'.$synonymFor.'</span></li>';
								}
							?>
						</ul>
						<div class="resultBody">
							<div class="term-desc"></div>
							<img class="resultBodyLoading" src="/img/dataLoading.gif" alt="Loading...">
							<div class="checkBoxes"></div>
							<div class="clear"></div>
						</div>
					</div>
					<a href="javascript:addQL('<?php echo $term->termsignifier; ?>', '<?php echo $term->termrid; ?>')" class="addQuickLink grow">
					<?php
						if(isset($term->saved) && $term->saved == '1'){
							echo '<img src="/img/iconStarOrange.gif" alt="Quick Link">';
						}else{
							echo '<img src="/img/iconStarBlue.gif" alt="Quick Link">';
						}
					?>
							
					</a>
					<?php
						if(!$notRequestable || Configure::read('allowUnrequestableTerms')){
					?>
					<input type="button" onclick="addToQueue(this, false)" data-title="<?php echo $termRequestTitle; ?>" data-rid="<?php echo $termRequestID ?>" data-vocabID="<?php echo $term->commrid ?>" class="requestAccess grow mainRequestBtn" value="Add To Request" />
					<?php
						}else{
					?>
					<div class="unrequestable">Not Requestable</div>
					<?php
						}
					?>
					<input type="button" onclick="addToQueue(this, true)" class="requestAccess grow detailsRequestBtn" value="Add To Request" />
					<a class="detailsTab" data-rid="<?php echo $term->domainrid; ?>" data-vocabRid="<?php echo $termRequestID ?>"><span class="detailsLess">Fewer</span><span class="detailsMore">More</span>&nbsp;Details</a>
				</form>
			</div>
<?php
			}
		}
	}
?>
			<div class="clear"></div>
			<ul class="page-nav"></ul>
			<div class="clear"></div>
		</div>
	</div>
</div>

<!-- Quick links -->
<?php echo $this->element('quick_links'); ?>
