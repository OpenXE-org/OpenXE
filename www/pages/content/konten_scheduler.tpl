<div class="filter-block filter-inline">
	<ul class="filter-list boxes">
		[ELEMENT]
	</ul>
</div>

<style type="text/css">
	ul.filter-list.boxes > li {
		width: 20px;
	}

	.switch .box:before {
		position: absolute;
		top: 3px;
		left: 3px;
		content: "";
		display: block;
		width: 10px;
		height: 10px;
		background-color: #FFF;
		-webkit-transition: 0.35s;
		transition: 0.35s;
	}

	.switch input + .box {
		background-color: #eee;
		color:#000;
	}

	.switch input:checked + .box {
		background-color: var(--turquoise);
		color:#fff;
	}
	.switch .box {
			border-radius: 2px;
		width: 20px;
		height: 17px;
		font-weight: bold;
		display:inline-block;
		font-size: 15px;
		line-height: 15px;
		padding-top:3px;
		text-align: center;
	}
</style>