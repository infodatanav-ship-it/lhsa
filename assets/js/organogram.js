google.charts.load('current', {packages:["orgchart"]});
google.charts.setOnLoadCallback(drawChart);

function drawChart() {
	var data = new google.visualization.DataTable();
	data.addColumn('string', 'Name');
	data.addColumn('string', 'Manager');
	data.addColumn('string', 'ToolTip');

	// For each orgchart box, provide the name, manager, and tooltip to show.
	data.addRows([
		[
			{
				'v':'Stan', 
				'f':'<div style="color:red; font-style:italic">CHAIRMAN</div>Stan Amos'
			}, 
			'', 
			'The CHAIRMAN'
		],
		[
			{
				'v':'Broidy', 
				'f':'<div style="color:red; font-style:italic">SECRETARY</div><br />Broidy'
			}, 
			'Stan', 
			'SECRETARY'
		],
		[
			{
				'v':'Parrott', 
				'f':'<div style="color:red; font-style:italic">TREASURER</div><br />Parrott'
			}, 
			'Stan', 
			'TREASURER'
		],
		[
			{
				'v':'Wilfred', 
				'f':'<div style="color:red; font-style:italic">VICE-CHAIRMAN</div><br />W. Daniels'
			}, 
			'Stan', 
			'VICE-CHAIRMAN'
		],
		[
			{
				'v':'Lana', 
				'f':'<div style="color:red; font-style:italic">VICE-SECRETARY</div>Lana Carelse'
			}, 
			'Stan', 
			'VICE-SECRETARY'
		],
		[
			{
				'v':'Boraine', 
				'f':'COMMISSION_1<div style="color:red; font-style:italic">HISTORICAL INFO & STATS</div><br />V. Boraine'
			}, 
			'Broidy', 
			'HISTORICAL INFO & STATS'
		],
		[
			{
				'v':'Petersen', 
				'f':'COMMISSION_2<div style="color:red; font-style:italic">FUNDRAISING & FUNCTIONS</div><br />J. Petersen'
			}, 
			'Parrott', 
			'FUNDRAISING & FUNCTIONS'
		],
		[
			{
				'v':'Hector', 
				'f':'COMMISSION_3<div style="color:red; font-style:italic">STAKEHOLDERS INTERACTION</div><br />S. Hector'
			}, 
			'Wilfred', 
			'STAKEHOLDERS INTERACTION'
		],
		[
			{
				'v':'Samuels', 
				'f':'COMMISSION_4<div style="color:red; font-style:italic">SCHOOL PREMISES</div><br />W. Samuels'
			}, 
			'Lana', 
			'SCHOOL PREMISES'
		]
	]);

	// Create the chart.
	var chart = new google.visualization.OrgChart(document.getElementById('chart_div'));
	// Draw the chart, setting the allowHtml option to true for the tooltips.
	chart.draw(data, {'allowHtml':true});
}