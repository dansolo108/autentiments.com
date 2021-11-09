simpleQueue.panel.Home = function (config) {
	config = config || {};
	Ext.apply(config, {
		baseCls: 'modx-formpanel',
		layout: 'anchor',
		/*
		 stateful: true,
		 stateId: 'simplequeue-panel-home',
		 stateEvents: ['tabchange'],
		 getState:function() {return {activeTab:this.items.indexOf(this.getActiveTab())};},
		 */
		hideMode: 'offsets',
		items: [{
			html: '<h2>' + _('simplequeue') + '</h2>',
			cls: '',
			style: {margin: '15px 0'}
		}, {
			xtype: 'modx-tabs',
			defaults: {border: false, autoHeight: true},
			border: true,
			hideMode: 'offsets',
			items: [{
				title: _('simplequeue_items'),
				layout: 'anchor',
				items: [{
					html: _('simplequeue_intro_msg'),
					cls: 'panel-desc',
				}, {
					xtype: 'simplequeue-grid-items',
					cls: 'main-wrapper',
				}]
			}]
		}]
	});
	simpleQueue.panel.Home.superclass.constructor.call(this, config);
};
Ext.extend(simpleQueue.panel.Home, MODx.Panel);
Ext.reg('simplequeue-panel-home', simpleQueue.panel.Home);
