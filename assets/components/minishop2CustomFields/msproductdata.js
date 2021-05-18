miniShop2.plugin.customfields = {
    // Изменение полей для панели товара
    getFields: function () {
        return {
            material: {
                xtype: 'minishop2-combo-options',
                description: '<b>[[+material]]</b><br />' + _('ms2_product_material_help')
            },
            video: {
                xtype: 'minishop2-combo-browser',
                description: '<b>[[+video]]</b><br />' + _('ms2_product_video_help')
            },
            // color: {
            //     xtype: 'textfield',
            //     description: '<b>[[+color]]</b><br />' + _('ms2_product_color_help')
            // },
            soon: {
                xtype: 'xcheckbox',
                inputValue: 1,
                // checked: parseInt(config.record.soon),
                description: '<b>[[+soon]]</b><br />' + _('ms2_product_soon_help')
            },
            sale: {
                xtype: 'xcheckbox',
                inputValue: 1,
                // checked: parseInt(config.record.sale),
                description: '<b>[[+sale]]</b><br />' + _('ms2_product_sale_help')
            },
            feed: {
                xtype: 'xcheckbox',
                inputValue: 1,
                // checked: parseInt(config.record.feed),
                description: '<b>[[+feed]]</b><br />' + _('ms2_product_feed_help')
            },
			sortindex: {
				xtype: 'numberfield'
				,fieldLabel: _('ms2_product_sortindex')
				,description: '<b>[[+sortindex]]</b><br />'+_('ms2_product_sortindex_help')
				,name: 'sortindex'
				,value: ''
				,allowBlank:true
				,anchor: '100%'
			}
        }
    },
    // Изменение колонок таблицы товаров в категории
    getColumns: function () {
        return {
            material: {
                width: 50,
                sortable: false,
                editor: {
                    xtype: 'minishop2-combo-options',
                    name: 'material'
                }
            },
            video: {
                width: 50,
                sortable: false,
                editor: {
                    xtype: 'minishop2-combo-browser',
                    name: 'video'
                }
            },
            // color: {
            //     width: 50,
            //     sortable: false,
            //     editor: {
            //         xtype: 'textfield',
            //         name: 'color'
            //     }
            // },
            soon: {
                width: 50,
                sortable: false,
                editor: {
                    xtype: 'combo-boolean',
                    renderer: 'boolean',
                    name: 'soon'
                }
            },
            sale: {
                width: 50,
                sortable: false,
                editor: {
                    xtype: 'combo-boolean',
                    renderer: 'boolean',
                    name: 'sale'
                }
            },
            feed: {
                width: 50,
                sortable: false,
                editor: {
                    xtype: 'combo-boolean',
                    renderer: 'boolean',
                    name: 'feed'
                }
            },
			sortindex: {
				header: _('ms2_product_sortindex')
				,dataIndex: 'sortindex'
				,name: 'sortindex'
				,editor: {
					xtype: 'numberfield'
				}
			}
        }
    }
};