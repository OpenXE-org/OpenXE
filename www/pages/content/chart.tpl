    <script type="text/javascript" src="./js/chart/includes/excanvas.js"></script>
		<script type="text/javascript" src="./js/chart/includes/chart.js"></script>
		<script type="text/javascript" src="./js/chart/includes/canvaschartpainter.js"></script>
		<link rel="stylesheet" type="text/css" href="./js/chart/includes/canvaschart.css" />
		<div class="webfx-main-body">
		<div id="chart3" class="chart" style="width: [CHART_WIDTH]px; height: [CHART_HEIGHT]px;"></div>
		</div>

    <script type="text/javascript">
        var c = new Chart(document.getElementById('chart3'));
        c.setDefaultType(CHART_LINE);
        c.setGridDensity([GRIDX], [GRIDY]);
	c.setVerticalRange([LIMITMIN],[LIMITMAX]);
        c.setHorizontalLabels([LABELS]);
        c.setShowLegend(false);
	[CHARTS]
        c.draw();

    </script>

		<!-- End WebFX Layout Includes -->
	</body>
</html>
