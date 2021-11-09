Ext.onReady(function() {
	simpleQueue.config.connector_url = OfficeConfig.actionUrl;

	var grid = new simpleQueue.panel.Home();
	grid.render('office-simplequeue-wrapper');

	var preloader = document.getElementById('office-preloader');
	if (preloader) {
		preloader.parentNode.removeChild(preloader);
	}
});