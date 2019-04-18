<div class="people-main container">
	<div class="tab-switcher">
		<button class="tab-button" id="search-btn" onclick="switchTabs(0)" style="background:#32d8ca;">Search</button>
		<button class="tab-button" id="known-btn" onclick="switchTabs(1);">Known</button>
	</div>
	
	<div class="search-people tab">
		<form name="peopleSearchForm" onSubmit="searchPeople(); return false;">
			<input type="text" name="name" placeholder= "Search people">
			<input id="submit" type="submit" value="Search">
		</form>
		
		<div id="result" class="people-results">
			
		</div>
	</div>
	
	<div id="known-people-result" class="people-results tab" style="display: none">
		
	</div>
	
</div>

<script type="text/javascript" src="/app/js/app/peopleSv.js"></script>
<script type="text/javascript" src="/app/js/app/glob.js"></script>
<script type="text/javascript" src="/app/js/app/header.js"></script>
<script type="text/javascript" src="/app/js/app/people.js"></script>